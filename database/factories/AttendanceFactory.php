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
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement(['present', 'absent', 'justified']),
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
}