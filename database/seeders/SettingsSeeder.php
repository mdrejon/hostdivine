<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Scalar site settings. Header, menus, footer, social links and widgets
 * are structured settings seeded by CmsContentSeeder (Site Layout screen).
 */
class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General & branding
            'site_name' => 'Host Divine™ Cloud Hosting',
            'brand_name' => 'Host Divine',
            'site_tagline' => 'High-performance web hosting, reseller hosting, VPS, VDS, dedicated servers, domains, SSL, business email and managed SEO with 24/7 expert support from Host Divine.',
            'site_logo' => 'assets/img/logo.svg',
            'site_logo_dark' => 'assets/img/dark-logo.svg',
            'site_favicon' => 'assets/img/favicon.svg',
            'established_year' => '2004',

            // External systems
            'client_area_url' => 'https://robot.hostdivine.com/clientarea.php',
            'robot_cart_url' => 'https://robot.hostdivine.com/cart.php',

            // Contact
            'sales_email' => 'sales@hostdivine.com',
            'support_email' => 'support@hostdivine.com',
            'abuse_email' => 'abuse@hostdivine.com',
            'whatsapp_number' => '+971500000000',

            // SMTP
            'mail_mailer' => 'smtp',
            'mail_host' => 'mail.hostdivine.com',
            'mail_port' => '587',
            'mail_username' => 'noreply@hostdivine.com',
            'mail_password' => '',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'noreply@hostdivine.com',
            'mail_from_name' => 'Host Divine Networks',

            // Scripts & SEO
            'header_scripts' => '',
            'footer_scripts' => '',
            'google_analytics_id' => '',
            'robots_txt' => "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /login\n\nSitemap: ".url('/sitemap.xml'),
        ];

        foreach ($settings as $key => $value) {
            if (! Setting::query()->where('key', $key)->exists()) {
                Setting::set($key, $value);
            }
        }
    }
}
