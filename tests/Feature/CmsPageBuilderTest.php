<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Section;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CmsPageBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
    }

    public function test_page_renders_its_visible_sections_in_order(): void
    {
        $page = Page::factory()->create(['slug' => 'cloud-hosting']);
        Section::factory()->for($page)->create(['sort_order' => 2, 'data' => ['title' => 'Second banner']]);
        Section::factory()->for($page)->create(['sort_order' => 1, 'data' => ['title' => 'First banner']]);
        Section::factory()->for($page)->hidden()->create(['sort_order' => 3, 'data' => ['title' => 'Hidden banner']]);

        $this->get('/cloud-hosting')
            ->assertOk()
            ->assertSeeInOrder(['First banner', 'Second banner'])
            ->assertDontSee('Hidden banner');
    }

    public function test_cached_page_renders_when_the_cache_store_serializes_values(): void
    {
        config(['cache.stores.array.serialize' => true, 'cache.serializable_classes' => false]);
        Cache::forgetDriver('array');

        $page = Page::factory()->create(['slug' => 'cached-page']);
        $shared = Section::factory()->library()->create(['data' => ['title' => 'Shared cached banner']]);
        Section::factory()->for($page)->create(['sort_order' => 1, 'data' => ['title' => 'Own cached banner']]);
        Section::factory()->for($page)->create(['sort_order' => 2, 'shared_id' => $shared->id, 'data' => null]);

        $this->get('/cached-page')->assertOk();
        $this->get('/cached-page')->assertOk()->assertSeeInOrder(['Own cached banner', 'Shared cached banner']);
    }

    public function test_unknown_and_draft_pages_return_not_found(): void
    {
        Page::factory()->draft()->create(['slug' => 'coming-soon']);

        $this->get('/coming-soon')->assertNotFound();
        $this->get('/does-not-exist')->assertNotFound();
    }

    public function test_text_content_is_escaped_and_taka_sign_is_wrapped(): void
    {
        $page = Page::factory()->create(['slug' => 'bd-prices']);
        Section::factory()->for($page)->create(['data' => ['title' => '<script>alert(1)</script> from ৳500']]);

        $this->get('/bd-prices')
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt; from <span class="tk">৳</span>500', false);
    }

    public function test_admin_edit_updates_the_public_page_and_drops_unknown_fields(): void
    {
        $page = Page::factory()->create(['slug' => 'offers']);
        $section = Section::factory()->for($page)->create();

        $this->get('/offers')->assertOk();

        $this->actingAs($this->admin)
            ->put(route('admin.sections.update', $section), [
                'name' => 'Spring offer',
                'data' => ['title' => 'Spring sale is live', 'unknown_field' => 'x', 'buttons' => [['label' => 'Buy', 'url' => '/cart', 'new_tab' => '1']]],
            ])
            ->assertSessionHas('success');

        $section->refresh();
        $this->assertSame(['title' => 'Spring sale is live', 'buttons' => [['label' => 'Buy', 'url' => '/cart', 'new_tab' => true]]], $section->data);
        $this->get('/offers')->assertSee('Spring sale is live');
    }

    public function test_shared_section_is_edited_once_and_shown_on_every_linked_page(): void
    {
        $shared = Section::factory()->library()->create(['data' => ['title' => 'Old shared title']]);
        $pages = Page::factory()->count(2)->create();

        foreach ($pages as $page) {
            $this->actingAs($this->admin)->post(route('admin.sections.store', $page), ['shared_id' => $shared->id])->assertSessionHas('success');
        }

        $link = $pages[0]->sections()->first();
        $this->actingAs($this->admin)->put(route('admin.sections.update', $link), ['name' => 'Shared CTA', 'data' => ['title' => 'New shared title']]);

        foreach ($pages as $page) {
            $this->get($page->path())->assertSee('New shared title')->assertDontSee('Old shared title');
        }
    }

    public function test_page_section_can_be_shared_and_detached_again(): void
    {
        $page = Page::factory()->create();
        $section = Section::factory()->for($page)->create(['data' => ['title' => 'Reusable banner']]);

        $this->actingAs($this->admin)->post(route('admin.sections.share', $section))->assertSessionHas('success');

        $section->refresh();
        $this->assertNotNull($section->shared_id);
        $this->assertNull($section->data);
        $this->assertSame('Reusable banner', $section->shared->data['title']);

        $this->actingAs($this->admin)->post(route('admin.sections.detach', $section))->assertSessionHas('success');

        $section->refresh();
        $this->assertNull($section->shared_id);
        $this->assertSame('Reusable banner', $section->data['title']);
    }

    public function test_sections_can_be_reordered(): void
    {
        $page = Page::factory()->create();
        $first = Section::factory()->for($page)->create(['sort_order' => 1, 'data' => ['title' => 'Alpha block']]);
        $second = Section::factory()->for($page)->create(['sort_order' => 2, 'data' => ['title' => 'Beta block']]);

        $this->actingAs($this->admin)
            ->post(route('admin.sections.reorder', $page), ['ids' => [$second->id, $first->id]])
            ->assertSessionHas('success');

        $this->get($page->path())->assertSeeInOrder(['Beta block', 'Alpha block']);
    }

    public function test_new_section_can_be_inserted_after_an_existing_one(): void
    {
        $page = Page::factory()->create();
        $first = Section::factory()->for($page)->create(['sort_order' => 1]);
        $last = Section::factory()->for($page)->create(['sort_order' => 2]);

        $this->actingAs($this->admin)
            ->post(route('admin.sections.store', $page), ['type' => 'steps', 'after_id' => $first->id])
            ->assertSessionHas('success');

        $this->assertSame([$first->id, null, $last->id], $page->sections()->get()->map(fn ($s) => $s->type === 'steps' ? null : $s->id)->all());
    }

    public function test_reserved_slugs_cannot_be_used_for_pages(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.pages.store'), ['title' => 'Admin', 'slug' => 'admin'])
            ->assertSessionHasErrors('slug');

        $this->actingAs($this->admin)
            ->post(route('admin.pages.store'), ['title' => 'Green Hosting', 'slug' => 'green-hosting', 'is_active' => true])
            ->assertRedirect();

        $this->get('/green-hosting')->assertOk()->assertSee('Green Hosting');
    }

    public function test_menu_saved_in_site_layout_renders_in_the_header_with_current_page_state(): void
    {
        $page = Page::factory()->create(['slug' => 'vps-plans']);

        $this->actingAs($this->admin)->put(route('admin.layout.update', 'menu_primary'), ['value' => [
            ['label' => 'Servers', 'layout' => 'compact-menu', 'columns' => [
                ['title' => 'Cloud', 'links' => [['label' => 'VPS Plans', 'url' => '/vps-plans', 'description' => 'KVM servers', 'bogus' => 'x']]],
            ]],
        ]])->assertSessionHas('success');

        $this->assertSame('VPS Plans', Setting::json('menu_primary')[0]['columns'][0]['links'][0]['label']);
        $this->assertArrayNotHasKey('bogus', Setting::json('menu_primary')[0]['columns'][0]['links'][0]);

        $this->get($page->path())
            ->assertSee('<li class="nav-item has-menu is-current">', false)
            ->assertSee('aria-current="page">VPS Plans<span>KVM servers</span>', false);
    }

    public function test_unknown_layout_setting_cannot_be_saved(): void
    {
        $this->actingAs($this->admin)->put(route('admin.layout.update', 'site_name'), ['value' => []])->assertNotFound();
    }

    public function test_guests_cannot_change_content(): void
    {
        $section = Section::factory()->create();

        $this->put(route('admin.sections.update', $section), ['data' => ['title' => 'Hacked']])->assertRedirect('/login');
        $this->assertNotSame('Hacked', $section->fresh()->data['title']);
    }
}
