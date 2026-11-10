# Host Divine™ — Step-by-Step Working Process & Execution Guide

This document defines the sequential implementation workflow to build the **Host Divine Hosting Content Management System & Admin Dashboard**. Follow each phase step-by-step.

---

## Roadmap Overview

```
Phase 1: Foundation & Database Migrations
         ↓
Phase 2: Data Models, Seeders & Default Data
         ↓
Phase 3: Global Configuration, SEO Engine & Helper Services
         ↓
Phase 4: Admin Dashboard Layout & Navigation (Inertia + Vue 3)
         ↓
Phase 5: Admin CRUD Modules Implementation
         ↓
Phase 6: Frontend Blade Component Library & Master Layout
         ↓
Phase 7: Frontend Page Conversion & Dynamic Data Binding (28 Pages)
         ↓
Phase 8: Contact Form Handling, Email Alerts & WhatsApp Integration
         ↓
Phase 9: SEO Validation, Responsive Checks & Quality Assurance
```

---

## Phase 1: Foundation & Database Migrations

### Goal
Establish the database schema supporting full CMS control without touching client management.

### Action Items
1. Configure database connection in `.env` (SQLite or MySQL).
2. Create migrations:
   - `create_settings_table.php`: Key-value configuration store for brand, contact, header/footer, and third-party scripts.
   - `create_inquiries_table.php`: Contact leads, department routing, notes, and status enum (`new`, `in_progress`, `contacted`, `closed`, `spam`).
   - `create_hosting_categories_table.php`: Categories for cPanel, Windows, Developer Languages, Reseller, VPS, Dedicated, Business Services.
   - `create_hosting_plans_table.php`: Package tiers (Gold, Platinum, Advance), specs (NVMe storage, bandwidth, cPanel/WHM features), pricing (BDT/USD), order link.
   - `create_domain_tlds_table.php`: 540+ TLD pricing records for 1, 2, 3, 5, 10 years, popular flags, promo badges.
   - `create_hero_slides_table.php`: Homepage hero slides with badges, titles, descriptions, buttons, and visual tags.
   - `create_blog_categories_and_posts_tables.php`: Blog articles, categories, reading times, featured tags, markdown/HTML content.
   - `create_faqs_table.php`: Categorized questions and answers for hosting, reseller, domains, servers, migration.
   - `create_testimonials_table.php`: Client reviews, rating, role, avatar styling, and service tags.
   - `create_timelines_table.php`: Company milestones from 2004 to present.
   - `create_seo_meta_table.php`: Route-based meta titles, descriptions, canonical URLs, OG images, and JSON-LD schema blocks.
3. Run migrations: `php artisan migrate`.

---

## Phase 2: Data Models, Seeders & Default Content

### Goal
Populate the database with all existing static content from `html/` so the website is 100% functional out of the box.

### Action Items
1. Create Eloquent Models with explicit type casts, relationships, and scopes:
   - `Setting`, `Inquiry`, `HostingCategory`, `HostingPlan`, `DomainTld`, `HeroSlide`, `BlogCategory`, `BlogPost`, `Faq`, `Testimonial`, `Timeline`, `SeoMeta`.
2. Build comprehensive database seeders:
   - `SettingsSeeder`: Seed UAE & BD addresses, phone numbers, WhatsApp link, social links, brand metadata.
   - `HeroSlideSeeder`: Seed the 4 hero slides from `html/index.html`.
   - `HostingPlanSeeder`: Seed Gold, Platinum, and Advance plans for cPanel, Windows, Reseller, and VPS servers.
   - `DomainTldSeeder`: Seed popular TLDs (`.com`, `.net`, `.org`, `.bd`, `.xyz`, etc.) and bulk TLD prices from `domain-pricing.html`.
   - `FaqSeeder`: Seed all 18+ categorized FAQs from `html/faq.html`.
   - `TestimonialSeeder`: Seed verified customer reviews from `html/index.html`.
   - `TimelineSeeder`: Seed company milestones from `html/timeline.html`.
   - `SeoMetaSeeder`: Seed optimized meta tags for all 28 pages.
   - `AdminUserSeeder`: Create the default Super Admin user account.
3. Run seeders: `php artisan db:seed`.

---

## Phase 3: Global Configuration, SEO Engine & Helper Services

### Goal
Provide high-performance access to settings and dynamic SEO across all pages.

### Action Items
1. Create `App\Services\SettingService`:
   - Cached settings retrieval (`Setting::get('key', 'default')`).
   - Settings cache invalidation on update.
2. Create `App\Services\SeoService`:
   - Resolve meta tags for the current route or custom slug.
   - Generate automated JSON-LD schemas (`Organization`, `Product`, `FAQPage`, `BreadcrumbList`).
   - Generate dynamic `/sitemap.xml` including all pages, plans, and blog posts.
   - Serve dynamic `/robots.txt` configurable from the dashboard.
3. Share global settings with Inertia admin views via `HandleInertiaRequests` middleware and Blade views via `ViewServiceProvider`.

---

## Phase 4: Admin Dashboard Layout & Navigation (Inertia.js + Vue 3)

### Goal
Implement the admin layout matching the prototype design in `media_1794241856088_c11d8f90.jpg`.

### Action Items
1. Create Inertia Admin Layout (`resources/js/Layouts/AdminLayout.vue`):
   - **Sidebar**:
     - Admin profile pill (Avatar, "Super Admin" badge).
     - Menu search input ("Search Menu Here...").
     - Navigation items with active state pills in royal blue (`#2563eb`).
     - Collapsible submenus for Website Management and Global Settings.
   - **Top Navigation Bar**:
     - Hamburger toggle for responsive sidebar collapse.
     - "Go to Home" button with external link icon.
     - Notifications icon with unread inquiry counter badge.
     - Settings shortcut icon.
     - Dark / Light theme toggle.
     - Admin user dropdown with logout action.
2. Build reusable UI components:
   - `StatCard.vue`: KPI metric cards with colored indicators.
   - `DataTable.vue`: Searchable, paginated data table with status badges and action buttons.
   - `Modal.vue` & `ConfirmDialog.vue`: Confirmation dialogs for deletion.
   - `StatusBadge.vue`: Visual badges for status (`New`, `Pending`, `Active`, `Closed`).

---

## Phase 5: Admin CRUD Modules Implementation

### Goal
Allow non-technical administrators to manage every aspect of the website content and settings.

### Action Items
1. **Dashboard Home (`/admin/dashboard`)**:
   - Aggregate statistics: Total Inquiries, Today's Inquiries, Active Plans, Published Posts, Active TLDs.
   - 7-day inquiry trend chart (Chart.js or Vue ApexCharts).
   - Inquiries status breakdown bar (Pending, Contacted, Closed).
   - Recent leads quick-view table.
2. **Inquiry Management (`/admin/inquiries`)**:
   - List inquiries with department filtering, search, and pagination.
   - Detail view with quick status update and internal notes.
   - Direct WhatsApp message launch button with prefilled template.
3. **Hosting Packages Management (`/admin/hosting-plans`)**:
   - Create, edit, delete hosting and server plans.
   - Feature list manager (dynamic checklist array).
   - Price manager (BDT and USD values).
   - Sort order reordering and active toggle.
4. **Domain TLDs Manager (`/admin/domain-pricing`)**:
   - Filterable TLD list.
   - Bulk price updates for 1yr to 10yr tenures.
   - Popular / homepage featured toggles.
5. **Content CMS Modules**:
   - **Hero Sliders (`/admin/hero-slides`)**: Slide content, call-to-actions, visual cards.
   - **Blog Articles (`/admin/blog`)**: Rich text post editor, category selector, featured image upload.
   - **FAQs (`/admin/faqs`)**: Question, answer, category grouping, and order.
   - **Testimonials (`/admin/testimonials`)**: Client reviews, avatar colors, star ratings.
   - **Timeline (`/admin/timeline`)**: Historical milestones.
6. **SEO Management (`/admin/seo`)**:
   - Edit meta titles, descriptions, OpenGraph tags per page.
   - Edit robots.txt rules.
   - View generated sitemap status.
7. **Global Settings (`/admin/settings`)**:
   - General, Branding (logo upload), Contact details (UAE & BD offices), Social media links.
   - Custom header/footer script injection (Google Analytics, GTM, Meta Pixel).
8. **SMTP & Email Settings (`/admin/smtp`)**:
   - Host, port, username, password, encryption.
   - "Send Test Email" modal to verify SMTP connectivity.
9. **Staff / User Management (`/admin/users`)**:
   - Manage administrators, roles, and profile passwords.

---

## Phase 6: Frontend Blade Component Library & Master Layout

### Goal
Construct clean, high-performance Blade components matching the HTML design system.

### Action Items
1. Copy static assets from `html/assets/` to `public/assets/` (CSS, JS, images, SVG logos, partner icons).
2. Create Master Layout `resources/views/layouts/frontend.blade.php`:
   - Head tag with fonts (`Inter`, `Manrope`, `Sora`) and Font Awesome 6.5.2.
   - Component `<x-seo-meta :seo="$seo" />`.
   - Skip link and main container wrapper.
   - Deferred script loading (`public/assets/js/main.js`).
3. Create Blade UI Components under `resources/views/components/`:
   - `x-header`: Dynamic top notification bar, brand logo, mega navigation, action buttons.
   - `x-footer`: Dynamic footer navigation, stats strip, robot control center box, copyright.
   - `x-quick-dock`: Floating dock (Scroll to top, 24/7 support, live chat, WhatsApp).
   - `x-hero-slider`: Homepage height-aware hero slider populated from database.
   - `x-domain-search`: Tabbed domain lookup with live BDT pricing chips.
   - `x-plan-card`: Reusable hosting/reseller plan card with feature badges and direct order link.
   - `x-pricing-grid`: Plan card grid with tier tabs (Gold, Platinum, Advance).
   - `x-faq-accordion`: Accessible disclosure accordion.
   - `x-review-slider`: Customer testimonials carousel.
   - `x-stats-float`: Floating metrics counter banner.
   - `x-page-hero`: Hero header for inner pages with breadcrumbs, console visuals, and badges.

---

## Phase 7: Frontend Page Conversion & Dynamic Data Binding

### Goal
Replace static HTML files with dynamic Blade templates powered by controllers and cached database queries.

### Action Items
1. Set up route structure in `routes/web.php` with clean names.
2. Build Frontend Controllers:
   - `HomeController`: Homepage (`index.blade.php`) pulling hero slides, featured plans, reviews, and domain chips.
   - `HostingController`: cPanel (`cpanel-hosting.blade.php`), Windows, and Language platforms (PHP, Node.js, Python, Ruby, Java).
   - `ResellerController`: cPanel Reseller, Master Reseller, Alpha Reseller, Flex Reseller.
   - `ServerController`: VPS servers and Dedicated bare-metal servers.
   - `DomainController`: Registration, .BD BTCL processing, domain transfer, renew, and full 540+ TLD pricing table.
   - `ServiceController`: SSL certificates, Business email, Managed SEO service.
   - `CompanyController`: About us, Timeline milestones, FAQs.
   - `BlogController`: Blog index with TTFB benchmark chart, category filter, and single article view.
   - `ContactController`: Contact page with channel directory and interactive message submission.
   - `SeoController`: Dynamic `/sitemap.xml` and `/robots.txt`.

---

## Phase 8: Contact Form Handling, Email Alerts & WhatsApp

### Goal
Provide end-to-end lead generation and instant response handling.

### Action Items
1. Handle `POST /contact`:
   - Validate form fields (`name`, `email`, `phone`, `department`, `message`).
   - Store inquiry record in `inquiries` table with status `new`.
   - Send confirmation email to the user (if SMTP enabled).
   - Send notification email to admin email (`sales@hostdivine.com`).
   - Return clean JSON or flash response with success message.
2. Connect WhatsApp floating dock and consultation buttons with live number configured in Global Settings.

---

## Phase 9: Testing, Verification & Launch Checklist

### Goal
Verify visual integrity, responsive behavior, SEO compliance, and admin functionality.

### Action Items
1. **Automated Overflow Test**:
   - Run `tools/responsive-check.html` across all 7 breakpoints (320px, 576px, 768px, 992px, 1200px, 1400px, 1920px+). Ensure 0 horizontal scrolling.
2. **SEO Verification**:
   - Inspect `<title>`, `<meta name="description">`, OpenGraph, Twitter Cards on all pages.
   - Test JSON-LD schema using Google Rich Results Test format.
   - Verify `/sitemap.xml` loads valid XML with correct URLs.
3. **Admin Functionality Verification**:
   - Log into `/admin/dashboard` as Super Admin.
   - Change a hosting plan's price in dashboard -> Confirm frontend updates immediately.
   - Submit a test inquiry via `/contact` -> Confirm inquiry appears in `/admin/inquiries`.
   - Send a test email from `/admin/smtp`.
   - Verify dark/light toggle and mobile sidebar menu responsiveness.
4. **Performance & Caching**:
   - Run `php artisan optimize` (config, route, view cache).
   - Run `npm run build` for production Vite assets.

