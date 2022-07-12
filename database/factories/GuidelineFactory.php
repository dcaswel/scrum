<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guideline>
 */
class GuidelineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'team_id' => Team::factory(),
            'score' => $this->faker->randomElement([0.5, 1, 3, 5, 8, 13, 21]),
            'description' => $this->faker->sentence()
        ];
    }
}
