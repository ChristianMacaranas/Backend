<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'admin_id' => Admin::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'category' => $this->faker->randomElement(['web', 'mobile', 'desktop', 'other']),
            'tools' => $this->faker->word(),
            'status' => $this->faker->randomElement(['draft', 'published']),
            'images' => null,
        ];
    }
}
