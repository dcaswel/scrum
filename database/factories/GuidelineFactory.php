<?php

namespace Database\Factories;

use App\Enums\Points;
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
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'score' => $this->faker->randomElement(Points::values()),
            'description' => $this->faker->sentence(),
        ];
    }

    public function score(Points $score)
    {
        return $this->state(['score' => $score]);
    }
}
