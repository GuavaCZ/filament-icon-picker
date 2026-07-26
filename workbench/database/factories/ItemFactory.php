<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\Item;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'icon' => fake()->randomElement([
                'heroicon-o-academic-cap',
                'heroicon-o-beaker',
                'heroicon-o-bolt',
                'heroicon-o-cloud',
                'heroicon-s-fire',
                'heroicon-s-star',
            ]),
        ];
    }

    /** An item with no icon, so IconColumn's placeholder path is visible too. */
    public function withoutIcon(): static
    {
        return $this->state(fn (): array => ['icon' => null]);
    }
}
