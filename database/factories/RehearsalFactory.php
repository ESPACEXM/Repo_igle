<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Rehearsal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rehearsal>
 */
class RehearsalFactory extends Factory
{
    protected $model = Rehearsal::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'date' => $this->faker->dateTimeBetween('+1 day', '+2 weeks'),
            'name' => $this->faker->sentence(2),
        ];
    }
}