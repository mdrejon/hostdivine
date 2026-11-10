<?php

namespace App\Services;

use InvalidArgumentException;

/**
 * Reads the section / layout schemas in config/cms.php and normalises data
 * submitted by the admin editor against them.
 */
class ContentSchema
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function sectionTypes(): array
    {
        return config('cms.sections', []);
    }

    /**
     * @return array<string, mixed>
     */
    public function sectionType(string $type): array
    {
        return $this->sectionTypes()[$type] ?? throw new InvalidArgumentException("Unknown section type [{$type}].");
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function layoutSettings(): array
    {
        return config('cms.layout', []);
    }

    /**
     * Normalise section data for a type.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function cleanSection(string $type, array $data): array
    {
        return $this->cleanObject($data, $this->sectionType($type)['fields']);
    }

    /**
     * Normalise a layout setting value (an object or a list of objects).
     *
     * @param  array<mixed>  $value
     * @return array<mixed>
     */
    public function cleanLayout(string $key, array $value): array
    {
        $definition = $this->layoutSettings()[$key] ?? throw new InvalidArgumentException("Unknown layout setting [{$key}].");

        return ! empty($definition['list'])
            ? $this->cleanList($value, $definition['fields'])
            : $this->cleanObject($value, $definition['fields']);
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array<string, mixed>>  $fields
     * @return array<string, mixed>
     */
    protected function cleanObject(array $data, array $fields): array
    {
        $clean = [];

        foreach ($fields as $field) {
            $key = $field['key'];

            if (! array_key_exists($key, $data)) {
                continue;
            }

            $value = $this->cleanValue($data[$key], $field);

            if ($value === null || $value === '' || $value === [] || $value === false) {
                continue;
            }

            $clean[$key] = $value;
        }

        return $clean;
    }

    /**
     * @param  array<mixed>  $items
     * @param  array<int, array<string, mixed>>  $fields
     * @return array<int, array<string, mixed>>
     */
    protected function cleanList(array $items, array $fields): array
    {
        return array_values(array_filter(array_map(
            fn ($item) => is_array($item) ? $this->cleanObject($item, $fields) : [],
            $items,
        )));
    }

    /**
     * @param  array<string, mixed>  $field
     */
    protected function cleanValue(mixed $value, array $field): mixed
    {
        return match ($field['type']) {
            'toggle' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($value) ? (int) $value : null,
            'list' => is_array($value)
                ? array_values(array_filter(array_map(fn ($item) => trim((string) $item), $value), fn ($item) => $item !== ''))
                : [],
            'repeater' => is_array($value) ? $this->cleanList($value, $field['fields'] ?? []) : [],
            default => is_scalar($value) ? trim((string) $value) : null,
        };
    }
}
