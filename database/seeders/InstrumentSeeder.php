<?php

namespace Database\Seeders;

use App\Models\Instrument;
use Illuminate\Database\Seeder;

class InstrumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instruments = [
            [
                'name' => 'Voz Principal',
                'description' => 'Cantante líder principal',
            ],
            [
                'name' => 'Coros',
                'description' => 'Voz de acompañamiento y armonías',
            ],
            [
                'name' => 'Guitarra Acústica',
                'description' => 'Guitarra acústica rítmica',
            ],
            [
                'name' => 'Guitarra Eléctrica',
                'description' => 'Guitarra eléctrica principal',
            ],
            [
                'name' => 'Bajo',
                'description' => 'Guitarra bajo',
            ],
            [
                'name' => 'Batería',
                'description' => 'Percusión principal',
            ],
            [
                'name' => 'Teclado',
                'description' => 'Piano y sintetizador',
            ],
            [
                'name' => 'Percusión',
                'description' => 'Congas, bongos, timbales',
            ],
            [
                'name' => 'Saxofón',
                'description' => 'Viento - saxofón',
            ],
            [
                'name' => 'Trompeta',
                'description' => 'Viento - trompeta',
            ],
            [
                'name' => 'Violín',
                'description' => 'Cuerdas - violín',
            ],
            [
                'name' => 'Cajón Peruano',
                'description' => 'Percusión cajón',
            ],
            [
                'name' => 'Ukelele',
                'description' => 'Guitarra ukelele',
            ],
            [
                'name' => 'Mandolina',
                'description' => 'Instrumento de cuerda mandolina',
            ],
        ];

        foreach ($instruments as $instrument) {
            Instrument::firstOrCreate(
                ['name' => $instrument['name']],
                ['description' => $instrument['description']]
            );
        }
    }
}