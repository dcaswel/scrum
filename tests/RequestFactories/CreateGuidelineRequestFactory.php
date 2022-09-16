<?php

namespace Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class CreateGuidelineRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
           'score' => $this->faker->randomElement(['0.5', '1.0', '2.0', '3.0', '5.0', '8.0', '13.0', '21.0']),
           'description' => $this->faker->sentence(),
           'bullets' => [],
           'tickets' => []
        ];
    }

    public function withNewBullets(int $number): static
    {
        $bullets = [];
        for ($i = 0; $i < $number; $i++) {
            $bullets[] = ['body' => $this->faker->sentence()];
        }

        return $this->state(['bullets' => $bullets]);
    }

    public function withNewTickets(int $number)
    {
        $tickets = [];
        for ($i = 0; $i < $number; $i++) {
            $tickets[] = ['ticket_number' => $this->faker->word()];
        }

        return $this->state(['tickets' => $tickets]);
    }
}
