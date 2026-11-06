# AGENTS.md — Host Divine website

Instructions for **any AI coding agent** (Claude Code, Codex, Cursor, Copilot, Gemini, Windsurf, etc.) and human developers working on this repository.
Read this whole file before editing. It applies to `index.html` and to **every new page** added later.

---

## 1. Project at a glance

Static marketing website for **Host Divine** (cloud, reseller, VPS, dedicated hosting, domains and SSL). There is no framework and no build step.

| Path | What it is |
|---|---|
| `index.html` | Homepage. It is also the **template** for every new page (head, header, footer). |
| `assets/css/style.css` | The **single** stylesheet. Tokens, then base, header, domain search, footer, shared responsive rules, homepage sections, then homepage responsive rules. |
| `assets/js/main.js` | Vanilla JS in an IIFE. Covers the nav, hero slider, card sliders, billing switch, tabs, reveal-on-scroll and counters. |
| `assets/img/` | Logos, favicons, `hero-hosting.svg` (hero illustration) and `logos/` (technology partner SVGs). |
| `diagram/` | Wireframes (`homepage.jpeg`). Follow them for layout and section order. |
| `tools/responsive-check.html` | Automated horizontal-overflow test at every breakpoint (see §7). |
| `Hostingard/` | Offline mirror of a **third-party commercial theme**. **Reference only.** Do not edit it, link to it, or copy its code or images. |

Runtime dependencies are loaded by `@import` at the top of `style.css`: fonts (Inter, Manrope, Sora) from fonts.bunny.net and Font Awesome 6.5.2 from jsDelivr. Don't add other CDNs or frameworks (no Bootstrap or Tailwind).

---

## 2. Responsive breakpoints — the only allowed values

This table is the project standard. Every media query in CSS and every `matchMedia` call in JS **must** use these edges.

| Tier | Min width | Max width | CSS query to use |
|---|---|---|---|
| **Mobile** | 320px | 575px | `@media (max-width: 575.98px)` |
| **Small tablet** | 576px | 767px | `@media (max-width: 767.98px)` |
| **Tablet** | 768px | 991px | `@media (max-width: 991.98px)` |
| **Laptop** | 992px | 1199px | `@media (max-width: 1199.98px)` |
| **Desktop** | 1200px | 1399px | *no query: these are the base styles* |
| **Large desktop** | 1400px | 1919px | `@media (min-width: 1400px)` |
| **Big screen / 2K+** | 1920px | unlimited | `@media (min-width: 1920px)` |

### Rules

1. **The base styles are Desktop (1200–1399).** Override downward with `max-width` queries and upward with `min-width` queries.
2. **Use the `.98` upper bounds** (`575.98px`, not `575px`). This prevents a gap or overlap at fractional widths caused by zoom and Windows display scaling.
3. **Order queries from widest to narrowest** within each section: `min-width: 1920` → `min-width: 1400` → `max-width: 1199.98` → `991.98` → `767.98` → `575.98` → `399.98`. Narrower tiers then cascade over wider ones correctly.
4. **Never invent in-between breakpoints** (no `900px`, `640px`, `1160px`, `1600px`). Inside a tier, use fluid CSS instead: `clamp()`, `min()`, `%`, `fr`, `auto-fit` grids.
5. Only these **refinements** are allowed beyond the 7 tiers. Each is already in `style.css`, so reuse it rather than adding new ones:
   - **Mobile S:** `@media (max-width: 399.98px)`, for 320–399px phones only (e.g. full-width buttons).
   - **2K/4K zoom steps:** `min-width: 2400px / 2880px / 3400px` set `html { zoom }`.
   - **Height-aware hero:** `min-width: 992px` (see §4).
6. **JS must match the CSS.** Desktop navigation is `window.matchMedia('(min-width: 992px)')`, so below 992px the hamburger menu is shown. If you add a JS breakpoint, use one of the edges above.
7. The breakpoint map is repeated as a comment at the top of `style.css`. Keep both in sync.

### Where to put responsive CSS

- Shared parts (header, domain search, footer) go in the **"Responsive Breakpoints"** block after the footer styles.
- Homepage sections go in the **"Homepage Responsive Breakpoints"** block.
- Each **new page** gets its own section in `style.css`: `/* PAGE: <name> */` for its styles, then `/* PAGE: <name> — Responsive */` containing the tier queries in the order from rule 3. Don't scatter one-off media queries next to individual rules.

---

## 3. What each tier must look like

Use this as the layout contract for every section on every page.

| Element | Big screen 1920+ | Large 1400–1919 | Desktop 1200–1399 | Laptop 992–1199 | Tablet 768–991 | Small tablet 576–767 | Mobile 320–575 |
|---|---|---|---|---|---|---|---|
| `--container` | 1440px | 1360px, fluid with ≥80px side gutters | 1240px | 100% − 40px | 100% − 40px | 100% − 40px | 100% − 28px |
| Header | Full nav and 3 buttons | Full nav and 3 buttons | Full nav and 3 buttons | Condensed nav; "Sales Contact" hidden | **Hamburger**; top strip hidden | Hamburger | Hamburger; stacked action buttons |
| Hero | 2 columns, fills the screen height | Same | Same | Same, may zoom down (§4) | **1 column**, visual under the copy | 1 column, compact spacing | 1 column, simplified mock-ups, **dot tabs** |
| Card sliders (`per-4` / `per-3`) | 4 / 3, arrows outside | 4 / 3, arrows outside | 4 / 3, arrows below | 3 / 3 | 2 / 2 | 1.6 (peek) | 1.12 (peek) |
| Content grids (services, features) | 3–4 cols | 3–4 cols | 3–4 cols | 3 cols | 2 cols | 2 cols | **1 col** |
| Section padding | ~100px | ~100px | ~100px | ~100px | 80px | 64px | 64px |
| Buttons | Inline | Inline | Inline | Inline | Inline | 48px tall | Fill the row; full width at ≤399px |
| Footer | Multi-column | Multi-column | Multi-column | 3 cols | 2 cols | 2 cols | 1 col |

Typography must be fluid. Headings use `clamp(min, vw-based, max)`, as in the existing `.hero-title` and `.section-title`. The minimum body text size is **14px**; on mobile, use **16px** for inputs so iOS doesn't zoom in.

---

## 4. Height-responsive rules (short laptop screens)

Many laptops show only 550–700px of page height, especially at 125% or 150% Windows display scaling. Width alone is not enough.

- **Hero (≥992px):** the hero fills `100vh − sticky header`. `main.js` (`fitHero`) measures the height available and sets `--hero-min` and `--hero-scale` (0.7–1) on `.hero-slider`. CSS applies `zoom: var(--hero-scale)` to `.hero-copy`, `.hero-visual` and `.hero-controls`. If even 0.7 is too tall, it adds `.is-compact`, which hides `.hero-price`. **Reuse this pattern** for the hero or banner of any new page. Never give a hero a fixed `min-height` in px.
- The first screen of every page must show the headline, the main call to action and the slide controls (if any) **without scrolling** at **1366×650** and **1280×560** viewport sizes.
- Use `svh` or `dvh` (not `vh`) for anything sized to the screen on mobile, so the browser's address bar doesn't push content off-screen.
- Fixed or sticky overlays (mobile nav panel, modals) need `max-height: calc(100svh − header)` and `overflow-y: auto`.

---

## 5. Universal responsive rules (all pages)

1. **No horizontal scrolling at any width from 320px.** `body { overflow-x: hidden }` is a safety net, not a fix. Find and fix the element that overflows.
2. Grid tracks use `minmax(0, 1fr)`, and flex children that hold text get `min-width: 0`, so long words and URLs can't force overflow. Long strings use `overflow-wrap: anywhere` or `text-overflow: ellipsis`.
3. **No fixed widths over 320px** on content elements. Use `max-width` together with `width: 100%`.
4. **Images:** always set `width` and `height` attributes (to prevent layout shift) plus `alt`. Use `max-width: 100%` (already global) and `object-fit` for cropped frames. Use `loading="lazy"` below the fold, and `fetchpriority="high"` plus a `<link rel="preload">` for the hero image only. Prefer SVG for illustrations.
5. **Touch targets are at least 44×44px** at ≤991px (buttons, arrows, dots wrapped in larger hit areas, menu items).
6. **Hover is an enhancement only.** Nothing may depend on hover to reveal content. Put hover-only effects in `@media (hover: hover)` when they would stick on touch screens.
7. **Scroll containers clip their children's shadows.** A horizontally scrolling track also clips vertically. Give the track padding and cancel it with negative margins (see `.slider-track`), and keep hover shadows inside that padding.
8. **Motion:** every animation needs a `prefers-reduced-motion: reduce` fallback (a global one exists at the end of `style.css`).
9. **Accessibility:** keep the skip link, landmarks, `aria-*` on tabs and sliders, visible `:focus-visible` outlines, and AA colour contrast on both the navy and white backgrounds.
10. **Fixed overlays need a clean parent chain.** Never put `backdrop-filter`, `filter`, `transform`, `perspective`, `contain` or `will-change: transform` on an **ancestor** of a `position: fixed` element (mobile nav panel, modals, drawers). Any of these makes the ancestor the panel's positioning box, so the panel gets squashed to the ancestor's size. For a frosted header, put the blur on a `::before` layer instead (see `.site-header::before`).
11. **Brand assets:** use only Host Divine's own or properly licensed assets. Never ship imagery that carries another company's name or logo (a "HOSTSEO" photo had to be replaced for this reason).

---

## 6. Creating a new page (checklist)

1. Copy the `<head>`, `<header class="site-header">` and `<footer>` from `index.html` unchanged, and update `<title>`, the meta description and the preload image.
2. Wrap the content in `<main id="main">`. Make each section `<section class="section …" id="…">` with a `.container` inside. Reuse the existing components: `.section-head`, `.btn`, `.card-slider` with the `data-slider*` hooks, `.plan-card`, `.service-card`, tabs with `data-tabs`, and `.reveal`.
3. Add page CSS as described in §2 ("Where to put responsive CSS"). Reuse the tokens (`--navy`, `--orange`, `--radius`, `--shadow`, etc.) and never hard-code new colours if a token exists.
4. JS behaviour attaches to `data-*` attributes, not to classes. Add new behaviour to `main.js` inside the existing IIFE, guarded with `if (element)` so pages without that element don't throw errors.
5. Follow the wireframe in `diagram/` when one exists for the page.
6. Run the full test in §7 before saying the page is done.

---

## 7. Testing — required before finishing any UI change

### a) Overflow test (automated)

Run it from the project root:

```bash
php -S localhost:8000          # or: npx serve .
# open http://localhost:8000/tools/responsive-check.html?page=index.html
```

Or run it headless (Windows path shown):

```bash
"/c/Program Files/Google/Chrome/Application/chrome.exe" --headless=new --disable-gpu \
  --allow-file-access-from-files --virtual-time-budget=60000 \
  --dump-dom "file:///D:/project/2026/smart-freamework/10%20Hostdevine/hostdivine/tools/responsive-check.html?page=index.html"
```

The page title must read **PASS**. It tests 320, 360, 375, 390, 414, 575/576, 767/768, 820, 991/992, 1024, 1199/1200, 1280, 1366, 1399/1400, 1536, 1919/1920 and 2560px.

### b) Visual check (screenshots)

Look at one width per tier: **360, 600, 820, 1024, 1280, 1440, 1920**. For each page, also check these heights: **1366×650, 1280×560, 1920×950** (the first screen must be complete, see §4).
Headless Chrome enforces a minimum window width of about 500px. For phone widths, render the page inside an iframe of the required width rather than using `--window-size`.

Elements with `.reveal` start invisible until they're scrolled into view. When taking a full-page screenshot, inject `.reveal{opacity:1!important;transform:none!important}` into a **temporary copy** of the page. Delete the copy afterwards.

### c) Manual

Open and close the mobile nav, try every slider (swipe, arrows and dots), switch tabs and billing periods, and tab through the page with the keyboard.

---

## 8. Definition of done

- [ ] Uses only the breakpoints in §2; there are no new magic numbers in media queries.
- [ ] `tools/responsive-check.html` reports **PASS** for the page.
- [ ] Screenshots look right at every tier, and the first screen is complete at short laptop heights.
- [ ] No console errors, and pages without a given component don't throw errors.
- [ ] Images have `width`, `height` and `alt`; nothing uses third-party branding.
- [ ] The breakpoint comment in `style.css` and this file still agree.
