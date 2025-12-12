<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkillFactory extends Factory
{
    public function definition(): array
    {
        return [
            'admin_id' => Admin::factory(),
            'name' => $this->faker->word(),
            'proficiency' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced', 'expert']),
        ];
    }
}
