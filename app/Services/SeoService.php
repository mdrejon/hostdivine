<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Page;
use App\Models\Section;
use App\Models\Setting;
use Illuminate\Support\Collection;

class SeoService
{
    /**
     * Background image used by each page-hero variant (see `main[data-hero]` in style.css).
     *
     * @var array<string, string>
     */
    public const HERO_IMAGES = [
        '1' => 'assets/img/header-home.webp',
        '2' => 'assets/img/header-home-1.webp',
        '3' => 'assets/img/header-home-2.webp',
    ];

    /**
     * Image the browser should fetch first for the page's above-the-fold hero.
     *
     * @param  Collection<int, Section>  $sections
     */
    public function preloadImage(Page $page, Collection $sections): ?string
    {
        $first = $sections->first();

        if ($first?->renderType() === 'hero_slider') {
            $slide = $first->renderData()['slides'][0] ?? [];

            return ($slide['visual'] ?? 'photo') === 'photo' ? ($slide['image'] ?? null) : null;
        }

        return self::HERO_IMAGES[$page->hero_background ?: '3'] ?? null;
    }

    /**
     * Schema.org JSON-LD blocks for a page: the organisation plus an
     * FAQPage built from the page's FAQ sections.
     *
     * @param  Collection<int, Section>  $sections
     * @return array<int, array<string, mixed>>
     */
    public function schemas(Page $page, Collection $sections): array
    {
        $schemas = [$this->organization()];

        $questions = $sections
            ->filter(fn (Section $section) => in_array($section->renderType(), ['faq', 'faq_groups'], true))
            ->flatMap(function (Section $section) {
                $data = $section->renderData();

                return $section->renderType() === 'faq'
                    ? ($data['items'] ?? [])
                    : collect($data['groups'] ?? [])->flatMap(fn (array $group) => $group['items'] ?? []);
            })
            ->filter(fn (array $item) => ! empty($item['question']))
            ->map(fn (array $item) => [
                '@type' => 'Question',
                'name' => $item['question'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => trim(strip_tags((string) ($item['answer'] ?? '')))],
            ])
            ->values();

        if ($questions->isNotEmpty()) {
            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => $questions->all(),
            ];
        }

        return $schemas;
    }

    /**
     * @return array<string, mixed>
     */
    public function organization(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => Setting::get('site_name', 'Host Divine'),
            'url' => url('/'),
            'logo' => asset(Setting::get('site_logo', 'assets/img/logo.svg')),
            'foundingDate' => Setting::get('established_year', '2004'),
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'contactType' => 'customer support',
                'email' => Setting::get('support_email', 'support@hostdivine.com'),
                'availableLanguage' => ['English', 'Bengali'],
            ],
            'sameAs' => collect(Setting::json('social_links'))
                ->pluck('url')
                ->filter(fn ($url) => is_string($url) && str_starts_with($url, 'http'))
                ->values()
                ->all(),
        ];
    }

    /**
     * XML sitemap with every active page and published blog post.
     */
    public function sitemapXml(): string
    {
        $urls = Page::query()->active()->orderBy('sort_order')->get()
            ->map(fn (Page $page) => [
                'loc' => url($page->path()),
                'lastmod' => $page->updated_at?->toAtomString(),
                'priority' => $page->slug === Page::HOME_SLUG ? '1.0' : '0.8',
            ])
            ->concat(BlogPost::query()->published()->get()->map(fn (BlogPost $post) => [
                'loc' => route('blog.show', $post->slug),
                'lastmod' => $post->updated_at?->toAtomString(),
                'priority' => '0.6',
            ]));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= '  <url><loc>'.htmlspecialchars($url['loc']).'</loc>'
                .($url['lastmod'] ? '<lastmod>'.$url['lastmod'].'</lastmod>' : '')
                .'<priority>'.$url['priority'].'</priority></url>'."\n";
        }

        return $xml.'</urlset>';
    }
}
