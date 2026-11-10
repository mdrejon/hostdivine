<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * Show a single published article.
     */
    public function show(string $slug): View
    {
        $post = BlogPost::query()->published()->where('slug', $slug)->firstOrFail();

        $recentPosts = BlogPost::query()->published()->whereKeyNot($post->id)->take(3)->get();

        return view('frontend.blog-post', compact('post', 'recentPosts'));
    }
}
