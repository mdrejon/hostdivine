<?php

namespace Database\Factories;

use App\Models\Page;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Section>
 */
class SectionFactory extends Factory
{
    /**
     * Define the model's default state: a CTA banner on a page.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'page_id' => Page::factory(),
            'type' => 'cta_banner',
            'name' => 'Call to action',
            'data' => [
                'eyebrow' => 'Get started today',
                'title' => fake()->sentence(4),
                'text' => fake()->sentence(),
                'buttons' => [['label' => 'Order now', 'url' => '#plans', 'style' => 'primary']],
            ],
            'sort_order' => 1,
            'is_active' => true,
        ];
    }

    /**
     * A shared library section (not attached to a page).
     */
    public function library(): static
    {
        return $this->state(fn () => ['page_id' => null]);
    }

    public function hidden(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
