<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(2),
            'date' => $this->faker->dateTimeBetween('+1 day', '+1 month'),
            'location' => $this->faker->optional()->city(),
            'description' => $this->faker->optional()->paragraph(),
            'created_by' => User::factory(),
        ];
    }

    /**
     * Assign a specific creator to the event.
     */
    public function createdBy(\App\Models\User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'created_by' => $user->id,
        ]);
    }
}
