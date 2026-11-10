<?php

namespace App\Models;

use Database\Factories\SectionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A content block on a page. Rows without a page are shared "library"
 * sections; page rows reference them through `shared_id`.
 */
class Section extends Model
{
    /** @use HasFactory<SectionFactory> */
    use HasFactory;

    protected $fillable = [
        'page_id',
        'shared_id',
        'type',
        'name',
        'data',
        'sort_order',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Page::flushContentCache());
        static::deleted(fn () => Page::flushContentCache());
    }

    /**
     * @return BelongsTo<Page, $this>
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * @return BelongsTo<Section, $this>
     */
    public function shared(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'shared_id');
    }

    /**
     * Page rows that render this shared section.
     *
     * @return HasMany<Section, $this>
     */
    public function usages(): HasMany
    {
        return $this->hasMany(Section::class, 'shared_id');
    }

    /**
     * Shared library sections only.
     *
     * @param  Builder<Section>  $query
     */
    public function scopeLibrary(Builder $query): void
    {
        $query->whereNull('page_id');
    }

    /**
     * Section type to render (taken from the shared section when linked).
     */
    public function renderType(): string
    {
        return $this->shared?->type ?? $this->type;
    }

    /**
     * Content to render (taken from the shared section when linked).
     *
     * @return array<string, mixed>
     */
    public function renderData(): array
    {
        return ($this->shared_id ? $this->shared?->data : $this->data) ?? [];
    }

    /**
     * Whether this row should render on the public site.
     */
    public function isVisible(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return ! $this->shared_id || ($this->shared && $this->shared->is_active);
    }
}
