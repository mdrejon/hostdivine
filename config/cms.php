<?php

/*
|--------------------------------------------------------------------------
| CMS section & layout schemas
|--------------------------------------------------------------------------
|
| Every page is an ordered list of sections. Each section "type" below maps
| to resources/views/sections/{type-with-dashes}.blade.php and declares the
| fields the admin editor shows. The same field format describes the
| structured site-wide settings (header, menus, footer, widgets).
|
| Field types: text, textarea, rich (inline HTML: links/bold), html (block
| HTML), url, icon (Font Awesome classes), image, select, toggle, number,
| list (list of strings) and repeater (list of objects with "fields").
|
*/

$backgrounds = [
    '' => 'None (white)',
    'bg-soft' => 'Soft blue',
    'bg-soft is-tight' => 'Soft blue (compact)',
    'bg-grid' => 'Grid pattern',
    'bg-waves' => 'Waves',
    'bg-circuit' => 'Circuit',
    'bg-globe' => 'Globe',
    'bg-hex' => 'Hexagons',
    'bg-mesh' => 'Mesh',
    'bg-photo is-dark' => 'Dark photo',
];

$buttonFields = [
    ['key' => 'label', 'label' => 'Label', 'type' => 'text', 'width' => 'half'],
    ['key' => 'url', 'label' => 'Link', 'type' => 'url', 'width' => 'half'],
    ['key' => 'style', 'label' => 'Style', 'type' => 'select', 'width' => 'half', 'options' => ['primary' => 'Primary (blue)', 'ghost' => 'Ghost (on dark)', 'outline' => 'Outline', 'light' => 'Light']],
    ['key' => 'icon', 'label' => 'Icon after label', 'type' => 'icon', 'width' => 'half'],
    ['key' => 'new_tab', 'label' => 'Open in new tab', 'type' => 'toggle'],
];

$buttons = fn (string $key = 'buttons', string $label = 'Buttons') => ['key' => $key, 'label' => $label, 'type' => 'repeater', 'item_label' => 'label', 'fields' => $buttonFields];

$section = [
    ['key' => 'background', 'label' => 'Background', 'type' => 'select', 'options' => $backgrounds, 'width' => 'half', 'group' => 'Layout'],
    ['key' => 'anchor', 'label' => 'Anchor ID', 'type' => 'text', 'width' => 'half', 'group' => 'Layout', 'help' => 'Used for in-page links such as #plans.'],
    ['key' => 'css_class', 'label' => 'Extra CSS class', 'type' => 'text', 'width' => 'half', 'group' => 'Layout'],
];

$head = [
    ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'width' => 'half', 'group' => 'Heading'],
    ['key' => 'eyebrow_dark', 'label' => 'Light eyebrow (for dark backgrounds)', 'type' => 'toggle', 'width' => 'half', 'group' => 'Heading'],
    ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'width' => 'half', 'group' => 'Heading'],
    ['key' => 'title_highlight', 'label' => 'Highlighted words', 'type' => 'text', 'width' => 'half', 'group' => 'Heading', 'help' => 'Part of the title shown in the blue gradient.'],
    ['key' => 'subtitle', 'label' => 'Subtitle', 'type' => 'textarea', 'group' => 'Heading'],
];

$extras = [
    ['key' => 'logos', 'label' => 'Logo strip', 'type' => 'repeater', 'item_label' => 'alt', 'group' => 'Extras', 'fields' => [
        ['key' => 'image', 'label' => 'Logo', 'type' => 'image', 'width' => 'half'],
        ['key' => 'alt', 'label' => 'Name (alt text)', 'type' => 'text', 'width' => 'half'],
    ]],
    ['key' => 'note_icon', 'label' => 'Note icon', 'type' => 'icon', 'width' => 'half', 'group' => 'Extras'],
    ['key' => 'note', 'label' => 'Note below the content', 'type' => 'rich', 'group' => 'Extras'],
    ['key' => 'note_reveal', 'label' => 'Animate note on scroll', 'type' => 'toggle', 'group' => 'Extras'],
    $buttons('actions', 'Centred buttons') + ['group' => 'Extras'],
];

$planFields = [
    ['key' => 'kind', 'label' => 'Card type', 'type' => 'select', 'width' => 'half', 'options' => ['plan' => 'Price plan', 'cta' => 'Link card']],
    ['key' => 'featured', 'label' => 'Highlighted (recommended)', 'type' => 'toggle', 'width' => 'half'],
    ['key' => 'name', 'label' => 'Plan name', 'type' => 'text', 'width' => 'half'],
    ['key' => 'category', 'label' => 'Category label', 'type' => 'text', 'width' => 'half'],
    ['key' => 'category_style', 'label' => 'Category style', 'type' => 'select', 'width' => 'half', 'options' => ['' => 'Default', 'is-cloud' => 'Cloud (blue)']],
    ['key' => 'badge', 'label' => 'Badge', 'type' => 'text', 'width' => 'half', 'help' => 'e.g. Recommended, Popular'],
    ['key' => 'badge_style', 'label' => 'Badge style', 'type' => 'select', 'width' => 'half', 'options' => ['' => 'Default', 'is-blue' => 'Blue']],
    ['key' => 'description', 'label' => 'Short description', 'type' => 'textarea'],
    ['key' => 'currency', 'label' => 'Currency', 'type' => 'text', 'width' => 'third', 'help' => '$ or ৳'],
    ['key' => 'price', 'label' => 'Price', 'type' => 'text', 'width' => 'third'],
    ['key' => 'period', 'label' => 'Period', 'type' => 'text', 'width' => 'third', 'help' => '/mo, /yr'],
    ['key' => 'billed', 'label' => 'Billing note', 'type' => 'text'],
    ['key' => 'button_label', 'label' => 'Button label', 'type' => 'text', 'width' => 'half'],
    ['key' => 'button_url', 'label' => 'Order link', 'type' => 'url', 'width' => 'half'],
    ['key' => 'features', 'label' => 'Features', 'type' => 'list', 'help' => 'Start a line with "- " to show it as not included.'],
    ['key' => 'icon', 'label' => 'Icon (link card)', 'type' => 'icon', 'width' => 'half'],
    ['key' => 'links', 'label' => 'Links (link card)', 'type' => 'repeater', 'item_label' => 'label', 'fields' => [
        ['key' => 'label', 'label' => 'Label', 'type' => 'text', 'width' => 'half'],
        ['key' => 'url', 'label' => 'Link', 'type' => 'url', 'width' => 'half'],
    ]],
];

$faqItems = ['key' => 'items', 'label' => 'Questions', 'type' => 'repeater', 'item_label' => 'question', 'fields' => [
    ['key' => 'question', 'label' => 'Question', 'type' => 'text'],
    ['key' => 'answer', 'label' => 'Answer', 'type' => 'html', 'help' => 'HTML allowed, e.g. <p>…</p>.'],
]];

$iconTitleText = [
    ['key' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'width' => 'half'],
    ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'width' => 'half'],
    ['key' => 'text', 'label' => 'Text', 'type' => 'textarea'],
];

$linkFields = [
    ['key' => 'label', 'label' => 'Label', 'type' => 'text', 'width' => 'half'],
    ['key' => 'url', 'label' => 'Link', 'type' => 'url', 'width' => 'half'],
    ['key' => 'new_tab', 'label' => 'Open in new tab', 'type' => 'toggle'],
];

return [

    'backgrounds' => $backgrounds,

    'sections' => [

        'page_hero' => [
            'label' => 'Page hero',
            'icon' => 'fa-solid fa-panorama',
            'description' => 'Inner page header with breadcrumb, buttons, badges and an optional summary console.',
            'fields' => [
                ['key' => 'breadcrumbs', 'label' => 'Breadcrumb (between Home and the page)', 'type' => 'repeater', 'item_label' => 'label', 'group' => 'Breadcrumb', 'fields' => [
                    ['key' => 'label', 'label' => 'Label', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'url', 'label' => 'Link (optional)', 'type' => 'url', 'width' => 'half'],
                ]],
                ['key' => 'breadcrumb_current', 'label' => 'Current page label', 'type' => 'text', 'group' => 'Breadcrumb'],
                ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'group' => 'Content'],
                ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'width' => 'half', 'group' => 'Content'],
                ['key' => 'title_highlight', 'label' => 'Highlighted words', 'type' => 'text', 'width' => 'half', 'group' => 'Content'],
                ['key' => 'lead', 'label' => 'Lead text', 'type' => 'textarea', 'group' => 'Content'],
                $buttons() + ['group' => 'Content'],
                ['key' => 'badges', 'label' => 'Badges', 'type' => 'repeater', 'item_label' => 'text', 'group' => 'Content', 'fields' => [
                    ['key' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'width' => 'half'],
                    ['key' => 'text', 'label' => 'Text', 'type' => 'text', 'width' => 'half'],
                ]],
                ['key' => 'centered', 'label' => 'Centred layout', 'type' => 'toggle', 'width' => 'half', 'group' => 'Layout'],
                ['key' => 'domain_search', 'label' => 'Show domain search', 'type' => 'select', 'width' => 'half', 'group' => 'Layout', 'options' => ['' => 'No', 'register' => 'Yes — register mode', 'transfer' => 'Yes — transfer mode']],
                ['key' => 'anchor', 'label' => 'Anchor ID', 'type' => 'text', 'width' => 'half', 'group' => 'Layout'],
                ['key' => 'console_badge', 'label' => 'Console badge', 'type' => 'text', 'group' => 'Summary console'],
                ['key' => 'console_rows', 'label' => 'Console rows (leave empty to hide the console)', 'type' => 'repeater', 'item_label' => 'label', 'group' => 'Summary console', 'fields' => [
                    ['key' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'width' => 'third'],
                    ['key' => 'label', 'label' => 'Label', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'value', 'label' => 'Value', 'type' => 'text', 'width' => 'third'],
                ]],
                ['key' => 'console_price_label', 'label' => 'Price label', 'type' => 'text', 'width' => 'third', 'group' => 'Summary console'],
                ['key' => 'console_price', 'label' => 'Price', 'type' => 'text', 'width' => 'third', 'group' => 'Summary console'],
                ['key' => 'console_period', 'label' => 'Period', 'type' => 'text', 'width' => 'third', 'group' => 'Summary console'],
                ['key' => 'console_button_label', 'label' => 'Button label', 'type' => 'text', 'width' => 'half', 'group' => 'Summary console'],
                ['key' => 'console_button_url', 'label' => 'Button link', 'type' => 'url', 'width' => 'half', 'group' => 'Summary console'],
            ],
        ],

        'hero_slider' => [
            'label' => 'Hero slider',
            'icon' => 'fa-solid fa-images',
            'description' => 'Homepage slider with tabs, buttons, price and a visual per slide.',
            'fields' => [
                ['key' => 'label', 'label' => 'Slider name (screen readers)', 'type' => 'text', 'width' => 'half'],
                ['key' => 'anchor', 'label' => 'Anchor ID', 'type' => 'text', 'width' => 'half'],
                ['key' => 'slides', 'label' => 'Slides', 'type' => 'repeater', 'item_label' => 'tab_label', 'fields' => [
                    ['key' => 'tab_label', 'label' => 'Tab label', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'title_highlight', 'label' => 'Highlighted words', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'text', 'label' => 'Text', 'type' => 'textarea'],
                    ['key' => 'points', 'label' => 'Check points', 'type' => 'list'],
                    $buttons(),
                    ['key' => 'price_label', 'label' => 'Price label', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'price', 'label' => 'Price', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'period', 'label' => 'Period', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'visual', 'label' => 'Visual', 'type' => 'select', 'width' => 'half', 'options' => ['photo' => 'Image', 'dashboard' => 'Reseller dashboard mock-up', 'domain' => 'Domain search mock-up', 'server' => 'Server rack & terminal']],
                    ['key' => 'image', 'label' => 'Image (for "Image" visual)', 'type' => 'image', 'width' => 'half'],
                    ['key' => 'image_alt', 'label' => 'Image alt text', 'type' => 'text'],
                    ['key' => 'chips', 'label' => 'Floating chips', 'type' => 'repeater', 'item_label' => 'title', 'fields' => [
                        ['key' => 'position', 'label' => 'Position', 'type' => 'select', 'width' => 'half', 'options' => ['tl' => 'Top left', 'tr' => 'Top right', 'bl' => 'Bottom left', 'br' => 'Bottom right']],
                        ['key' => 'color', 'label' => 'Icon colour', 'type' => 'select', 'width' => 'half', 'options' => ['blue' => 'Blue', 'green' => 'Green']],
                        ['key' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'width' => 'third'],
                        ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'width' => 'third'],
                        ['key' => 'text', 'label' => 'Text', 'type' => 'text', 'width' => 'third'],
                    ]],
                ]],
            ],
        ],

        'domain_search' => [
            'label' => 'Domain search',
            'icon' => 'fa-solid fa-globe',
            'description' => 'Heading plus the shared domain search card (chips come from Domain Pricing).',
            'fields' => [
                ['key' => 'badge', 'label' => 'Badge', 'type' => 'text', 'width' => 'half'],
                ['key' => 'badge_highlight', 'label' => 'Badge highlight', 'type' => 'text', 'width' => 'half'],
                ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'width' => 'half'],
                ['key' => 'title_highlight', 'label' => 'Highlighted words', 'type' => 'text', 'width' => 'half'],
                ['key' => 'subtitle', 'label' => 'Subtitle', 'type' => 'textarea'],
                ['key' => 'mode', 'label' => 'Default mode', 'type' => 'select', 'width' => 'half', 'options' => ['register' => 'Register', 'transfer' => 'Transfer']],
                ['key' => 'anchor', 'label' => 'Anchor ID', 'type' => 'text', 'width' => 'half'],
            ],
        ],

        'stats_float' => [
            'label' => 'Stats bar',
            'icon' => 'fa-solid fa-chart-simple',
            'description' => 'Floating bar of key numbers under a page hero.',
            'fields' => [
                ['key' => 'items', 'label' => 'Stats', 'type' => 'repeater', 'item_label' => 'label', 'fields' => [
                    ['key' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'width' => 'third'],
                    ['key' => 'value', 'label' => 'Value', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'label', 'label' => 'Label', 'type' => 'text', 'width' => 'third'],
                ]],
            ],
        ],

        'pricing' => [
            'label' => 'Pricing plans',
            'icon' => 'fa-solid fa-tags',
            'description' => 'Grid of plan cards, with an optional price table below.',
            'fields' => [
                ...$section, ...$head,
                ['key' => 'columns', 'label' => 'Cards per row', 'type' => 'select', 'width' => 'half', 'options' => ['2' => '2', '3' => '3', '4' => '4'], 'group' => 'Plans'],
                ['key' => 'plans', 'label' => 'Plans', 'type' => 'repeater', 'item_label' => 'name', 'group' => 'Plans', 'fields' => $planFields],
                ['key' => 'table_label', 'label' => 'Table name', 'type' => 'text', 'width' => 'half', 'group' => 'Price table'],
                ['key' => 'table_caption', 'label' => 'Table caption', 'type' => 'text', 'width' => 'half', 'group' => 'Price table'],
                ['key' => 'table_headers', 'label' => 'Column headings', 'type' => 'list', 'group' => 'Price table'],
                ['key' => 'table_rows', 'label' => 'Rows', 'type' => 'repeater', 'group' => 'Price table', 'fields' => [
                    ['key' => 'cells', 'label' => 'Cells (first cell = extension)', 'type' => 'list'],
                ]],
                ...$extras,
            ],
        ],

        'pricing_tabs' => [
            'label' => 'Pricing plans (tabs)',
            'icon' => 'fa-solid fa-table-columns',
            'description' => 'Tabbed groups of plan cards shown as a grid or a slider.',
            'fields' => [
                ...$section, ...$head,
                ['key' => 'tabs_label', 'label' => 'Tab list name (screen readers)', 'type' => 'text', 'width' => 'half', 'group' => 'Tabs'],
                ['key' => 'layout', 'label' => 'Layout', 'type' => 'select', 'width' => 'half', 'group' => 'Tabs', 'options' => ['grid' => 'Grid', 'slider' => 'Slider']],
                ['key' => 'columns', 'label' => 'Cards per row (grid)', 'type' => 'select', 'width' => 'half', 'group' => 'Tabs', 'options' => ['2' => '2', '3' => '3', '4' => '4']],
                ['key' => 'tabs', 'label' => 'Tabs', 'type' => 'repeater', 'item_label' => 'label', 'group' => 'Tabs', 'fields' => [
                    ['key' => 'id', 'label' => 'Tab ID', 'type' => 'text', 'width' => 'third', 'help' => 'Unique, e.g. gold'],
                    ['key' => 'label', 'label' => 'Label', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'suffix', 'label' => 'Suffix', 'type' => 'text', 'width' => 'third', 'help' => 'Hidden on mobile'],
                    ['key' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'width' => 'third'],
                    ['key' => 'slider_label', 'label' => 'Slider name', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'nav_label', 'label' => 'Arrow label', 'type' => 'text', 'width' => 'third', 'help' => 'e.g. gold plans'],
                    ['key' => 'plans', 'label' => 'Plans', 'type' => 'repeater', 'item_label' => 'name', 'fields' => $planFields],
                ]],
                ...$extras,
            ],
        ],

        'highlights' => [
            'label' => 'Feature highlights',
            'icon' => 'fa-solid fa-star',
            'description' => 'Grid of icon cards, with optional logo strip.',
            'fields' => [
                ...$section, ...$head,
                ['key' => 'columns', 'label' => 'Cards per row', 'type' => 'select', 'width' => 'half', 'group' => 'Cards', 'options' => ['3' => '3', '4' => '4']],
                ['key' => 'items', 'label' => 'Cards', 'type' => 'repeater', 'item_label' => 'title', 'group' => 'Cards', 'fields' => $iconTitleText],
                ...$extras,
            ],
        ],

        'specs' => [
            'label' => 'Technical specifications',
            'icon' => 'fa-solid fa-list-check',
            'description' => 'Groups of label / value rows.',
            'fields' => [
                ...$section, ...$head,
                ['key' => 'groups', 'label' => 'Groups', 'type' => 'repeater', 'item_label' => 'title', 'group' => 'Specs', 'fields' => [
                    ['key' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'width' => 'half'],
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'rows', 'label' => 'Rows', 'type' => 'repeater', 'item_label' => 'label', 'fields' => [
                        ['key' => 'label', 'label' => 'Label', 'type' => 'text', 'width' => 'half'],
                        ['key' => 'value', 'label' => 'Value', 'type' => 'text', 'width' => 'half'],
                    ]],
                ]],
                ...$extras,
            ],
        ],

        'steps' => [
            'label' => 'Steps',
            'icon' => 'fa-solid fa-shoe-prints',
            'description' => 'Numbered process steps.',
            'fields' => [
                ...$section, ...$head,
                ['key' => 'items', 'label' => 'Steps', 'type' => 'repeater', 'item_label' => 'title', 'group' => 'Steps', 'fields' => $iconTitleText],
                ...$extras,
            ],
        ],

        'split' => [
            'label' => 'Split content',
            'icon' => 'fa-solid fa-table-columns',
            'description' => 'Text with check list on the left, info card on the right.',
            'fields' => [
                ...$section, ...$head,
                ['key' => 'checklist', 'label' => 'Check list', 'type' => 'list', 'group' => 'Heading'],
                $buttons() + ['group' => 'Heading'],
                ['key' => 'card_dark', 'label' => 'Dark card', 'type' => 'toggle', 'width' => 'half', 'group' => 'Info card'],
                ['key' => 'card_icon', 'label' => 'Icon', 'type' => 'icon', 'width' => 'half', 'group' => 'Info card'],
                ['key' => 'card_title', 'label' => 'Title', 'type' => 'text', 'group' => 'Info card'],
                ['key' => 'card_text', 'label' => 'Text', 'type' => 'textarea', 'group' => 'Info card'],
                ['key' => 'card_list', 'label' => 'List style', 'type' => 'select', 'width' => 'half', 'group' => 'Info card', 'options' => ['price' => 'Label / value rows', 'check' => 'Icon list']],
                ['key' => 'card_items', 'label' => 'List items', 'type' => 'repeater', 'item_label' => 'label', 'group' => 'Info card', 'fields' => [
                    ['key' => 'icon', 'label' => 'Icon (icon list)', 'type' => 'icon', 'width' => 'third'],
                    ['key' => 'label', 'label' => 'Label', 'type' => 'rich', 'width' => 'third'],
                    ['key' => 'value', 'label' => 'Value (rows)', 'type' => 'text', 'width' => 'third'],
                ]],
            ],
        ],

        'compare' => [
            'label' => 'Comparison table',
            'icon' => 'fa-solid fa-table',
            'description' => 'Plan comparison with yes / no cells. Type "yes" or "no" for icons.',
            'fields' => [
                ...$section, ...$head,
                ['key' => 'caption', 'label' => 'Table name', 'type' => 'text', 'width' => 'half', 'group' => 'Table'],
                ['key' => 'first_header', 'label' => 'First column heading', 'type' => 'text', 'width' => 'half', 'group' => 'Table'],
                ['key' => 'footer_label', 'label' => 'Footer row label', 'type' => 'text', 'width' => 'half', 'group' => 'Table'],
                ['key' => 'columns', 'label' => 'Plans (columns)', 'type' => 'repeater', 'item_label' => 'name', 'group' => 'Table', 'fields' => [
                    ['key' => 'name', 'label' => 'Name', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'price', 'label' => 'Price', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'period', 'label' => 'Period', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'button_label', 'label' => 'Button', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'button_url', 'label' => 'Button link', 'type' => 'url', 'width' => 'third'],
                    ['key' => 'featured', 'label' => 'Highlighted', 'type' => 'toggle', 'width' => 'third'],
                ]],
                ['key' => 'rows', 'label' => 'Rows', 'type' => 'repeater', 'item_label' => 'label', 'group' => 'Table', 'fields' => [
                    ['key' => 'label', 'label' => 'Feature', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'group', 'label' => 'Group heading row', 'type' => 'toggle', 'width' => 'half'],
                    ['key' => 'values', 'label' => 'Values (one per plan, in column order)', 'type' => 'list'],
                ]],
                ...$extras,
            ],
        ],

        'tld_table' => [
            'label' => 'Domain price table',
            'icon' => 'fa-solid fa-table-list',
            'description' => 'Live prices from Domain Pricing: popular list, renewals or the full searchable list.',
            'fields' => [
                ...$section, ...$head,
                ['key' => 'mode', 'label' => 'List', 'type' => 'select', 'width' => 'half', 'group' => 'Table', 'options' => ['popular' => 'Popular (register + transfer)', 'renew' => 'Renewal prices', 'all' => 'All extensions (searchable)']],
                ['key' => 'limit', 'label' => 'Rows before "Show all" (all extensions)', 'type' => 'number', 'width' => 'half', 'group' => 'Table'],
                ['key' => 'caption', 'label' => 'Table name', 'type' => 'text', 'group' => 'Table'],
                ['key' => 'action_label', 'label' => 'Row button label', 'type' => 'text', 'width' => 'half', 'group' => 'Table'],
                ['key' => 'action_url', 'label' => 'Row button link', 'type' => 'url', 'width' => 'half', 'group' => 'Table'],
                ...$extras,
            ],
        ],

        'timeline' => [
            'label' => 'Timeline',
            'icon' => 'fa-solid fa-timeline',
            'description' => 'Company milestones.',
            'fields' => [
                ...$section, ...$head,
                ['key' => 'items', 'label' => 'Milestones', 'type' => 'repeater', 'item_label' => 'year', 'group' => 'Milestones', 'fields' => [
                    ['key' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'width' => 'half'],
                    ['key' => 'year', 'label' => 'Year / label', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text'],
                    ['key' => 'text', 'label' => 'Text', 'type' => 'textarea'],
                ]],
                ...$extras,
            ],
        ],

        'faq' => [
            'label' => 'FAQ',
            'icon' => 'fa-solid fa-circle-question',
            'description' => 'Questions with the shared "Still have questions?" box (edit it in Site Layout).',
            'fields' => [
                ...$section,
                ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'width' => 'half', 'group' => 'Heading'],
                ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'width' => 'half', 'group' => 'Heading'],
                ['key' => 'subtitle', 'label' => 'Subtitle', 'type' => 'textarea', 'group' => 'Heading'],
                $faqItems + ['group' => 'Questions'],
            ],
        ],

        'faq_topics' => [
            'label' => 'FAQ topics',
            'icon' => 'fa-solid fa-folder-tree',
            'description' => 'Topic cards linking to FAQ groups.',
            'fields' => [
                ...$section, ...$head,
                ['key' => 'items', 'label' => 'Topics', 'type' => 'repeater', 'item_label' => 'title', 'group' => 'Topics', 'fields' => [
                    ['key' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'width' => 'half'],
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'count', 'label' => 'Sub text', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'url', 'label' => 'Link (e.g. #faq-general)', 'type' => 'url', 'width' => 'half'],
                ]],
            ],
        ],

        'faq_groups' => [
            'label' => 'FAQ groups',
            'icon' => 'fa-solid fa-layer-group',
            'description' => 'Several titled FAQ lists (used on the FAQ page).',
            'fields' => [
                ...$section,
                ['key' => 'groups', 'label' => 'Groups', 'type' => 'repeater', 'item_label' => 'title', 'group' => 'Groups', 'fields' => [
                    ['key' => 'anchor', 'label' => 'Anchor ID', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'width' => 'third'],
                    $faqItems,
                ]],
            ],
        ],

        'cta_banner' => [
            'label' => 'Call to action',
            'icon' => 'fa-solid fa-bullhorn',
            'description' => 'Dark banner with heading and buttons.',
            'fields' => [
                ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'width' => 'half'],
                ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'width' => 'half'],
                ['key' => 'text', 'label' => 'Text', 'type' => 'textarea'],
                $buttons(),
            ],
        ],

        'logos' => [
            'label' => 'Logo strip',
            'icon' => 'fa-solid fa-icons',
            'description' => 'Heading with a row of technology logos.',
            'fields' => [...$section, ...$head, ...$extras],
        ],

        'framework_row' => [
            'label' => 'Framework row',
            'icon' => 'fa-solid fa-cubes',
            'description' => 'Compact "Works with" list.',
            'fields' => [
                ...$section,
                ['key' => 'label', 'label' => 'Label', 'type' => 'text'],
                ['key' => 'items', 'label' => 'Items', 'type' => 'list'],
            ],
        ],

        'lang_grid' => [
            'label' => 'Language cards',
            'icon' => 'fa-solid fa-code',
            'description' => 'Linked cards for each supported language.',
            'fields' => [
                ...$section, ...$head,
                ['key' => 'items', 'label' => 'Languages', 'type' => 'repeater', 'item_label' => 'name', 'group' => 'Cards', 'fields' => [
                    ['key' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'width' => 'third'],
                    ['key' => 'name', 'label' => 'Name', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'url', 'label' => 'Link', 'type' => 'url', 'width' => 'third'],
                    ['key' => 'version', 'label' => 'Versions', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'frameworks', 'label' => 'Frameworks', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'link_label', 'label' => 'Link label', 'type' => 'text', 'width' => 'third'],
                ]],
            ],
        ],

        'number_grid' => [
            'label' => 'Numbers',
            'icon' => 'fa-solid fa-hashtag',
            'description' => 'Large animated numbers.',
            'fields' => [
                ...$section, ...$head,
                ['key' => 'items', 'label' => 'Numbers', 'type' => 'repeater', 'item_label' => 'label', 'group' => 'Numbers', 'fields' => [
                    ['key' => 'value', 'label' => 'Shown value', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'label', 'label' => 'Label', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'count', 'label' => 'Count-up to (number, optional)', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'suffix', 'label' => 'Count-up suffix', 'type' => 'text', 'width' => 'half'],
                ]],
            ],
        ],

        'contact' => [
            'label' => 'Contact form',
            'icon' => 'fa-solid fa-envelope-open-text',
            'description' => 'Contact channels and the inquiry form (submissions go to Inquiries).',
            'fields' => [
                ...$section,
                ['key' => 'channels', 'label' => 'Channels', 'type' => 'repeater', 'item_label' => 'title', 'group' => 'Channels', 'fields' => [
                    ...$iconTitleText,
                    ['key' => 'link_label', 'label' => 'Link label', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'link_url', 'label' => 'Link', 'type' => 'url', 'width' => 'half'],
                    ['key' => 'new_tab', 'label' => 'Open in new tab', 'type' => 'toggle'],
                ]],
                ['key' => 'form_title', 'label' => 'Form title', 'type' => 'text', 'group' => 'Form'],
                ['key' => 'form_text', 'label' => 'Form text', 'type' => 'textarea', 'group' => 'Form'],
                ['key' => 'departments', 'label' => 'Departments', 'type' => 'list', 'group' => 'Form'],
                ['key' => 'button_label', 'label' => 'Button label', 'type' => 'text', 'group' => 'Form'],
            ],
        ],

        'offices' => [
            'label' => 'Offices',
            'icon' => 'fa-solid fa-building',
            'description' => 'Office address cards.',
            'fields' => [
                ...$section, ...$head,
                ['key' => 'items', 'label' => 'Offices', 'type' => 'repeater', 'item_label' => 'title', 'group' => 'Offices', 'fields' => [
                    ['key' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'width' => 'half'],
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'address', 'label' => 'Address (one line per row)', 'type' => 'textarea'],
                ]],
            ],
        ],

        'blog_featured' => [
            'label' => 'Featured article',
            'icon' => 'fa-solid fa-newspaper',
            'description' => 'Highlighted article with a benchmark chart.',
            'fields' => [
                ...$section,
                ['key' => 'category', 'label' => 'Tag', 'type' => 'text', 'width' => 'half', 'group' => 'Article'],
                ['key' => 'read_time', 'label' => 'Read time', 'type' => 'text', 'width' => 'half', 'group' => 'Article'],
                ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'group' => 'Article'],
                ['key' => 'text', 'label' => 'Text', 'type' => 'textarea', 'group' => 'Article'],
                ['key' => 'url', 'label' => 'Article link', 'type' => 'url', 'group' => 'Article'],
                ['key' => 'chart_title', 'label' => 'Chart title', 'type' => 'text', 'width' => 'half', 'group' => 'Chart'],
                ['key' => 'chart_label', 'label' => 'Chart name (screen readers)', 'type' => 'text', 'width' => 'half', 'group' => 'Chart'],
                ['key' => 'bars', 'label' => 'Bars', 'type' => 'repeater', 'item_label' => 'label', 'group' => 'Chart', 'fields' => [
                    ['key' => 'label', 'label' => 'Label', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'value', 'label' => 'Value', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'width', 'label' => 'Bar width %', 'type' => 'number', 'width' => 'third'],
                    ['key' => 'best', 'label' => 'Highlight', 'type' => 'toggle'],
                ]],
            ],
        ],

        'blog_grid' => [
            'label' => 'Blog posts',
            'icon' => 'fa-solid fa-table-cells',
            'description' => 'Latest published posts from Blog Articles.',
            'fields' => [
                ...$section, ...$head,
                ['key' => 'limit', 'label' => 'Number of posts', 'type' => 'number', 'width' => 'half', 'group' => 'Posts'],
                ['key' => 'exclude', 'label' => 'Hide posts (slugs)', 'type' => 'list', 'group' => 'Posts'],
            ],
        ],

        'services_showcase' => [
            'label' => 'Services showcase',
            'icon' => 'fa-solid fa-grip',
            'description' => 'Homepage services overview with feature cards and service links.',
            'fields' => [
                ['key' => 'background', 'label' => 'Background', 'type' => 'select', 'options' => $backgrounds, 'width' => 'half', 'group' => 'Layout'],
                ['key' => 'anchor', 'label' => 'Anchor ID', 'type' => 'text', 'width' => 'half', 'group' => 'Layout'],
                ['key' => 'eyebrow_icon', 'label' => 'Eyebrow icon', 'type' => 'icon', 'width' => 'half', 'group' => 'Heading'],
                ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'width' => 'half', 'group' => 'Heading'],
                ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'width' => 'half', 'group' => 'Heading'],
                ['key' => 'title_highlight', 'label' => 'Highlighted words', 'type' => 'text', 'width' => 'half', 'group' => 'Heading'],
                ['key' => 'subtitle', 'label' => 'Lead text', 'type' => 'textarea', 'group' => 'Heading'],
                ['key' => 'cards', 'label' => 'Dark feature cards', 'type' => 'repeater', 'item_label' => 'title', 'group' => 'Cards', 'fields' => $iconTitleText],
                ['key' => 'banner_title', 'label' => 'Banner title', 'type' => 'text', 'group' => 'Services'],
                ['key' => 'tags', 'label' => 'Banner tags', 'type' => 'list', 'group' => 'Services'],
                ['key' => 'services', 'label' => 'Service cards', 'type' => 'repeater', 'item_label' => 'title', 'group' => 'Services', 'fields' => [
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text'],
                    ['key' => 'text', 'label' => 'Text', 'type' => 'textarea'],
                    ['key' => 'link_label', 'label' => 'Link label', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'link_url', 'label' => 'Link', 'type' => 'url', 'width' => 'half'],
                ]],
            ],
        ],

        'features_showcase' => [
            'label' => 'Features showcase',
            'icon' => 'fa-solid fa-puzzle-piece',
            'description' => 'Software logo cards, badges and language chips.',
            'fields' => [
                ...$section, ...$head,
                ['key' => 'cards', 'label' => 'Software cards', 'type' => 'repeater', 'item_label' => 'alt', 'group' => 'Cards', 'fields' => [
                    ['key' => 'image', 'label' => 'Logo', 'type' => 'image', 'width' => 'half'],
                    ['key' => 'alt', 'label' => 'Name (alt text)', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'text', 'label' => 'Text', 'type' => 'textarea'],
                ]],
                ['key' => 'badges', 'label' => 'Badges', 'type' => 'repeater', 'item_label' => 'text', 'group' => 'Cards', 'fields' => [
                    ['key' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'width' => 'half'],
                    ['key' => 'text', 'label' => 'Text', 'type' => 'text', 'width' => 'half'],
                ]],
                ['key' => 'langs_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'width' => 'half', 'group' => 'Languages'],
                ['key' => 'langs_title', 'label' => 'Title', 'type' => 'text', 'width' => 'half', 'group' => 'Languages'],
                ['key' => 'langs_link_label', 'label' => 'Link label', 'type' => 'text', 'width' => 'half', 'group' => 'Languages'],
                ['key' => 'langs_link_url', 'label' => 'Link', 'type' => 'url', 'width' => 'half', 'group' => 'Languages'],
                ['key' => 'langs', 'label' => 'Language chips', 'type' => 'repeater', 'item_label' => 'name', 'group' => 'Languages', 'fields' => [
                    ['key' => 'name', 'label' => 'Name', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'meta', 'label' => 'Sub text', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'url', 'label' => 'Link', 'type' => 'url', 'width' => 'third'],
                    ['key' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'width' => 'half'],
                    ['key' => 'style', 'label' => 'Colour', 'type' => 'select', 'width' => 'half', 'options' => ['is-php' => 'PHP', 'is-node' => 'Node.js', 'is-python' => 'Python', 'is-ruby' => 'Ruby', 'is-java' => 'Java', 'is-dotnet' => '.NET']],
                ]],
            ],
        ],

        'why_us' => [
            'label' => 'Why choose us',
            'icon' => 'fa-solid fa-award',
            'description' => 'Dark band with reasons, cards and animated stats.',
            'fields' => [
                ['key' => 'anchor', 'label' => 'Anchor ID', 'type' => 'text', 'width' => 'half', 'group' => 'Heading'],
                ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'width' => 'half', 'group' => 'Heading'],
                ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'width' => 'half', 'group' => 'Heading'],
                ['key' => 'title_highlight', 'label' => 'Highlighted words', 'type' => 'text', 'width' => 'half', 'group' => 'Heading'],
                ['key' => 'points', 'label' => 'Check points', 'type' => 'list', 'group' => 'Heading'],
                $buttons() + ['group' => 'Heading'],
                ['key' => 'cards', 'label' => 'Cards', 'type' => 'repeater', 'item_label' => 'title', 'group' => 'Cards', 'fields' => [
                    ...$iconTitleText,
                    ['key' => 'link_label', 'label' => 'Link label', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'link_url', 'label' => 'Link', 'type' => 'url', 'width' => 'half'],
                ]],
                ['key' => 'stats', 'label' => 'Stats', 'type' => 'repeater', 'item_label' => 'label', 'group' => 'Stats', 'fields' => [
                    ['key' => 'value', 'label' => 'Shown value', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'label', 'label' => 'Label', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'count', 'label' => 'Count-up to (optional)', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'suffix', 'label' => 'Count-up suffix', 'type' => 'text', 'width' => 'half'],
                ]],
            ],
        ],

        'reviews' => [
            'label' => 'Customer reviews',
            'icon' => 'fa-solid fa-comment-dots',
            'description' => 'Review card slider.',
            'fields' => [
                ...$section, ...$head,
                ['key' => 'slider_label', 'label' => 'Slider name', 'type' => 'text', 'width' => 'half', 'group' => 'Reviews'],
                ['key' => 'nav_label', 'label' => 'Arrow label', 'type' => 'text', 'width' => 'half', 'group' => 'Reviews'],
                ['key' => 'items', 'label' => 'Reviews', 'type' => 'repeater', 'item_label' => 'name', 'group' => 'Reviews', 'fields' => [
                    ['key' => 'name', 'label' => 'Name', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'role', 'label' => 'Role', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'tag', 'label' => 'Service tag', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'initials', 'label' => 'Initials', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'avatar_color', 'label' => 'Avatar colour', 'type' => 'select', 'width' => 'third', 'options' => ['blue' => 'Blue', 'green' => 'Green', 'purple' => 'Purple', 'cyan' => 'Cyan', 'pink' => 'Pink']],
                    ['key' => 'rating', 'label' => 'Stars (1–5)', 'type' => 'number', 'width' => 'third'],
                    ['key' => 'title', 'label' => 'Headline', 'type' => 'text'],
                    ['key' => 'text', 'label' => 'Review', 'type' => 'textarea'],
                ]],
            ],
        ],

        'custom_html' => [
            'label' => 'Custom HTML',
            'icon' => 'fa-solid fa-code',
            'description' => 'Raw HTML for one-off content. Use the site\'s CSS classes.',
            'fields' => [
                ['key' => 'html', 'label' => 'HTML', 'type' => 'html'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Site layout settings (stored as JSON in the settings table)
    |--------------------------------------------------------------------------
    */

    'layout' => [

        'menu_primary' => [
            'label' => 'Main menu',
            'icon' => 'fa-solid fa-bars',
            'description' => 'Header mega menu. Drag order with the arrows; the active page is highlighted automatically.',
            'list' => true,
            'item_label' => 'label',
            'fields' => [
                ['key' => 'label', 'label' => 'Menu label', 'type' => 'text', 'width' => 'half'],
                ['key' => 'url', 'label' => 'Link (only for items without columns)', 'type' => 'url', 'width' => 'half'],
                ['key' => 'layout', 'label' => 'Dropdown size', 'type' => 'select', 'width' => 'half', 'options' => ['compact-menu' => 'Compact (1 column)', 'mega-two' => 'Two columns', 'mega-wide' => 'Wide (intro + columns)']],
                ['key' => 'intro_eyebrow', 'label' => 'Intro card eyebrow', 'type' => 'text', 'width' => 'half'],
                ['key' => 'intro_title', 'label' => 'Intro card title', 'type' => 'text', 'width' => 'half'],
                ['key' => 'intro_text', 'label' => 'Intro card text', 'type' => 'textarea'],
                ['key' => 'columns', 'label' => 'Columns', 'type' => 'repeater', 'item_label' => 'title', 'fields' => [
                    ['key' => 'title', 'label' => 'Column title', 'type' => 'text'],
                    ['key' => 'links', 'label' => 'Links', 'type' => 'repeater', 'item_label' => 'label', 'fields' => [
                        ['key' => 'label', 'label' => 'Label', 'type' => 'text', 'width' => 'third'],
                        ['key' => 'description', 'label' => 'Description', 'type' => 'text', 'width' => 'third'],
                        ['key' => 'url', 'label' => 'Link', 'type' => 'url', 'width' => 'third'],
                        ['key' => 'new_tab', 'label' => 'Open in new tab', 'type' => 'toggle'],
                    ]],
                    ['key' => 'promo_icon', 'label' => 'Promo box icon', 'type' => 'icon', 'width' => 'third'],
                    ['key' => 'promo_title', 'label' => 'Promo box title', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'promo_text', 'label' => 'Promo box text', 'type' => 'text', 'width' => 'third'],
                ]],
            ],
        ],

        'layout_header' => [
            'label' => 'Header',
            'icon' => 'fa-solid fa-window-maximize',
            'description' => 'Top strip and header buttons. The logo is set in Global Settings.',
            'fields' => [
                ['key' => 'strip_brand', 'label' => 'Strip brand', 'type' => 'text', 'width' => 'half'],
                ['key' => 'strip_text', 'label' => 'Strip text', 'type' => 'text', 'width' => 'half'],
                ['key' => 'strip_links', 'label' => 'Strip links', 'type' => 'repeater', 'item_label' => 'label', 'fields' => $linkFields],
                ['key' => 'actions', 'label' => 'Header buttons', 'type' => 'repeater', 'item_label' => 'label', 'fields' => [
                    ...$linkFields,
                    ['key' => 'style', 'label' => 'Style', 'type' => 'select', 'options' => ['nav-phone' => 'Text link (hidden on laptop)', 'nav-login' => 'Outline', 'nav-cta' => 'Primary']],
                ]],
            ],
        ],

        'social_links' => [
            'label' => 'Social links',
            'icon' => 'fa-solid fa-share-nodes',
            'description' => 'Shown in the header strip and the mobile menu.',
            'list' => true,
            'item_label' => 'label',
            'fields' => [
                ['key' => 'label', 'label' => 'Name (screen readers)', 'type' => 'text', 'width' => 'half'],
                ['key' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'width' => 'half'],
                ['key' => 'url', 'label' => 'Link', 'type' => 'url', 'width' => 'half'],
                ['key' => 'new_tab', 'label' => 'Open in new tab', 'type' => 'toggle', 'width' => 'half'],
            ],
        ],

        'layout_footer' => [
            'label' => 'Footer',
            'icon' => 'fa-solid fa-window-minimize',
            'description' => 'Footer text, client-area box, highlights, copyright and bottom links.',
            'fields' => [
                ['key' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                ['key' => 'command_eyebrow', 'label' => 'Box eyebrow', 'type' => 'text', 'width' => 'half', 'group' => 'Client area box'],
                ['key' => 'command_title', 'label' => 'Box title', 'type' => 'text', 'width' => 'half', 'group' => 'Client area box'],
                ['key' => 'command_label', 'label' => 'Box link label', 'type' => 'text', 'width' => 'half', 'group' => 'Client area box'],
                ['key' => 'command_url', 'label' => 'Box link', 'type' => 'url', 'width' => 'half', 'group' => 'Client area box'],
                ['key' => 'stats', 'label' => 'Highlights', 'type' => 'repeater', 'item_label' => 'label', 'group' => 'Highlights', 'fields' => [
                    ['key' => 'value', 'label' => 'Value', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'label', 'label' => 'Label', 'type' => 'text', 'width' => 'half'],
                ]],
                ['key' => 'copyright_prefix', 'label' => 'Copyright before year', 'type' => 'text', 'width' => 'third', 'group' => 'Copyright'],
                ['key' => 'copyright_brand', 'label' => 'Brand link text', 'type' => 'text', 'width' => 'third', 'group' => 'Copyright'],
                ['key' => 'copyright_suffix', 'label' => 'Copyright after brand', 'type' => 'text', 'width' => 'third', 'group' => 'Copyright'],
                ['key' => 'bottom_links', 'label' => 'Bottom links', 'type' => 'repeater', 'item_label' => 'label', 'group' => 'Copyright', 'fields' => [
                    ['key' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'width' => 'third'],
                    ['key' => 'label', 'label' => 'Label', 'type' => 'text', 'width' => 'third'],
                    ['key' => 'url', 'label' => 'Link', 'type' => 'url', 'width' => 'third'],
                    ['key' => 'new_tab', 'label' => 'Open in new tab', 'type' => 'toggle'],
                ]],
            ],
        ],

        'menu_footer' => [
            'label' => 'Footer menu',
            'icon' => 'fa-solid fa-sitemap',
            'description' => 'Footer link columns.',
            'list' => true,
            'item_label' => 'title',
            'fields' => [
                ['key' => 'title', 'label' => 'Column title', 'type' => 'text'],
                ['key' => 'links', 'label' => 'Links', 'type' => 'repeater', 'item_label' => 'label', 'fields' => $linkFields],
            ],
        ],

        'widget_quick_dock' => [
            'label' => 'Floating dock',
            'icon' => 'fa-solid fa-life-ring',
            'description' => 'Floating buttons on the right (the scroll-to-top button is always first).',
            'fields' => [
                ['key' => 'buttons', 'label' => 'Buttons', 'type' => 'repeater', 'item_label' => 'tooltip', 'fields' => [
                    ['key' => 'style', 'label' => 'Style', 'type' => 'select', 'width' => 'half', 'options' => ['dock-support' => 'Support (blue)', 'dock-chat' => 'Chat', 'dock-wa' => 'WhatsApp (green)']],
                    ['key' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'width' => 'half'],
                    ['key' => 'url', 'label' => 'Link', 'type' => 'url', 'width' => 'half'],
                    ['key' => 'tooltip', 'label' => 'Tooltip', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'label', 'label' => 'Name (screen readers)', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'title', 'label' => 'Hover title', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'new_tab', 'label' => 'Open in new tab', 'type' => 'toggle'],
                ]],
            ],
        ],

        'widget_faq_help' => [
            'label' => 'FAQ help box',
            'icon' => 'fa-solid fa-headset',
            'description' => 'The "Still have questions?" box and button shown next to every FAQ.',
            'fields' => [
                ['key' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'width' => 'half'],
                ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'width' => 'half'],
                ['key' => 'text', 'label' => 'Text', 'type' => 'text'],
                ['key' => 'button_label', 'label' => 'Button label', 'type' => 'text', 'width' => 'half'],
                ['key' => 'button_url', 'label' => 'Button link', 'type' => 'url', 'width' => 'half'],
            ],
        ],

        'widget_domain_search' => [
            'label' => 'Domain search',
            'icon' => 'fa-solid fa-magnifying-glass',
            'description' => 'Shared domain search card. Price chips are the "featured" TLDs in Domain Pricing.',
            'fields' => [
                ['key' => 'register_placeholder', 'label' => 'Register placeholder', 'type' => 'text', 'width' => 'half'],
                ['key' => 'transfer_placeholder', 'label' => 'Transfer placeholder', 'type' => 'text', 'width' => 'half'],
                ['key' => 'extensions', 'label' => 'Extensions in the dropdown', 'type' => 'list'],
                ['key' => 'perks', 'label' => 'Perks', 'type' => 'list'],
                ['key' => 'links', 'label' => 'Links', 'type' => 'repeater', 'item_label' => 'label', 'fields' => [
                    ['key' => 'label', 'label' => 'Label', 'type' => 'text', 'width' => 'half'],
                    ['key' => 'url', 'label' => 'Link', 'type' => 'url', 'width' => 'half'],
                ]],
            ],
        ],
    ],
];
