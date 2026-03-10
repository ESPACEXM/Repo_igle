<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Rehearsal;
use App\Models\User;
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
            'location' => $this->faker->optional()->city(),
            'notes' => $this->faker->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }

    /**
     * Assign a specific creator to the rehearsal.
     */
    public function createdBy(\App\Models\User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'created_by' => $user->id,
        ]);
    }

    /**
     * Create a rehearsal without an associated event (independent rehearsal).
     */
    public function independent(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_id' => null,
        ]);
    }
}
