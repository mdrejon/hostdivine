# Host Divine™ — Product Requirements Document (PRD)
## Full-Stack Content Management & Admin Dashboard System

---

## 1. Executive Summary & Vision

**Host Divine™** is an established cloud hosting, domain registrar, and managed infrastructure provider operating since 2004. This PRD defines the technical requirements, architecture, and data flows to transform the static HTML marketing website (28 static templates) into an enterprise-grade, content-managed platform.

### Core Architectural Decisions:
1. **No Client Portal**: WHMCS/Robot Control Center handles external client billing and server management (`robot.hostdivine.com`). This project focuses strictly on **Public Marketing Frontend** + **Centralized Admin Management Dashboard**.
2. **Backend Management**: **Laravel 11/12 + Inertia.js v2 + Vue 3 (Composition API, Tailwind CSS)** for a seamless single-page application (SPA) admin experience matching the reference dashboard prototype.
3. **Public Frontend**: **Pure Laravel Blade Components Structure**, delivering fast server-side rendering (SSR), high Core Web Vitals performance, and search engine crawlability.
4. **SEO-First Engine**: Complete dynamic control over Meta Titles, Descriptions, OpenGraph tags, Canonical URLs, XML Sitemaps, Robots.txt, and JSON-LD Structured Data Schema (Organization, Product, FAQPage, BlogPost).

---

## 2. Dashboard UI Reference & Architecture

Based on the dashboard prototype overview:
- **Clean Two-Tone Layout**: Modern light slate background (`#f8fafc`) with pure white elevated cards, subtle borders (`#e2e8f0`), and royal blue primary accents (`#2563eb`).
- **Sidebar Navigation**:
  - Brand header with Admin Avatar & Role indicator ("Super Admin").
  - Quick-search filter for navigation items ("Search Menu Here...").
  - Clear section grouping:
    - **Overview**: Dashboard
    - **Leads & CRM**: Inquiries / Contact Submissions
    - **Product Catalog**: Hosting Packages, Reseller Packages, Cloud & Bare-Metal Servers, Domain TLDs & Pricing
    - **Website Management**: Hero Sliders, Services, Blog & Articles, FAQs, Testimonials, Timeline Milestones, Static Pages
    - **Global Settings**: General Info, Brand Assets, Contact & Branches, Social Links, Custom Header/Footer Scripts
    - **SEO Engine**: Per-Page Meta Tags, XML Sitemap, Robots.txt, JSON-LD Schemas
    - **System & Security**: Email SMTP Configuration, User/Staff Management, Database Backup & Health
- **Top Header Bar**:
  - Hamburger toggle for mobile/compact layout.
  - "Go to Home" quick action link (opens public frontend in a new tab).
  - Notification drawer & quick alerts.
  - Light / Dark theme switch.
  - Admin profile menu with Jetstream logout & profile update.
- **Main Dashboard Metrics**:
  - Today's Inquiries, Pending Inquiries, Active Hosting Plans, Published Blog Posts, Active Domain TLDs, Staff Users.
  - Inquiries Last 7 Days (Interactive Bar / Trend Chart).
  - Inquiry Status Distribution Progress Bars (Pending, Contacted, In Progress, Closed).
  - Recent Leads Quick-Response Table.

---

## 3. Product Features & Functional Scope

### 3.1. Inquiries & Lead Management
- Captures submissions from `contact.html` (department routing: Pre-Sales, Support, Migration, .BD Domain, Reseller, Dedicated).
- Status workflow: `New` -> `In Progress` -> `Contacted` -> `Closed` / `Spam`.
- Internal staff notes for follow-up.
- One-click WhatsApp direct reply link (`https://wa.me/{phone}?text=...`).
- Email notification to administrators upon receiving new inquiries.

### 3.2. Hosting & Server Package CMS
- **Web Hosting Plans**: Gold, Platinum, and Advance package tiers (cPanel NVMe & Windows IIS/MSSQL).
- **Language Hosting Platforms**: PHP, Node.js, Python, Ruby, Java/Tomcat specifications.
- **Reseller Hosting**: Standard cPanel Reseller, Master Reseller, Alpha Reseller, and Flex Reseller tiers.
- **Cloud & Bare Metal**: KVM VPS configurations and Dedicated bare-metal servers.
- **Attributes Managed**:
  - Plan Name, Slug, Package Category, Pricing (USD / BDT), Billing Cycle (Yearly, Monthly).
  - Hardware specifications: NVMe Storage, Bandwidth, Databases, Email mailboxes, Subdomains, RAM, vCPU cores, Uplink speed.
  - Badges ("Recommended", "Popular", "Flat 25% OFF").
  - Direct cart checkout URL (linking to `robot.hostdivine.com/cart.php`).
  - Active/Inactive status toggle & drag-and-drop sort order.

### 3.3. Domain TLDs & Pricing Manager
- Full management of **540+ TLDs** (e.g., `.com`, `.net`, `.org`, `.bd`, `.xyz`, `.ai`, `.io`).
- Configurable multi-year pricing: 1 Year, 2 Years, 3 Years, 5 Years, 10 Years in BDT.
- Popular/Featured flags for quick display on homepage domain chips.
- Interactive live filter for frontend `domain-pricing.blade.php`.

### 3.4. Content & Marketing CMS
- **Hero Slider Manager**: Manage 4+ hero carousel slides (Headline, Subhead, CTA 1, CTA 2, Console highlights, Background class/image, ordering).
- **Blog & Tutorials CMS**: Rich-text post editor, category assignment, read-time calculation, featured image upload, publish date, author details.
- **FAQ Knowledge Base**: Categorized accordion items (General, Hosting, Reseller, Domains, Servers, Migration) with sort order.
- **Customer Testimonials**: Customer name, company/role, avatar initials & color, 5-star rating, review text, service tag.
- **Timeline Milestones**: Company history checkpoints from 2004 to present.

### 3.5. Comprehensive SEO Engine
- **Page Meta Management**: Dedicated SEO settings for every route/page (`title`, `meta description`, `keywords`, `canonical URL`, `og:image`, `og:type`, `twitter:card`).
- **Structured Data (Schema.org / JSON-LD)**:
  - `Organization` schema for brand credentials, founding year (2004), customer care contacts, and social profiles.
  - `Product` & `Offer` schema for hosting plans and domain prices.
  - `FAQPage` schema automatically generated from active FAQ items.
  - `BlogPosting` schema for article pages.
- **Sitemap & Robots**:
  - Automated XML sitemap generator at `/sitemap.xml` with priority and `changefreq`.
  - Dynamic Robots.txt editor from the dashboard.

### 3.6. Global Settings & Integrations
- **Branding**: Site title, dark & light SVG logos, favicons, copyright statement.
- **Branches & Contact Info**: Dubai UAE and Dhaka Bangladesh office addresses, phone numbers, WhatsApp link, support links.
- **Scripts & Tracking**: Header and footer script injection fields for Google Analytics 4 (GA4), Google Tag Manager (GTM), Meta Pixel, custom chat widgets.
- **SMTP Configuration**: Live credentials setup with built-in "Send Test Email" utility.

---

## 4. Database Schema Design

```
+--------------------------------------------------------------------------+
|                               DATABASE MAP                               |
+--------------------------------------------------------------------------+

[ users ]
  - id, name, email, password, role ('super_admin','admin','editor'), ...

[ settings ]
  - id, key (string, unique), value (longText), group ('general','contact','scripts','seo')

[ inquiries ]
  - id, name, email, phone, department, service_id, message, status, notes, ip_address

[ hosting_categories ]
  - id, name, slug, group ('hosting','reseller','server','service'), sort_order, is_active

[ hosting_plans ]
  - id, category_id, tier_group ('gold','platinum','advance','standard'),
    name, slug, badge, short_desc, price_bdt, price_usd, billing_cycle,
    features (json), order_url, is_featured, sort_order, is_active

[ domain_tlds ]
  - id, tld (unique), price_1yr, price_2yr, price_3yr, price_5yr, price_10yr,
    is_popular, is_active, sort_order

[ hero_slides ]
  - id, title, subtitle, badge_text, primary_btn_text, primary_btn_url,
    secondary_btn_text, secondary_btn_url, bg_class, image_path, sort_order, is_active

[ blog_categories ]
  - id, name, slug, sort_order

[ blog_posts ]
  - id, category_id, title, slug, excerpt, content (longtext), featured_image,
    author_name, read_time, is_featured, is_published, published_at, views_count

[ faqs ]
  - id, category ('general','hosting','reseller','domains','servers','migration'),
    question, answer (text), sort_order, is_active

[ testimonials ]
  - id, client_name, client_role, avatar_initials, avatar_color, rating,
    content (text), service_tag, sort_order, is_active

[ timelines ]
  - id, year, title, description, badge, sort_order, is_active

[ seo_meta ]
  - id, path_or_route (unique), meta_title, meta_description, meta_keywords,
    canonical_url, og_title, og_description, og_image, twitter_card, custom_schema_json
```

---

## 5. Public Frontend Blade Component Architecture

All frontend views will live under `resources/views/` and strictly reuse the CSS styles (`html/assets/css/style.css`), vanilla JS animations (`html/assets/js/main.js`), and responsive breakpoints defined in the project:

### 5.1. Master Layout & Partials
- `layouts/frontend.blade.php`: Base HTML5 shell with skip link, fonts, CSS tokens, SEO meta tags component, and deferred JS.
- `components/header.blade.php`: Announcement top-strip, company contact links, brand logo, mega navigation dropdowns, action buttons.
- `components/footer.blade.php`: Robot control center box, company stat indicators, multi-column navigation, announcements & copyright.
- `components/quick-dock.blade.php`: Floating bottom dock (Back to top, 24/7 support desk, live chat, WhatsApp).
- `components/seo-meta.blade.php`: Outputs dynamic `<title>`, `<meta description>`, OpenGraph, Twitter, canonical, and JSON-LD schema blocks.

### 5.2. Functional UI Components
- `components/hero-slider.blade.php`: Height-aware carousel slider with interactive controls and background glow layers.
- `components/domain-search.blade.php`: Register & Transfer tabbed card, instant lookup input, and interactive TLD price chips.
- `components/plan-card.blade.php`: Reusable hosting/reseller plan card with feature checkmarks, price highlight, and direct checkout button.
- `components/pricing-grid.blade.php`: Wrapper grid (`pricing-grid-3` / `pricing-grid-4`) with tab switcher (Gold / Platinum / Advance).
- `components/faq-accordion.blade.php`: Semantic `<details><summary>` accordion with smooth disclosure styling.
- `components/review-slider.blade.php`: Testimonial card slider with customer rating stars and avatar tags.
- `components/stats-float.blade.php`: Floating stats banner (`240,000+ Websites`, `99.9% Uptime`, `60s Activation`, `30-Day Money-Back`).
- `components/contact-form.blade.php`: CSRF-protected contact form with validation error indicators and flash feedback.

---

## 6. Security, Performance & Non-Functional Requirements

1. **Security**:
   - CSRF protection across all forms.
   - Admin authentication via Laravel Fortify/Jetstream with brute-force rate limiting.
   - Honeypot and rate-limiting on the public contact form (`throttle:5,1`) to eliminate spam.
   - HTML sanitization on rich-text inputs (Purifier / clean strip).
2. **Performance**:
   - Database query caching using Laravel Cache tag/remember for settings, plans, and FAQs.
   - Fast static asset bundling via Vite.
   - Responsive images with lazy-loading (`loading="lazy"`) and hero image preloads (`fetchpriority="high"`).
3. **Responsive Fidelity**:
   - Strictly adhere to the 7 responsive tiers (`html/AGENTS.md`):
     - Mobile (320px–575.98px)
     - Small Tablet (576px–767.98px)
     - Tablet (768px–991.98px)
     - Laptop (992px–1199.98px)
     - Desktop (1200px–1399.98px)
     - Large Desktop (1400px–1919.98px)
     - Big Screen (1920px+)
   - Automated overflow testing via `responsive-check.html` ensuring 0 horizontal scroll.

