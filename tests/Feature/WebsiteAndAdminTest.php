<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsiteAndAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_homepage_loads_successfully_with_seo_meta(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('<title>Host Divine™ Cloud Hosting</title>', false);
        $response->assertSee('Ultra-fast, secure &amp; reliable', false);
        $response->assertSee('application/ld+json', false);
    }

    public function test_every_seeded_page_loads_successfully(): void
    {
        $routes = [
            '/cpanel-hosting', '/windows-hosting', '/reseller-hosting', '/master-reseller', '/alpha-reseller',
            '/flex-reseller', '/vps-server', '/dedicated-server', '/php-hosting', '/nodejs-hosting', '/python-hosting',
            '/ruby-hosting', '/java-hosting', '/languages', '/domain-registration', '/bd-domain', '/domain-pricing',
            '/domain-transfer', '/domain-renew', '/ssl-certificates', '/business-email', '/seo-service', '/about-us',
            '/timeline', '/faq', '/blog', '/contact',
        ];

        foreach ($routes as $route) {
            $this->get($route)->assertStatus(200);
        }
    }

    public function test_domain_pricing_page_lists_every_extension_from_the_database(): void
    {
        $response = $this->get('/domain-pricing');

        $response->assertSee('543 extensions');
        $response->assertSee('data-tld=".com"', false);
    }

    public function test_blog_post_page_loads(): void
    {
        $this->get('/blog/how-to-register-a-bd-domain-with-btcl-in-2026')
            ->assertStatus(200)
            ->assertSee('How to register a .BD domain with BTCL in 2026');
    }

    public function test_sitemap_xml_and_robots_txt(): void
    {
        $sitemapResponse = $this->get('/sitemap.xml');
        $sitemapResponse->assertStatus(200);
        $sitemapResponse->assertHeader('Content-Type', 'application/xml; charset=utf-8');
        $sitemapResponse->assertSee('<urlset', false);
        $sitemapResponse->assertSee(url('/cpanel-hosting'), false);

        $robotsResponse = $this->get('/robots.txt');
        $robotsResponse->assertStatus(200);
        $robotsResponse->assertHeader('Content-Type', 'text/plain; charset=utf-8');
        $robotsResponse->assertSee('User-agent');
    }

    public function test_contact_form_submission_creates_inquiry(): void
    {
        $payload = [
            'name' => 'John Doe Test',
            'email' => 'john.test@example.com',
            'phone' => '+8801700000000',
            'department' => 'Pre-Sales & Plan Inquiries',
            'service' => 'cpanel-gold-3',
            'message' => 'Hello, I would like to inquire about moving my business website to your cPanel hosting.',
        ];

        $response = $this->post('/contact', $payload);

        $response->assertSessionHas('contact_success');
        $this->assertDatabaseHas('inquiries', [
            'name' => 'John Doe Test',
            'email' => 'john.test@example.com',
            'department' => 'Pre-Sales & Plan Inquiries',
        ]);
    }

    public function test_contact_form_returns_json_for_ajax_submissions(): void
    {
        $this->postJson('/contact', [
            'name' => 'Ajax User',
            'email' => 'ajax@example.com',
            'department' => 'Technical Support (General)',
            'message' => 'Please help me with my hosting account.',
        ])->assertOk()->assertJson(['success' => true]);

        $this->postJson('/contact', ['name' => 'Missing fields'])->assertUnprocessable();
    }

    public function test_admin_dashboard_redirects_unauthenticated_guests(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_admin_can_access_dashboard(): void
    {
        $admin = User::first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_all_management_modules(): void
    {
        $admin = User::first();

        $routes = [
            '/admin/inquiries',
            '/admin/pages',
            '/admin/pages/1',
            '/admin/shared-sections',
            '/admin/layout',
            '/admin/domain-pricing',
            '/admin/content/blog',
            '/admin/settings',
            '/admin/smtp',
            '/admin/users',
        ];

        foreach ($routes as $route) {
            $this->actingAs($admin)->get($route)->assertStatus(200);
        }
    }

    public function test_admin_can_update_settings(): void
    {
        $admin = User::first();

        $response = $this->actingAs($admin)->post('/admin/settings', [
            'site_name' => 'Host Divine Cloud Enterprise',
            'brand_name' => 'Host Divine',
            'menu_primary' => 'overwritten',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('settings', ['key' => 'site_name', 'value' => 'Host Divine Cloud Enterprise']);
        $this->assertDatabaseMissing('settings', ['key' => 'menu_primary', 'value' => 'overwritten']);
    }

    public function test_admin_can_view_and_update_inquiry(): void
    {
        $admin = User::first();

        $inquiry = Inquiry::create([
            'name' => 'Sarah Connor',
            'email' => 'sarah@example.com',
            'department' => 'Pre-Sales & Plan Inquiries',
            'message' => 'Need pricing details for cluster setup',
            'status' => 'new',
            'priority' => 'normal',
        ]);

        $showResponse = $this->actingAs($admin)->get("/admin/inquiries/{$inquiry->id}");
        $showResponse->assertStatus(200);

        $updateResponse = $this->actingAs($admin)->put("/admin/inquiries/{$inquiry->id}", [
            'status' => 'contacted',
            'priority' => 'high',
            'admin_notes' => 'Sent customized quotation via email.',
        ]);

        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('inquiries', [
            'id' => $inquiry->id,
            'status' => 'contacted',
            'priority' => 'high',
        ]);
    }
}
