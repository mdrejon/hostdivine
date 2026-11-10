<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Section;
use App\Services\ContentSchema;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function __construct(protected ContentSchema $schema) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Pages/Index', [
            'pages' => Page::query()->withCount('sections')->orderBy('sort_order')->orderBy('title')->get()
                ->map(fn (Page $page) => [
                    'id' => $page->id,
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'path' => $page->path(),
                    'url' => url($page->path()),
                    'is_active' => $page->is_active,
                    'sections_count' => $page->sections_count,
                    'updated_at' => $page->updated_at?->diffForHumans(),
                ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePage($request);
        $validated['sort_order'] = (int) Page::query()->max('sort_order') + 1;

        $page = Page::query()->create($validated);

        $page->sections()->create([
            'type' => 'page_hero',
            'name' => 'Page hero',
            'data' => ['title' => $page->title, 'breadcrumb_current' => $page->title],
            'sort_order' => 1,
        ]);

        return redirect()->route('admin.pages.edit', $page)->with('success', 'Page created. Add sections below.');
    }

    public function edit(Page $page): Response
    {
        $page->load(['sections.shared' => fn ($query) => $query->withCount('usages')]);

        return Inertia::render('Admin/Pages/Edit', [
            'page' => $page->only(['id', 'title', 'slug', 'hero_background', 'meta_title', 'meta_description', 'meta_keywords', 'og_image', 'is_active']) + [
                'url' => url($page->path()),
                'is_home' => $page->slug === Page::HOME_SLUG,
            ],
            'sections' => $page->sections->map(fn (Section $section) => SectionController::present($section)),
            'types' => $this->schema->sectionTypes(),
            'library' => Section::query()->library()->orderBy('name')->get(['id', 'name', 'type']),
        ]);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $page->update($this->validatePage($request, $page));

        return back()->with('success', 'Page settings saved.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        abort_if($page->slug === Page::HOME_SLUG, 422, 'The homepage cannot be deleted.');

        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Page deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function validatePage(Request $request, ?Page $page = null): array
    {
        $isHome = $page?->slug === Page::HOME_SLUG;

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => $isHome ? ['nullable'] : [
                'required', 'string', 'max:120', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::notIn(['admin', 'api', 'login', 'logout', 'register', 'user', 'storage', 'build', 'assets', 'blog', 'contact', 'dashboard', 'sitemap', 'robots', Page::HOME_SLUG]),
                Rule::unique('pages', 'slug')->ignore($page?->id),
            ],
            'hero_background' => ['nullable', Rule::in(['1', '2', '3'])],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'og_image' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ], [
            'slug.regex' => 'Use lowercase letters, numbers and single dashes only.',
            'slug.not_in' => 'This URL is reserved by the system.',
        ]);

        if ($isHome) {
            unset($validated['slug']);
            $validated['is_active'] = true;
        }

        return $validated;
    }
}
