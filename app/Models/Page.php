<?php

namespace App\Models;

use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Page extends Model
{
    /** @use HasFactory<PageFactory> */
    use HasFactory;

    /**
     * Slug used for the site root ("/").
     */
    public const HOME_SLUG = 'home';

    /**
     * Cache key holding the current content version; bumping it invalidates
     * every cached page.
     */
    public const CACHE_VERSION_KEY = 'cms_content_version';

    protected $fillable = [
        'title',
        'slug',
        'hero_background',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'schema_type',
        'is_active',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => self::flushContentCache());
        static::deleted(fn () => self::flushContentCache());
    }

    /**
     * @return HasMany<Section, $this>
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @param  Builder<Page>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Public path of the page ("/" for the homepage).
     */
    public function path(): string
    {
        return $this->slug === self::HOME_SLUG ? '/' : '/'.$this->slug;
    }

    /**
     * Load an active page with its visible sections for the public site.
     */
    public static function findForFrontend(string $slug): ?self
    {
        $version = Cache::get(self::CACHE_VERSION_KEY, 1);

        $cached = Cache::rememberForever("cms_page_{$slug}_v{$version}", function () use ($slug): ?array {
            $page = self::query()
                ->active()
                ->where('slug', $slug)
                ->with(['sections' => fn ($query) => $query->where('is_active', true)->with('shared')])
                ->first();

            return $page ? self::toCachePayload($page) : null;
        });

        return is_array($cached) ? self::fromCachePayload($cached) : null;
    }

    /**
     * Reduce a loaded page to plain arrays, because the cache store refuses to
     * unserialize objects (`cache.serializable_classes` is false).
     *
     * @return array{page: array<string, mixed>, sections: list<array{attributes: array<string, mixed>, shared: array<string, mixed>|null}>}
     */
    protected static function toCachePayload(self $page): array
    {
        return [
            'page' => $page->getAttributes(),
            'sections' => $page->sections->map(fn (Section $section): array => [
                'attributes' => $section->getAttributes(),
                'shared' => $section->shared?->getAttributes(),
            ])->values()->all(),
        ];
    }

    /**
     * Rebuild the page and its sections from a cached payload.
     *
     * @param  array{page: array<string, mixed>, sections: list<array{attributes: array<string, mixed>, shared: array<string, mixed>|null}>}  $payload
     */
    protected static function fromCachePayload(array $payload): self
    {
        $page = (new self)->newFromBuilder($payload['page']);

        $sections = array_map(function (array $row): Section {
            $section = (new Section)->newFromBuilder($row['attributes']);
            $section->setRelation('shared', $row['shared'] ? (new Section)->newFromBuilder($row['shared']) : null);

            return $section;
        }, $payload['sections']);

        return $page->setRelation('sections', (new Section)->newCollection($sections));
    }

    /**
     * Invalidate all cached frontend pages.
     */
    public static function flushContentCache(): void
    {
        Cache::forever(self::CACHE_VERSION_KEY, ((int) Cache::get(self::CACHE_VERSION_KEY, 1)) + 1);
    }
}
