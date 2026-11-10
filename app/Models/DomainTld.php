<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainTld extends Model
{
    use HasFactory;

    protected $fillable = [
        'tld',
        'price_1yr',
        'price_2yr',
        'price_3yr',
        'price_5yr',
        'price_10yr',
        'transfer_price',
        'renew_prices',
        'badge',
        'popular_order',
        'featured_order',
        'renew_order',
        'sort_order',
        'is_active',
    ];

    /**
     * Registration terms shown in the full price list, keyed by years.
     *
     * @var array<int, string>
     */
    public const TERMS = [1 => 'price_1yr', 2 => 'price_2yr', 3 => 'price_3yr', 5 => 'price_5yr', 10 => 'price_10yr'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_1yr' => 'decimal:2',
            'price_2yr' => 'decimal:2',
            'price_3yr' => 'decimal:2',
            'price_5yr' => 'decimal:2',
            'price_10yr' => 'decimal:2',
            'transfer_price' => 'decimal:2',
            'renew_prices' => 'array',
            'popular_order' => 'integer',
            'featured_order' => 'integer',
            'renew_order' => 'integer',
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
     * @param  Builder<DomainTld>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Active TLDs of one curated list, in that list's order.
     *
     * @param  Builder<DomainTld>  $query
     * @param  'popular'|'featured'|'renew'  $list
     */
    public function scopeInList(Builder $query, string $list): void
    {
        $column = $list.'_order';

        $query->where('is_active', true)->whereNotNull($column)->orderBy($column)->orderBy('id');
    }

    /**
     * Renewal price for a term; falls back to the 1-year renewal (or
     * registration) price multiplied by the number of years.
     */
    public function renewalFor(int $years): ?float
    {
        $prices = $this->renew_prices ?? [];

        if (isset($prices[$years]) && $prices[$years] !== '') {
            return (float) $prices[$years];
        }

        $yearly = $prices[1] ?? $this->price_1yr;

        return $yearly === null || $yearly === '' ? null : round((float) $yearly * $years, 2);
    }
}
