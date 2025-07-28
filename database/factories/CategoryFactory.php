<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Membuat nama kategori yang realistis
        $name = $this->faker->unique()->randomElement([
            'Electronics',
            'Fashion Apparel',
            'Home & Kitchen',
            'Books & Media',
            'Health & Beauty',
            'Sports & Outdoors',
            'Toys & Games',
            'Automotive Parts',
        ]);

        return [
            'name' => $name,
            // Membuat slug secara otomatis dari nama
            'slug' => Str::slug($name),
        ];
    }
}
