<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'        => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'price'       => $this->faker->randomFloat(2, 10, 200),
            'inventory'   => $this->faker->numberBetween(0, 100),
            'categories_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
        ];
    }
}
