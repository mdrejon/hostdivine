<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Section;
use App\Services\SeoService;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(protected SeoService $seo) {}

    /**
     * Render a CMS page from its ordered sections.
     */
    public function show(?string $slug = null): View
    {
        $page = Page::findForFrontend($slug ?? Page::HOME_SLUG);

        abort_if(! $page, 404);

        $sections = $page->sections->filter(fn (Section $section) => $section->isVisible())->values();

        return view('frontend.page', [
            'page' => $page,
            'sections' => $sections,
            'schemas' => $this->seo->schemas($page, $sections),
            'preloadImage' => $this->seo->preloadImage($page, $sections),
        ]);
    }
}
