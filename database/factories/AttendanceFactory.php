<?php

namespace Database\Factories;

use App\Models\Rehearsal;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rehearsal_id' => Rehearsal::factory(),
            'event_id' => null,
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement(['present', 'absent', 'late', 'justified']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the attendance is present.
     */
    public function present(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'present',
        ]);
    }

    /**
     * Indicate that the attendance is absent.
     */
    public function absent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'absent',
        ]);
    }

    /**
     * Indicate that the attendance is justified.
     */
    public function justified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'justified',
        ]);
    }

    /**
     * Create attendance for an event instead of a rehearsal.
     */
    public function forEvent(): static
    {
        return $this->state(fn (array $attributes) => [
            'rehearsal_id' => null,
            'event_id' => \App\Models\Event::factory(),
        ]);
    }

    /**
     * Create attendance for a rehearsal (default behavior, explicit for clarity).
     */
    public function forRehearsal(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_id' => null,
            'rehearsal_id' => Rehearsal::factory(),
        ]);
    }
}