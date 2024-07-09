<?php

namespace Database\Factories;

use App\Models\Guideline;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GuidelineTicket>
 */
class GuidelineTicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'guideline_id' => Guideline::factory(),
            'ticket_number' => $this->faker->word(),
            'url' => null,
        ];
    }
}
