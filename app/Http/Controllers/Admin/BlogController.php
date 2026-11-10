<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Content/Blog/Index', [
            'posts' => BlogPost::query()->latest('published_at')->latest('id')->get(),
            'categories' => BlogPost::query()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        BlogPost::query()->create($this->validated($request));

        return back()->with('success', 'Article created successfully.');
    }

    public function update(Request $request, BlogPost $blog): RedirectResponse
    {
        $blog->update($this->validated($request, $blog));

        return back()->with('success', 'Article updated successfully.');
    }

    public function destroy(BlogPost $blog): RedirectResponse
    {
        $blog->delete();

        return back()->with('success', 'Article deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function validated(Request $request, ?BlogPost $post = null): array
    {
        $validated = $request->validate([
            'category' => ['nullable', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('blog_posts', 'slug')->ignore($post?->id)],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'author_name' => ['required', 'string', 'max:100'],
            'read_time' => ['required', 'string', 'max:50'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['icon'] = $validated['icon'] ?: 'fa-solid fa-newspaper';

        if (($validated['is_published'] ?? false) && empty($validated['published_at'])) {
            $validated['published_at'] = $post?->published_at ?? now();
        }

        return $validated;
    }
}
