<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Services\ContentSchema;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Library of sections that are edited once and reused on many pages.
 */
class SharedSectionController extends Controller
{
    public function __construct(protected ContentSchema $schema) {}

    public function index(): Response
    {
        $sections = Section::query()->library()->with('usages.page:id,title,slug')->orderBy('name')->get();

        return Inertia::render('Admin/SharedSections/Index', [
            'sections' => $sections->map(fn (Section $section) => [
                'id' => $section->id,
                'type' => $section->type,
                'name' => $section->name,
                'data' => (object) ($section->data ?? []),
                'is_active' => $section->is_active,
                'pages' => $section->usages->map(fn (Section $usage) => ['id' => $usage->page?->id, 'title' => $usage->page?->title])->filter(fn ($page) => $page['id'])->unique('id')->values(),
            ]),
            'types' => $this->schema->sectionTypes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(array_keys($this->schema->sectionTypes()))],
            'name' => ['required', 'string', 'max:255'],
        ]);

        Section::query()->create($validated + ['data' => [], 'is_active' => true]);

        return back()->with('success', 'Shared section created. Edit its content, then add it to pages.');
    }
}
