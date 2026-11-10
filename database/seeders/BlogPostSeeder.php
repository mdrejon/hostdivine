<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

/**
 * Seeds the starter blog articles shown on the blog page.
 */
class BlogPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = json_decode(file_get_contents(database_path('seeders/data/blog_posts.json')), true, flags: JSON_THROW_ON_ERROR);

        foreach ($posts as $post) {
            BlogPost::query()->firstOrCreate(['slug' => $post['slug']], [
                'title' => $post['title'],
                'category' => $post['category'],
                'icon' => $post['icon'],
                'excerpt' => $post['excerpt'],
                'content' => $post['content'],
                'read_time' => $post['read_time'],
                'author_name' => 'Host Divine Team',
                'is_published' => true,
                'published_at' => $post['published_at'],
            ]);
        }
    }
}
