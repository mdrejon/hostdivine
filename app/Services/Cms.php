<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * View helpers used by the frontend Blade templates (aliased as `Cms`).
 *
 * Content stored in the CMS is plain text unless a field is explicitly an
 * "html" field, so every helper here escapes its input.
 */
class Cms
{
    /**
     * Escape plain text and wrap the Taka sign in `.tk` (the brand fonts
     * lack the glyph, see html/AGENTS.md).
     */
    public static function text(mixed $value): HtmlString
    {
        $escaped = e((string) ($value ?? ''));

        return new HtmlString(str_replace('৳', '<span class="tk">৳</span>', $escaped));
    }

    /**
     * Short text that may contain inline markup (links, bold, code).
     * Every other tag is stripped.
     */
    public static function rich(mixed $value): HtmlString
    {
        $html = strip_tags((string) ($value ?? ''), '<a><b><strong><em><i><code><br><span>');
        $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
        $html = preg_replace('/(href|src)\s*=\s*(["\'])\s*javascript:[^"\']*\2/i', '$1="#"', $html);

        return new HtmlString(str_replace('৳', '<span class="tk">৳</span>', $html));
    }

    /**
     * Escape text and turn line breaks into `<br>` tags.
     */
    public static function lines(mixed $value): HtmlString
    {
        $lines = preg_split('/\r\n|\r|\n/', trim((string) ($value ?? '')));

        return new HtmlString(implode('<br>', array_map(fn (string $line) => (string) self::text($line), $lines)));
    }

    /**
     * Render a heading where one phrase is wrapped in `.text-gradient`.
     */
    public static function highlight(mixed $title, mixed $highlight = null): HtmlString
    {
        $title = (string) ($title ?? '');
        $highlight = trim((string) ($highlight ?? ''));

        if ($highlight === '') {
            return self::text($title);
        }

        $span = '<span class="text-gradient">'.self::text($highlight).'</span>';
        $position = mb_strpos($title, $highlight);

        if ($position === false) {
            return new HtmlString(trim(self::text($title).' '.$span));
        }

        $before = mb_substr($title, 0, $position);
        $after = mb_substr($title, $position + mb_strlen($highlight));

        return new HtmlString(self::text($before).$span.self::text($after));
    }

    /**
     * Resolve a CMS link: absolute URLs, anchors, mailto and tel pass through,
     * site paths become absolute application URLs.
     */
    public static function url(mixed $value): string
    {
        $value = trim((string) ($value ?? ''));

        if ($value === '') {
            return '#';
        }

        if (Str::startsWith($value, ['http://', 'https://', '#', 'mailto:', 'tel:', '//'])) {
            return $value;
        }

        return url($value === '/' ? '/' : '/'.ltrim($value, '/'));
    }

    /**
     * Resolve a stored image or file path to a public URL.
     */
    public static function asset(mixed $value): string
    {
        $value = trim((string) ($value ?? ''));

        if ($value === '' || Str::startsWith($value, ['http://', 'https://', '//', 'data:'])) {
            return $value;
        }

        return asset(ltrim($value, '/'));
    }

    /**
     * Whether a CMS link points at the page being viewed (anchors ignored).
     */
    public static function isCurrent(mixed $value): bool
    {
        $value = trim((string) ($value ?? ''));

        if ($value === '' || Str::startsWith($value, ['#', 'http://', 'https://', 'mailto:', 'tel:'])) {
            return false;
        }

        if (Str::contains($value, '#')) {
            return false;
        }

        return trim($value, '/') === trim(request()->path(), '/');
    }

    /**
     * Read a scalar global setting.
     */
    public static function setting(string $key, mixed $default = null): mixed
    {
        $value = Setting::get($key);

        return ($value === null || $value === '') ? $default : $value;
    }

    /**
     * Read a structured (JSON) global setting such as a menu or the footer.
     *
     * @return array<mixed>
     */
    public static function layout(string $key): array
    {
        return Setting::json($key);
    }

    /**
     * Format a price without decimals ("1,600").
     */
    public static function wholePrice(mixed $value): string
    {
        return $value === null ? '—' : number_format((float) $value, 0);
    }

    /**
     * Format a price with two decimals ("1,600.00").
     */
    public static function price(mixed $value): string
    {
        return $value === null ? '—' : number_format((float) $value, 2);
    }
}
