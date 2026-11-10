<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ContentSchema;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Header, menus, footer and site-wide widgets, each edited in one place.
 */
class LayoutController extends Controller
{
    public function __construct(protected ContentSchema $schema) {}

    public function index(): Response
    {
        $definitions = $this->schema->layoutSettings();

        return Inertia::render('Admin/Layout/Index', [
            'definitions' => $definitions,
            'values' => collect($definitions)->mapWithKeys(fn (array $definition, string $key) => [
                $key => Setting::json($key) ?: (empty($definition['list']) ? (object) [] : []),
            ]),
        ]);
    }

    public function update(Request $request, string $key): RedirectResponse
    {
        $definition = $this->schema->layoutSettings()[$key] ?? abort(404);

        $validated = $request->validate(['value' => ['present', 'array']]);

        Setting::set($key, $this->schema->cleanLayout($key, $validated['value']), 'layout');

        return back()->with('success', $definition['label'].' saved.');
    }
}
