<?php

namespace Tests\RequestFactories;

use App\Models\GuidelineBullet;
use App\Models\GuidelineTicket;
use Worksome\RequestFactories\RequestFactory;

class UpdateGuidelineRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'score' => $this->faker->randomElement(['0.5', '1.0', '2.0', '3.0', '5.0', '8.0', '13.0', '21.0']),
            'description' => $this->faker->sentence(),
            'bullets' => [],
            'tickets' => [],
        ];
    }

    public function withBullet(GuidelineBullet $bullet, ?array $overrides = null)
    {
        $bullet = $bullet->toArray();
        if (! is_null($overrides)) {
            $bullet = array_merge($bullet, $overrides);
        }

        return $this->state(['bullets' => [$bullet]]);
    }

    public function withNewBullet(?array $attributes = null)
    {
        $bullet = ['body' => $this->faker->sentence()];
        if (! is_null($attributes)) {
            $bullet = array_merge($bullet, $attributes);
        }

        return $this->state(['bullets' => [$bullet]]);
    }

    public function withTicket(GuidelineTicket $ticket, ?array $overrides = null)
    {
        $ticket = $ticket->toArray();
        if (! is_null($overrides)) {
            $ticket = array_merge($ticket, $overrides);
        }

        return $this->state(['tickets' => [$ticket]]);
    }

    public function withNewTicket(array $attributes)
    {
        $ticket = ['ticket_number' => $this->faker->word()];
        if (! is_null($attributes)) {
            $ticket = array_merge($ticket, $attributes);
        }

        return $this->state(['tickets' => [$ticket]]);
    }
}
