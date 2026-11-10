<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Section;
use App\Services\ContentSchema;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{
    public function __construct(protected ContentSchema $schema) {}

    /**
     * Shape a section for the page builder.
     *
     * @return array<string, mixed>
     */
    public static function present(Section $section): array
    {
        return [
            'id' => $section->id,
            'page_id' => $section->page_id,
            'type' => $section->renderType(),
            'name' => $section->name,
            'data' => (object) ($section->shared_id ? [] : ($section->data ?? [])),
            'is_active' => $section->is_active,
            'shared' => $section->shared ? [
                'id' => $section->shared->id,
                'name' => $section->shared->name,
                'data' => (object) ($section->shared->data ?? []),
                'is_active' => $section->shared->is_active,
                'usages_count' => $section->shared->usages_count ?? $section->shared->usages()->count(),
            ] : null,
        ];
    }

    /**
     * Add a new section (or a link to a shared section) to a page.
     */
    public function store(Request $request, Page $page): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required_without:shared_id', 'nullable', Rule::in(array_keys($this->schema->sectionTypes()))],
            'shared_id' => ['nullable', Rule::exists('sections', 'id')->whereNull('page_id')],
            'after_id' => ['nullable', 'integer'],
        ]);

        DB::transaction(function () use ($validated, $page) {
            $position = $this->insertPosition($page, $validated['after_id'] ?? null);

            if (! empty($validated['shared_id'])) {
                $shared = Section::query()->findOrFail($validated['shared_id']);
                $page->sections()->create(['shared_id' => $shared->id, 'type' => $shared->type, 'name' => $shared->name, 'sort_order' => $position]);

                return;
            }

            $page->sections()->create([
                'type' => $validated['type'],
                'name' => $this->schema->sectionType($validated['type'])['label'],
                'data' => [],
                'sort_order' => $position,
            ]);
        });

        return back()->with('success', 'Section added.');
    }

    /**
     * Save a section's content (works for page and shared sections).
     */
    public function update(Request $request, Section $section): RedirectResponse
    {
        $target = $section->shared_id ? $section->shared : $section;

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'data' => ['present', 'array'],
        ]);

        $target->update([
            'name' => $validated['name'] ?: $target->name,
            'data' => $this->schema->cleanSection($target->type, $validated['data']),
        ]);

        if ($section->shared_id && $validated['name']) {
            $section->update(['name' => $validated['name']]);
        }

        return back()->with('success', $section->shared_id ? 'Shared section saved — updated on every page that uses it.' : 'Section saved.');
    }

    public function toggle(Section $section): RedirectResponse
    {
        $section->update(['is_active' => ! $section->is_active]);

        return back()->with('success', $section->is_active ? 'Section shown.' : 'Section hidden.');
    }

    public function duplicate(Section $section): RedirectResponse
    {
        DB::transaction(function () use ($section) {
            $copy = $section->replicate(['sort_order']);
            $copy->name = trim(($section->name ?? '').' (copy)');

            if ($section->page_id) {
                $copy->sort_order = $this->insertPosition($section->page, $section->id);
            }

            $copy->save();
        });

        return back()->with('success', 'Section duplicated.');
    }

    /**
     * Move a page section's content into the shared library and link it.
     */
    public function share(Section $section): RedirectResponse
    {
        abort_if(! $section->page_id || $section->shared_id, 422, 'Only page sections can be shared.');

        DB::transaction(function () use ($section) {
            $shared = Section::query()->create([
                'type' => $section->type,
                'name' => $section->name,
                'data' => $section->data,
                'is_active' => true,
            ]);

            $section->update(['shared_id' => $shared->id, 'data' => null]);
        });

        return back()->with('success', 'Section moved to the shared library. Add it to other pages from "Add section".');
    }

    /**
     * Replace a link to a shared section with an editable copy.
     */
    public function detach(Section $section): RedirectResponse
    {
        abort_if(! $section->shared_id, 422, 'This section is not shared.');

        $section->update(['data' => $section->shared?->data, 'type' => $section->shared?->type ?? $section->type, 'shared_id' => null]);

        return back()->with('success', 'Section detached. Changes now apply to this page only.');
    }

    public function destroy(Section $section): RedirectResponse
    {
        $section->delete();

        return back()->with('success', $section->page_id ? 'Section removed.' : 'Shared section deleted from every page.');
    }

    public function reorder(Request $request, Page $page): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', Rule::exists('sections', 'id')->where('page_id', $page->id)],
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['ids'] as $index => $id) {
                Section::query()->whereKey($id)->update(['sort_order' => $index + 1]);
            }
        });

        Page::flushContentCache();

        return back()->with('success', 'Section order saved.');
    }

    /**
     * Sort position for a new section, shifting later sections down.
     */
    protected function insertPosition(Page $page, ?int $afterId): int
    {
        $after = $afterId ? $page->sections()->whereKey($afterId)->first() : null;

        if (! $after) {
            return (int) $page->sections()->max('sort_order') + 1;
        }

        $page->sections()->where('sort_order', '>', $after->sort_order)->increment('sort_order');

        return $after->sort_order + 1;
    }
}
