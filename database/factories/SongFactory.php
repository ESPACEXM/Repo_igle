<?php

namespace Database\Factories;

use App\Models\Song;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Song>
 */
class SongFactory extends Factory
{
    protected $model = Song::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'artist' => $this->faker->name(),
            'key' => $this->faker->randomElement(['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B']),
            'tempo' => $this->faker->numberBetween(60, 180),
            'duration' => $this->faker->numberBetween(180, 600),
            'lyrics_url' => $this->faker->optional()->url(),
            'chords_url' => $this->faker->optional()->url(),
            'youtube_url' => $this->faker->optional()->url(),
            'spotify_url' => $this->faker->optional()->url(),
            'lyrics' => $this->faker->optional()->paragraphs(3, true),
            'chords' => $this->faker->optional()->text(500),
        ];
    }
}
