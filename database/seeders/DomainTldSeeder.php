<?php

namespace Database\Seeders;

use App\Models\DomainTld;
use Illuminate\Database\Seeder;

/**
 * Seeds the full TLD price list (BDT) from the html/ design. Existing
 * extensions keep their current prices.
 */
class DomainTldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = json_decode(file_get_contents(database_path('seeders/data/domain_tlds.json')), true, flags: JSON_THROW_ON_ERROR);
        $existing = DomainTld::query()->pluck('tld')->flip();
        $columns = ['tld', 'price_1yr', 'price_2yr', 'price_3yr', 'price_5yr', 'price_10yr', 'transfer_price', 'renew_prices', 'badge', 'popular_order', 'featured_order', 'renew_order', 'sort_order'];
        $now = now();

        $missing = collect($rows)
            ->reject(fn (array $row) => $existing->has($row['tld']))
            ->map(function (array $row) use ($columns, $now) {
                $record = array_merge(array_fill_keys($columns, null), array_intersect_key($row, array_flip($columns)));
                $record['renew_prices'] = isset($row['renew_prices']) ? json_encode($row['renew_prices']) : null;
                $record['sort_order'] ??= 0;

                return $record + ['is_active' => true, 'created_at' => $now, 'updated_at' => $now];
            });

        foreach ($missing->chunk(100) as $chunk) {
            DomainTld::query()->insert($chunk->values()->all());
        }
    }
}
