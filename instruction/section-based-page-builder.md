# Section-Based Page Builder: Implementation Guide

A reusable recipe for building a CMS where every page is an ordered list of **sections** (components), and each section's content is stored as JSON. One schema file drives both the admin forms and data cleaning. Common blocks such as the header, footer, menu and shared sections are edited in **one place**.

It fits portfolio sites, agency/company sites, landing pages, product sites, and anything else that is mostly content.

> **Reference implementation:** the Host Divine project (`D:\laragon-new\laragon\www\hostdivine`). The file paths in this guide point to it. When starting a new project, copy those files and adapt them.

---

## 1. When to use it (and when not to)

**Use it when:**
- Pages are made of repeated visual blocks (hero, features, gallery, testimonials, FAQ, CTA…).
- The client must change text, images, buttons, order and visibility without a developer.
- You want few database tables.

**Don't use it for:**
- Data you need to **query, filter, sort, paginate or relate**, such as blog posts, products, orders or prices. Those still get their own table. The page builder only *displays* them, for example with a "latest posts" section that queries the `posts` table.
- Free-form drag-and-drop design (Webflow-style). This system gives editors **fixed, well-designed blocks**, not pixel control. That is a feature: the design cannot be broken.

---

## 2. The core idea

```
                    config/cms.php  (THE SCHEMA: one source of truth)
                   ┌─────────────────────────────┬──────────────────────────────┐
                   │ sections: { type => fields }│ layout: { key => fields }    │
                   └──────────────┬──────────────┴──────────────┬───────────────┘
                                  │                             │
         ┌────────────────────────┼─────────────┐               │
         ▼                        ▼             ▼               ▼
  Admin form (Vue)        ContentSchema    Blade partial    settings table (JSON)
  built from fields       cleans saved     sections/{type}  header, menus, footer,
                          data             .blade.php       widgets

  pages ──1:N──► sections (type, data JSON, sort_order, is_active)
                    │
                    └─ shared_id ──► library section (page_id = NULL)
                                     edited once, shown on many pages
```

There are five rules:

1. **A page is a list of sections.** Each section row has a `type` (for example `hero`), a JSON `data` column, a `sort_order` and an `is_active` flag.
2. **A type is a schema plus a partial.** `config/cms.php` lists the fields. `resources/views/sections/{type}.blade.php` renders them. Nothing else is needed to add a new block.
3. **The admin UI is generated from the schema.** You never hand-build a form for a block.
4. **Saved data is cleaned against the schema.** Unknown keys are dropped and types are normalised.
5. **Common content lives in one place:**
   - Site-wide structure (header, menu, footer, social links) lives in the `settings` table as JSON, with its own schema.
   - Reusable blocks are **shared sections**: rows with `page_id = NULL` that pages link to through `shared_id`.

---

## 3. Database: 3 tables

### `pages`
```php
Schema::create('pages', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('slug')->unique();          // 'home' = site root "/"
    $table->string('meta_title')->nullable();
    $table->string('meta_description', 500)->nullable();
    $table->string('meta_keywords')->nullable();
    $table->string('og_image')->nullable();
    $table->boolean('is_active')->default(true); // false = draft (404 on the site)
    $table->unsignedInteger('sort_order')->default(0);
    $table->timestamps();
});
```

### `sections`
```php
Schema::create('sections', function (Blueprint $table) {
    $table->id();
    // page_id NULL  => shared "library" section
    $table->foreignId('page_id')->nullable()->constrained('pages')->cascadeOnDelete();
    // shared_id set => this page row just renders that library section
    $table->foreignId('shared_id')->nullable()->constrained('sections')->cascadeOnDelete();
    $table->string('type', 60);                // key in config/cms.php
    $table->string('name')->nullable();        // admin-only label
    $table->json('data')->nullable();          // the content
    $table->unsignedInteger('sort_order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->index(['page_id', 'sort_order']);
});
```

### `settings`
```php
Schema::create('settings', function (Blueprint $table) {
    $table->id();
    $table->string('key')->unique();   // e.g. site_name, menu_primary, layout_footer
    $table->longText('value')->nullable();
    $table->string('group')->default('general');
    $table->string('type')->default('text'); // 'json' for structured values
    $table->timestamps();
});
```

These three tables are all you need. Add a dedicated table only for queryable data (see section 1).

---

## 4. The schema file: `config/cms.php`

### 4.1 Format

```php
<?php

$buttonFields = [
    ['key' => 'label', 'label' => 'Label', 'type' => 'text', 'width' => 'half'],
    ['key' => 'url', 'label' => 'Link', 'type' => 'url', 'width' => 'half'],
    ['key' => 'style', 'label' => 'Style', 'type' => 'select', 'width' => 'half',
        'options' => ['primary' => 'Primary', 'outline' => 'Outline']],
    ['key' => 'new_tab', 'label' => 'Open in new tab', 'type' => 'toggle'],
];

// Reusable fragments keep the schema short and consistent.
$buttons = fn (string $key = 'buttons', string $label = 'Buttons') =>
    ['key' => $key, 'label' => $label, 'type' => 'repeater', 'item_label' => 'label', 'fields' => $buttonFields];

$head = [
    ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'width' => 'half', 'group' => 'Heading'],
    ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'width' => 'half', 'group' => 'Heading'],
    ['key' => 'subtitle', 'label' => 'Subtitle', 'type' => 'textarea', 'group' => 'Heading'],
];

$layout = [
    ['key' => 'background', 'label' => 'Background', 'type' => 'select', 'group' => 'Layout',
        'options' => ['' => 'White', 'bg-soft' => 'Soft', 'bg-dark' => 'Dark']],
    ['key' => 'anchor', 'label' => 'Anchor ID', 'type' => 'text', 'group' => 'Layout',
        'help' => 'For in-page links such as #projects.'],
];

return [
    'sections' => [

        'hero' => [
            'label' => 'Hero',
            'icon' => 'fa-solid fa-star',
            'description' => 'Intro with name, role, photo and buttons.',
            'fields' => [
                ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'width' => 'half'],
                ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'width' => 'half'],
                ['key' => 'text', 'label' => 'Intro', 'type' => 'textarea'],
                ['key' => 'photo', 'label' => 'Photo', 'type' => 'image'],
                $buttons(),
            ],
        ],

        'projects' => [
            'label' => 'Project grid',
            'icon' => 'fa-solid fa-briefcase',
            'description' => 'Portfolio cards with image, tags and link.',
            'fields' => [
                ...$head,
                ['key' => 'columns', 'label' => 'Columns', 'type' => 'select', 'width' => 'half',
                    'options' => ['2' => '2', '3' => '3']],
                ['key' => 'items', 'label' => 'Projects', 'type' => 'repeater', 'item_label' => 'title', 'fields' => [
                    ['key' => 'image', 'label' => 'Image', 'type' => 'image'],
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'url', 'label' => 'Link', 'type' => 'url', 'width' => 'half'],
                    ['key' => 'summary', 'label' => 'Summary', 'type' => 'textarea'],
                    ['key' => 'tags', 'label' => 'Tags', 'type' => 'list'],
                ]],
                ...$layout,
            ],
        ],

        'cta_banner' => [ /* … */ ],
        'custom_html' => [ /* one 'html' field: escape hatch for one-off blocks */ ],
    ],

    // Site-wide structured settings, stored as JSON in the settings table.
    'layout' => [
        'menu_primary' => [
            'label' => 'Main menu',
            'icon' => 'fa-solid fa-bars',
            'list' => true,                 // value is a LIST of objects (order = menu order)
            'item_label' => 'label',
            'fields' => [
                ['key' => 'label', 'label' => 'Label', 'type' => 'text', 'width' => 'half'],
                ['key' => 'url', 'label' => 'Link', 'type' => 'url', 'width' => 'half'],
                ['key' => 'children', 'label' => 'Dropdown links', 'type' => 'repeater', 'item_label' => 'label',
                    'fields' => $buttonFields],
            ],
        ],
        'layout_footer' => [
            'label' => 'Footer',
            'icon' => 'fa-solid fa-shoe-prints',
            'fields' => [ /* about text, columns (repeater of title + links), copyright … */ ],
        ],
    ],
];
```

### 4.2 Field types

| type | Admin input | Stored as | Render with |
|---|---|---|---|
| `text` | input | string | `@text($data['x'])` |
| `textarea` | textarea | string | `@text(...)` or `Cms::lines(...)` for line breaks |
| `rich` | small textarea | string with inline tags only | `{{ Cms::rich(...) }}` (whitelisted tags) |
| `html` | code textarea | raw HTML | `{!! $data['x'] !!}`, **trusted admins only** |
| `url` | input | string | `{{ Cms::url(...) }}` |
| `image` | input + upload button + preview | path like `storage/cms/x.webp` | `{{ Cms::asset(...) }}` |
| `icon` | input + live icon preview | `fa-solid fa-check` | `<i class="{{ $x }}">` |
| `select` | dropdown from `options` | string key | compare or print |
| `toggle` | checkbox | bool (stored only when true) | `! empty($data['x'])` |
| `number` | number input | int | — |
| `list` | textarea, one item per line | array of strings | `@foreach` |
| `repeater` | sortable cards with nested `fields` | array of objects | `@foreach` |

### 4.3 Field options

| option | Meaning |
|---|---|
| `key` | Key in the JSON (required) |
| `label` | Admin label (required) |
| `width` | `half` / `third` (grid column); default is full width |
| `group` | Puts the field in a collapsible panel ("Heading", "Layout"…) |
| `help` | Grey hint under the input |
| `options` | For `select`: `['value' => 'Label']` |
| `item_label` | For `repeater`: which item key to show as the card title |
| `fields` | For `repeater`: nested field list (repeaters can nest) |

### 4.4 Naming conventions

- Type keys use `snake_case` (`cta_banner`), and partial files use `kebab-case` (`sections/cta-banner.blade.php`).
- Reuse the same keys across types (`title`, `subtitle`, `items`, `buttons`, `background`, `anchor`). Shared Blade components then work everywhere.
- Every type has a `label`, `icon` and `description`, because the admin "Add section" picker shows them.

---

## 5. Backend

### 5.1 `App\Services\ContentSchema`: clean data against the schema

This is the safety net. Whatever the browser sends, only declared keys with normalised values are saved.

```php
class ContentSchema
{
    public function sectionTypes(): array { return config('cms.sections', []); }

    public function sectionType(string $type): array
    {
        return $this->sectionTypes()[$type] ?? throw new InvalidArgumentException("Unknown section type [{$type}].");
    }

    public function layoutSettings(): array { return config('cms.layout', []); }

    public function cleanSection(string $type, array $data): array
    {
        return $this->cleanObject($data, $this->sectionType($type)['fields']);
    }

    public function cleanLayout(string $key, array $value): array
    {
        $definition = $this->layoutSettings()[$key] ?? throw new InvalidArgumentException("Unknown layout setting [{$key}].");

        return ! empty($definition['list'])
            ? $this->cleanList($value, $definition['fields'])
            : $this->cleanObject($value, $definition['fields']);
    }

    protected function cleanObject(array $data, array $fields): array
    {
        $clean = [];
        foreach ($fields as $field) {
            if (! array_key_exists($field['key'], $data)) {
                continue;
            }
            $value = $this->cleanValue($data[$field['key']], $field);
            if ($value === null || $value === '' || $value === [] || $value === false) {
                continue; // keep JSON small: empty = absent
            }
            $clean[$field['key']] = $value;
        }

        return $clean;
    }

    protected function cleanList(array $items, array $fields): array
    {
        return array_values(array_filter(array_map(
            fn ($item) => is_array($item) ? $this->cleanObject($item, $fields) : [], $items,
        )));
    }

    protected function cleanValue(mixed $value, array $field): mixed
    {
        return match ($field['type']) {
            'toggle' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($value) ? (int) $value : null,
            'list' => is_array($value)
                ? array_values(array_filter(array_map(fn ($i) => trim((string) $i), $value), fn ($i) => $i !== ''))
                : [],
            'repeater' => is_array($value) ? $this->cleanList($value, $field['fields'] ?? []) : [],
            default => is_scalar($value) ? trim((string) $value) : null,
        };
    }
}
```

> Because empty values are dropped, partials must always read with a fallback: `$data['title'] ?? ''`, `! empty($data['items'])`.

### 5.2 `Section` model

```php
protected $fillable = ['page_id', 'shared_id', 'type', 'name', 'data', 'sort_order', 'is_active'];
protected function casts(): array { return ['data' => 'array', 'is_active' => 'boolean', 'sort_order' => 'integer']; }

protected static function booted(): void
{
    static::saved(fn () => Page::flushContentCache());
    static::deleted(fn () => Page::flushContentCache());
}

public function page(): BelongsTo { return $this->belongsTo(Page::class); }
public function shared(): BelongsTo { return $this->belongsTo(Section::class, 'shared_id'); }
public function usages(): HasMany { return $this->hasMany(Section::class, 'shared_id'); }
public function scopeLibrary(Builder $q): void { $q->whereNull('page_id'); }

// A linked row renders the SHARED section's type and data.
public function renderType(): string { return $this->shared?->type ?? $this->type; }
public function renderData(): array { return ($this->shared_id ? $this->shared?->data : $this->data) ?? []; }

public function isVisible(): bool
{
    if (! $this->is_active) {
        return false;
    }

    return ! $this->shared_id || ($this->shared && $this->shared->is_active);
}
```

### 5.3 `Page` model and caching

- `sections()` is ordered: `hasMany(Section::class)->orderBy('sort_order')->orderBy('id')`.
- `path()` returns `/` for `home` and `/{slug}` for everything else.
- **Cache with a version key.** Every save bumps the version, so you never need to know which keys to forget.

```php
public static function findForFrontend(string $slug): ?self
{
    $version = Cache::get('cms_content_version', 1);

    $cached = Cache::rememberForever("cms_page_{$slug}_v{$version}", function () use ($slug): ?array {
        $page = self::query()->active()->where('slug', $slug)
            ->with(['sections' => fn ($q) => $q->where('is_active', true)->with('shared')])
            ->first();

        return $page ? self::toCachePayload($page) : null;   // ARRAYS ONLY, see warning
    });

    return is_array($cached) ? self::fromCachePayload($cached) : null;
}

public static function flushContentCache(): void
{
    Cache::forever('cms_content_version', ((int) Cache::get('cms_content_version', 1)) + 1);
}
```

> ⚠️ **Never cache Eloquent models directly.** Laravel 13's `config/cache.php` ships with `'serializable_classes' => false`, so a cached model comes back as `__PHP_Incomplete_Class` and the site returns a 500 error. Tests won't catch it, because the `array` store doesn't serialize by default. Cache `$model->getAttributes()` arrays and rebuild with `(new Model)->newFromBuilder($attributes)` plus `setRelation()`. See `toCachePayload()` / `fromCachePayload()` in `app/Models/Page.php`.

Any other model whose changes appear on pages (posts, products…) should also call `Page::flushContentCache()` on save and delete.

### 5.4 `Setting` model (layout JSON)

- `Setting::get($key, $default)` and `Setting::json($key): array` read values.
- `Setting::set($key, $value, $group)` json-encodes arrays and sets `type = 'json'`.
- Load all settings once per request with `Cache::rememberForever` (an array of strings, which is safe). Memoise the result in the container (`app()->instance('settings.loaded', …)`), **not** in a `static` property, because a static property leaks between tests.

### 5.5 Controllers and routes

**Public:**
```php
Route::get('/', [PageController::class, 'show'])->name('home');
// …all other public routes (blog, contact, sitemap) FIRST…

// Catch-all LAST, with a reserved-word blacklist:
Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '^(?!admin|api|login|logout|register|user|storage|build|assets|up$)[a-z0-9-]+$')
    ->name('page');
```

```php
// Frontend\PageController
public function show(?string $slug = null): View
{
    $page = Page::findForFrontend($slug ?? Page::HOME_SLUG);
    abort_if(! $page, 404);

    $sections = $page->sections->filter(fn (Section $s) => $s->isVisible())->values();

    return view('frontend.page', compact('page', 'sections'));
}
```

**Admin** (inside the `auth` group + an admin-role check, prefix `admin`, name `admin.`):

| Method | URI | Action |
|---|---|---|
| GET/POST | `/pages` | list / create (the new page gets a starter hero section) |
| GET/PUT/DELETE | `/pages/{page}` | builder screen / save title, slug, SEO / delete (the home page cannot be deleted) |
| POST | `/pages/{page}/sections` | add a section: `type`, or `shared_id`, plus optional `after_id` |
| POST | `/pages/{page}/sections/reorder` | `ids[]` in the new order |
| PUT | `/sections/{section}` | save `name` + `data` (a linked row saves its **shared** section) |
| PATCH | `/sections/{section}/toggle` | show / hide |
| POST | `/sections/{section}/duplicate` | copy right below it |
| POST | `/sections/{section}/share` | move the content into the library, keep a link |
| POST | `/sections/{section}/detach` | turn a link into an editable local copy |
| DELETE | `/sections/{section}` | delete (deleting a library section removes it everywhere) |
| GET/POST | `/shared-sections` | library list (with "used on" pages) / create |
| GET/PUT | `/layout`, `/layout/{key}` | edit header / menus / footer (`{key}` must exist in `cms.layout`, otherwise 404) |
| POST | `/uploads` | image upload; returns `{ path: 'storage/cms/…' }` |

Key logic to copy from `app/Http/Controllers/Admin/SectionController.php`:
- **`update`** sets `$target = $section->shared_id ? $section->shared : $section`, then `$target->update(['data' => $schema->cleanSection($target->type, $request->data)])`.
- **`insertPosition($page, $afterId)`** shifts later rows (`sort_order > after`) by +1 and returns `after + 1`. Without an `after_id` it appends.
- **`share`** creates a library row with the same type, name and data, then sets `shared_id` on the page row and `data = null`.
- **`detach`** copies the shared data back into the row and sets `shared_id = null`.
- **`present($section)`** is the shape sent to Vue. It casts `data` to `(object)` so that an empty `{}` stays an object in JS, not `[]`.
- Validate page slugs with `regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/`, `Rule::notIn([...reserved])` and `Rule::unique`.

---

## 6. Frontend rendering (Blade)

### 6.1 The page view is just a loop

```blade
{{-- resources/views/frontend/page.blade.php --}}
@extends('layouts.frontend')

@section('meta_title', $page->meta_title ?: $page->title)
@section('meta_description', (string) $page->meta_description)

@section('content')
  @foreach($sections as $section)
    @includeIf('sections.'.str_replace('_', '-', $section->renderType()), [
        'data' => $section->renderData(),
        'section' => $section,
        'page' => $page,
    ])
  @endforeach
@endsection
```

`@includeIf` means an unknown or removed type renders nothing instead of crashing.

### 6.2 A section partial

```blade
{{-- resources/views/sections/projects.blade.php --}}
<x-cms.section :data="$data" class="projects">
  <div class="project-grid cols-{{ $data['columns'] ?? 3 }}">
    @foreach($data['items'] ?? [] as $item)
      <article class="project-card reveal">
        @if(! empty($item['image']))
          <img src="{{ Cms::asset($item['image']) }}" alt="{{ $item['title'] ?? '' }}" loading="lazy">
        @endif
        <h3>@text($item['title'] ?? '')</h3>
        <p>@text($item['summary'] ?? '')</p>
        @if(! empty($item['tags']))
          <ul class="tags">@foreach($item['tags'] as $tag)<li>@text($tag)</li>@endforeach</ul>
        @endif
        @if(! empty($item['url']))
          <a href="{{ Cms::url($item['url']) }}">View project</a>
        @endif
      </article>
    @endforeach
  </div>
</x-cms.section>
```

### 6.3 Shared Blade components (`resources/views/components/cms/`)

Build these once and reuse them in every partial:
- **`section`**: the `<section class="section {bg}" id="{anchor}"><div class="container">` wrapper, plus the heading.
- **`head`**: eyebrow, title (with an optional highlighted phrase) and subtitle.
- **`button`** / **`buttons`**: render a `buttons` repeater (style, icon, new tab with `rel="noopener"`).
- Any repeated card (plan card, FAQ accordion…).

### 6.4 The `Cms` helper (`App\Services\Cms`, aliased as `Cms`)

| Helper | Purpose |
|---|---|
| `Cms::text($v)` | escape plain text (also a Blade directive: `@text($v)`) |
| `Cms::lines($v)` | escape, then turn newlines into `<br>` |
| `Cms::rich($v)` | allow only `<a><b><strong><em><i><code><br><span>`; strip `on*=` and `javascript:` |
| `Cms::highlight($title, $phrase)` | wrap one phrase of a heading in `<span class="text-gradient">` |
| `Cms::url($v)` | leave `http…`, `#`, `mailto:` and `tel:` as-is; make others site-relative; empty becomes `#` |
| `Cms::asset($v)` | turn a stored path into a public URL |
| `Cms::isCurrent($url)` | true when the link is the current page, used for active menu items and `aria-current="page"` |
| `Cms::setting($key)` / `Cms::layout($key)` | read a global setting / a layout JSON setting |

Register them in `AppServiceProvider::boot()`:
```php
AliasLoader::getInstance()->alias('Cms', \App\Services\Cms::class);
Blade::directive('text', fn ($e) => "<?php echo \\App\\Services\\Cms::text({$e}); ?>");
```

### 6.5 Header, footer and menu come from layout settings

```blade
{{-- partials/header.blade.php --}}
@php($menu = Cms::layout('menu_primary'))
<nav>
  <ul>
    @foreach($menu as $item)
      @php($current = Cms::isCurrent($item['url'] ?? '') || collect($item['children'] ?? [])->contains(fn ($c) => Cms::isCurrent($c['url'] ?? '')))
      <li class="nav-item{{ $current ? ' is-current' : '' }}">
        <a href="{{ Cms::url($item['url'] ?? '') }}"@if($current) aria-current="page"@endif>@text($item['label'] ?? '')</a>
        …children…
      </li>
    @endforeach
  </ul>
</nav>
```

The menu order is the list order, and the admin repeater has up and down arrows to change it.

---

## 7. Admin UI (Inertia + Vue)

Copy `resources/js/Components/Cms/` from the reference project. These four generic components render **any** schema:

| Component | Job |
|---|---|
| `SchemaForm.vue` | Takes `fields` + `modelValue` (a reactive object). Groups fields by `group` into collapsible panels and uses a 6-column grid (`half` = 3 columns, `third` = 2). |
| `SchemaField.vue` | One input per field type. `list` is edited as a textarea with one item per line. `image` uploads via `fetch(route('admin.uploads.store'))` with the `X-XSRF-TOKEN` cookie. `icon` shows a live preview. |
| `RepeaterField.vue` | A list of collapsible cards (title from `item_label`) with add, move up/down, duplicate and remove. Each item is a nested `SchemaForm`, so repeaters nest. |
| `SectionEditor.vue` | Slide-over panel. It deep-copies `section.shared ?? section` data, then `router.put(route('admin.sections.update', id), { name, data })`. It shows a "Shared · used on N pages" badge. |

Screens:
- **Pages / Index**: list of pages with status, section count, "view" link and "create page".
- **Pages / Edit (builder)**: section cards in order. Each card has an icon, name, type and a shared badge, plus buttons for ↑ ↓, Edit, Hide/Show, Duplicate, Share/Detach and Delete. A "+ Add section here" button between cards opens a picker with two tabs: **New block** (grid of types from the schema, showing label, icon and description) and **Shared** (the library). A **Settings & SEO** tab covers the title, slug, meta and draft status.
- **Shared Sections**: library list showing which pages use each item, plus create and edit.
- **Site Layout**: one card per `cms.layout` key, each a `SchemaForm` with a Save button (`PUT admin.layout.update`).

To reorder, use simple arrow buttons that send the full `ids[]` list. They are reliable and accessible, and need no drag-and-drop library. Add drag-and-drop later only if a client asks.

> Using Livewire, Filament or React instead? Keep everything else the same. Only rewrite these four components: a schema-driven form renderer is about 300 lines in any stack.

---

## 8. Default content (seeding)

- Put the default content in JSON files: `database/seeders/data/pages.json`, `shared_sections.json` and `layout.json`.
  ```json
  [{ "slug": "home", "title": "Home", "meta_title": "…", "sections": [
      { "type": "hero", "name": "Hero", "data": { "title": "Hi, I'm …", "buttons": [{ "label": "Hire me", "url": "#contact" }] } },
      { "shared": "testimonials" },
      { "type": "projects", "data": { "items": [ … ] } }
  ]}]
  ```
- `CmsContentSeeder` must be **insert-only**: skip pages whose slug already exists, use `firstOrCreate` for library sections, and only `set` a layout key when it is missing. Re-running seeders then never destroys a client's edits.
- When converting an existing static design (an `html/` folder or a theme), write a one-off extraction script, kept out of the repo, that parses each HTML page into section JSON. Then **diff the rendered DOM** against the original HTML until they match. The design stays pixel-identical and every text becomes editable.

---

## 9. Security checklist

- [ ] Admin routes check an **admin role**, not just `auth`. Turn public registration off, or give new users a non-admin role by default.
- [ ] Every text output goes through `@text` / `Cms::text`. Use `{!! !!}` only for `html`-type fields and `Cms::rich`.
- [ ] `html` fields (custom HTML, header/footer scripts) are effectively "run any code on the site". Only trusted admins should have access.
- [ ] Uploads: validate `mimes` + `max`. **Do not allow SVG** unless you sanitise it, because an uploaded SVG can carry JavaScript (XSS) on your domain.
- [ ] `ContentSchema` drops unknown keys, and unknown section types and layout keys are rejected (422/404).
- [ ] Catch-all slug route is last. Reserved slugs are blocked in validation **and** in the route regex.
- [ ] Rate-limit public form endpoints (`throttle:10,1`).

---

## 10. Tests to write (PHPUnit feature tests)

These cover the behaviour that matters, so copy and adapt them from `tests/Feature/CmsPageBuilderTest.php`:

1. A page renders only its **visible** sections, **in order** (`assertSeeInOrder`).
2. Draft pages and unknown slugs return **404**.
3. Text is **escaped** (`<script>` shows as `&lt;script&gt;`).
4. Admin save **drops unknown fields** and normalises types (toggle → bool).
5. A **shared** section edited once changes on **every** linked page.
6. **Share then detach** keeps the content.
7. **Reorder** changes the render order, and **insert after** lands in the right position.
8. **Reserved slugs** are rejected, and a valid new page is reachable.
9. A menu saved in Layout renders in the header with the **current-page** state.
10. An unknown layout key returns **404**, and **guests** cannot change content.
11. **Cache with a serializing store**: run `config(['cache.stores.array.serialize' => true, 'cache.serializable_classes' => false]); Cache::forgetDriver('array');`, request the page twice, and assert it renders.

Factories: `PageFactory` (`draft()` state) and `SectionFactory` (a simple default type, plus `library()` and `hidden()` states).

---

## 11. Step-by-step: set it up in a new project

1. **Migrations**: `pages`, `sections`, `settings` (section 3).
2. **Models + factories**: `Page` (cache payload as arrays), `Section`, `Setting` (section 5).
3. **`config/cms.php`**: start with 5–8 types. A portfolio needs about: `hero`, `about`/`split`, `services`/`features`, `projects`, `skills`/`stats`, `testimonials`, `faq`, `cta_banner`, `contact`, `custom_html`. Add layout keys: `menu_primary`, `layout_header`, `layout_footer`, `social_links`.
4. **`ContentSchema` + `Cms` services**, alias + `@text` directive.
5. **Frontend**: layout file (header/footer partials reading layout settings), `frontend/page.blade.php` loop, `components/cms/*`, one partial per type. Routes: home, other public routes, then the catch-all last.
6. **Admin**: controllers + routes (section 5.5), the four Vue components, and the four screens (section 7). Add a nav entry.
7. **Seeders**: JSON default content, insert-only.
8. **Tests** (section 10), then `pint`, `npm run build`.
9. **Verify in a browser**: every page loads, edit a section and see the change, hide/reorder/share, and check the menu's active state at mobile width.

## 12. Recipe: add a new section type (about 10 minutes)

1. Add `'my_block' => ['label', 'icon', 'description', 'fields' => […]]` to `config/cms.php`, reusing `$head`, `$buttons()` and `$layout`.
2. Create `resources/views/sections/my-block.blade.php`, using `<x-cms.section>` and reading every key with a fallback.
3. Add its CSS to the site stylesheet. It must be responsive, so check 360px, 768px and 1280px.
4. Open the admin, then **Add section → My block**. The form appears automatically, with no Vue or controller changes.

---

## 13. Pitfalls already hit (save yourself the time)

| Problem | Cause | Fix |
|---|---|---|
| 500: `__PHP_Incomplete_Class returned` | Cached an Eloquent model; `serializable_classes => false` | Cache arrays only (section 5.3) |
| Blade directive printed as text (`@endif@if`, `<td@if(...)`) | Blade needs a non-word character before `@` | Use `{{ $cond ? ' attr' : '' }}` or put a space/newline before the directive |
| `&amp;amp;` in `<title>` | `@section('x', $value)` already escapes, and `@yield` escaped again | Decode once in the layout, then print escaped: `$title = trim(html_entity_decode($__env->yieldContent('meta_title'), ENT_QUOTES \| ENT_HTML5, 'UTF-8'));` then `<title>{{ $title }}</title>` |
| Empty `{}` data becomes `[]` in Vue, so new keys are lost | PHP empty array → JSON array | Cast `(object) $data` when sending to Inertia |
| Settings memo leaks between tests | `static` property cache | Memoise in the container (`app()->instance`) |
| Page slug clashes with `/admin`, `/login`… | Catch-all route | Reserved list in the route regex **and** in validation |
| Edited content not showing | Stale cache | Every content model calls `Page::flushContentCache()` on save/delete; reorder (mass update) calls it manually |
| Slow first paint | CDN font/icon `@import` chains | Self-host fonts and icons in `public/assets`, preload the 2–3 critical woff2 files and the hero image, add `?v=filemtime` to CSS/JS |

---

## 14. Reference files (Host Divine project)

| Area | Path |
|---|---|
| Schema | `config/cms.php` |
| Services | `app/Services/ContentSchema.php`, `app/Services/Cms.php` |
| Models | `app/Models/Page.php`, `Section.php`, `Setting.php` |
| Admin controllers | `app/Http/Controllers/Admin/PageController.php`, `SectionController.php`, `SharedSectionController.php`, `LayoutController.php`, `UploadController.php` |
| Public controller | `app/Http/Controllers/Frontend/PageController.php` |
| Routes | `routes/web.php` (admin group + catch-all at the bottom) |
| Blade | `resources/views/frontend/page.blade.php`, `resources/views/sections/*`, `resources/views/components/cms/*`, `resources/views/partials/header.blade.php` / `footer.blade.php` |
| Vue | `resources/js/Components/Cms/*`, `resources/js/Pages/Admin/Pages/Edit.vue`, `Admin/SharedSections/Index.vue`, `Admin/Layout/Index.vue` |
| Seeding | `database/seeders/CmsContentSeeder.php`, `database/seeders/data/*.json` |
| Tests | `tests/Feature/CmsPageBuilderTest.php` |

---

## 15. Prompt for an AI coding agent

Paste this into a new project, together with this file:

> Implement a section-based page builder by following `instruction/section-based-page-builder.md` exactly. Use only the 3 tables (`pages`, `sections`, `settings`), a `config/cms.php` schema that drives both the admin forms and `ContentSchema` cleaning, shared library sections via `shared_id`, and header/menu/footer as JSON layout settings. Cache pages as arrays, never models. The frontend markup must match my design in `<path to design>` exactly. Create one section type per distinct block in that design and seed the design's current content as the defaults (insert-only seeder). Write the tests listed in section 10 and make them pass.
