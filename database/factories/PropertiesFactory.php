<?php

namespace Database\Factories;

use App\Models\Categories;
use App\Models\Cities;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PropertiesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::all()->random()->id,
            'city_id' => Cities::all()->random()->id,
            'category_id' => Categories::all()->random()->id,
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(2),
            'price' => $this->faker->numberBetween(10000, 1000000),
            'area' => $this->faker->numberBetween(100, 1000),
            'bedrooms' => $this->faker->numberBetween(1, 5),
            'bathrooms' => $this->faker->numberBetween(1, 5),
            'garages' => $this->faker->numberBetween(1, 5),
            'kitchens' => $this->faker->numberBetween(1, 5),
            'address' => [
                'lat' => $this->faker->latitude,
                'lng' => $this->faker->longitude,
            ],
            'images' => ['1.jpg', '2.jpg', '3.jpg'],
        ];
    }
}
