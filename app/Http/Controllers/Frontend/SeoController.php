<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SeoService;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function __construct(protected SeoService $seo) {}

    public function sitemap(): Response
    {
        return response($this->seo->sitemapXml(), 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    public function robots(): Response
    {
        $robots = Setting::get('robots_txt') ?: "User-agent: *\nAllow: /\nDisallow: /admin/\n\nSitemap: ".url('/sitemap.xml');

        return response($robots, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }
}
