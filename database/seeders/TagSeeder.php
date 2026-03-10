<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            // Temas principales
            ['name' => 'Adoración/Intimidad', 'type' => 'theme', 'color' => 'purple', 'icon' => 'sparkles', 'description' => 'Canciones de adoración profunda e intimidad con Dios'],
            ['name' => 'Alabanza/Celebración', 'type' => 'theme', 'color' => 'yellow', 'icon' => 'sun', 'description' => 'Canciones alegres de celebración'],
            ['name' => 'Santa Cena', 'type' => 'theme', 'color' => 'red', 'icon' => 'heart', 'description' => 'Canciones para el momento de la Santa Cena'],
            ['name' => 'Perdón/Restauración', 'type' => 'theme', 'color' => 'green', 'icon' => 'refresh', 'description' => 'Canciones sobre el perdón y restauración'],
            ['name' => 'Sanidad', 'type' => 'theme', 'color' => 'teal', 'icon' => 'plus-circle', 'description' => 'Canciones para momentos de sanidad'],
            ['name' => 'Guerra Espiritual', 'type' => 'theme', 'color' => 'indigo', 'icon' => 'shield', 'description' => 'Canciones de guerra espiritual y victoria'],
            ['name' => 'Prosperidad/Providencia', 'type' => 'theme', 'color' => 'green', 'icon' => 'trending-up', 'description' => 'Canciones sobre la provisión de Dios'],
            ['name' => 'Familia/Hogar', 'type' => 'theme', 'color' => 'orange', 'icon' => 'home', 'description' => 'Canciones sobre familia y hogar'],
            ['name' => 'Juventud', 'type' => 'theme', 'color' => 'pink', 'icon' => 'users', 'description' => 'Canciones especiales para jóvenes'],
            ['name' => 'Evangelismo/Misión', 'type' => 'theme', 'color' => 'blue', 'icon' => 'globe', 'description' => 'Canciones para evangelismo y misiones'],
            ['name' => 'Esperanza/Consuelo', 'type' => 'theme', 'color' => 'blue', 'icon' => 'smile', 'description' => 'Canciones de esperanza y consuelo'],
            ['name' => 'Fe/Confianza', 'type' => 'theme', 'color' => 'indigo', 'icon' => 'anchor', 'description' => 'Canciones sobre fe y confianza en Dios'],
            ['name' => 'Amor de Dios', 'type' => 'theme', 'color' => 'red', 'icon' => 'heart', 'description' => 'Canciones sobre el amor de Dios'],
            ['name' => 'Gratitud', 'type' => 'theme', 'color' => 'yellow', 'icon' => 'gift', 'description' => 'Canciones de agradecimiento'],
            ['name' => 'Arrepentimiento', 'type' => 'theme', 'color' => 'gray', 'icon' => 'arrow-down', 'description' => 'Canciones de arrepentimiento y humillación'],

            // Ánimos/Estados
            ['name' => 'Tranquila', 'type' => 'mood', 'color' => 'blue', 'icon' => 'moon'],
            ['name' => 'Alegre', 'type' => 'mood', 'color' => 'yellow', 'icon' => 'smile'],
            ['name' => 'Reflexiva', 'type' => 'mood', 'color' => 'purple', 'icon' => 'book-open'],
            ['name' => 'Poderosa', 'type' => 'mood', 'color' => 'red', 'icon' => 'zap'],
            ['name' => 'Esperanzadora', 'type' => 'mood', 'color' => 'green', 'icon' => 'sunrise'],

            // Momentos del culto
            ['name' => 'Entrada', 'type' => 'moment', 'color' => 'green', 'icon' => 'log-in'],
            ['name' => 'Adoración', 'type' => 'moment', 'color' => 'purple', 'icon' => 'mic'],
            ['name' => 'Ofrenda', 'type' => 'moment', 'color' => 'yellow', 'icon' => 'dollar-sign'],
            ['name' => 'Predicación', 'type' => 'moment', 'color' => 'blue', 'icon' => 'book'],
            ['name' => 'Altar/Milagros', 'type' => 'moment', 'color' => 'red', 'icon' => 'flame'],
            ['name' => 'Cierre', 'type' => 'moment', 'color' => 'indigo', 'icon' => 'log-out'],

            // Tempo
            ['name' => 'Lenta', 'type' => 'tempo', 'color' => 'blue', 'icon' => 'minus'],
            ['name' => 'Media', 'type' => 'tempo', 'color' => 'green', 'icon' => 'circle'],
            ['name' => 'Rápida', 'type' => 'tempo', 'color' => 'red', 'icon' => 'plus'],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(
                ['name' => $tag['name']],
                $tag
            );
        }

        $this->command->info('✅ Tags creados exitosamente: ' . count($tags));
    }
}
