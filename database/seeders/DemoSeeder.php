<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Instrument;
use App\Models\Rehearsal;
use App\Models\Song;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando carga de datos demo...');

        // Crear instrumentos musicales
        $this->createInstruments();

        // Crear usuarios líderes
        $this->createLeaders();

        // Crear usuarios miembros
        $this->createMembers();

        // Crear canciones
        $this->createSongs();

        // Crear eventos
        $this->createEvents();

        // Crear ensayos
        $this->createRehearsals();

        // Crear asistencias
        $this->createAttendances();

        $this->command->info('✅ Datos demo cargados exitosamente!');
        $this->command->info('');
        $this->command->info('📋 Resumen:');
        $this->command->info('   - Instrumentos: ' . Instrument::count());
        $this->command->info('   - Usuarios: ' . User::count());
        $this->command->info('   - Canciones: ' . Song::count());
        $this->command->info('   - Eventos: ' . Event::count());
        $this->command->info('   - Ensayos: ' . Rehearsal::count());
        $this->command->info('   - Asistencias: ' . Attendance::count());
        $this->command->info('');
        $this->command->info('👤 Usuarios de prueba:');
        $this->command->info('   Líder: admin@iglesia.com / password');
        $this->command->info('   Miembro: juan@iglesia.com / password');
    }

    /**
     * Crear instrumentos musicales
     */
    private function createInstruments(): void
    {
        $this->command->info('🎸 Creando instrumentos...');

        $instruments = [
            ['name' => 'Guitarra Acústica', 'description' => 'Guitarra acústica estándar'],
            ['name' => 'Guitarra Eléctrica', 'description' => 'Guitarra eléctrica con amplificador'],
            ['name' => 'Bajo Eléctrico', 'description' => 'Bajo de 4 cuerdas'],
            ['name' => 'Piano/Teclado', 'description' => 'Teclado digital 88 teclas'],
            ['name' => 'Batería Acústica', 'description' => 'Batería completa con platillos'],
            ['name' => 'Congas', 'description' => 'Set de congas'],
            ['name' => 'Saxofón Alto', 'description' => 'Saxofón en Mi bemol'],
            ['name' => 'Trompeta', 'description' => 'Trompeta en Si bemol'],
            ['name' => 'Voz Soprano', 'description' => 'Voz principal femenina'],
            ['name' => 'Voz Tenor', 'description' => 'Voz principal masculina'],
        ];

        foreach ($instruments as $instrument) {
            Instrument::create($instrument);
        }
    }

    /**
     * Crear usuarios líderes
     */
    private function createLeaders(): void
    {
        $this->command->info('👑 Creando usuarios líderes...');

        User::create([
            'name' => 'Pastor Carlos Mendoza',
            'email' => 'admin@iglesia.com',
            'password' => Hash::make('password'),
            'phone' => '50241234567',
            'role' => 'leader',
            'is_active' => true,
            'notes' => 'Pastor principal de la iglesia',
        ]);

        User::create([
            'name' => 'María González',
            'email' => 'maria@iglesia.com',
            'password' => Hash::make('password'),
            'phone' => '50242345678',
            'role' => 'leader',
            'is_active' => true,
            'notes' => 'Directora de alabanza',
        ]);

        User::create([
            'name' => 'Roberto Sánchez',
            'email' => 'roberto@iglesia.com',
            'password' => Hash::make('password'),
            'phone' => '50243456789',
            'role' => 'leader',
            'is_active' => true,
            'notes' => 'Coordinador de eventos',
        ]);
    }

    /**
     * Crear usuarios miembros
     */
    private function createMembers(): void
    {
        $this->command->info('🎵 Creando usuarios miembros...');

        $members = [
            [
                'name' => 'Juan Pérez',
                'email' => 'juan@iglesia.com',
                'phone' => '50244567890',
                'instruments' => ['Guitarra Eléctrica', 'Voz Tenor'],
            ],
            [
                'name' => 'Ana López',
                'email' => 'ana@iglesia.com',
                'phone' => '50245678901',
                'instruments' => ['Piano/Teclado', 'Voz Soprano'],
            ],
            [
                'name' => 'Pedro Hernández',
                'email' => 'pedro@iglesia.com',
                'phone' => '50246789012',
                'instruments' => ['Batería Acústica'],
            ],
            [
                'name' => 'Lucía Torres',
                'email' => 'lucia@iglesia.com',
                'phone' => '50247890123',
                'instruments' => ['Voz Soprano'],
            ],
            [
                'name' => 'Diego Ramírez',
                'email' => 'diego@iglesia.com',
                'phone' => '50248901234',
                'instruments' => ['Bajo Eléctrico', 'Guitarra Acústica'],
            ],
            [
                'name' => 'Sofia Castro',
                'email' => 'sofia@iglesia.com',
                'phone' => '50249012345',
                'instruments' => ['Congas', 'Voz Soprano'],
            ],
            [
                'name' => 'Miguel Ángel Ruiz',
                'email' => 'miguel@iglesia.com',
                'phone' => '5012345678',
                'instruments' => ['Saxofón Alto'],
            ],
            [
                'name' => 'Carmen Vásquez',
                'email' => 'carmen@iglesia.com',
                'phone' => '50251234567',
                'instruments' => ['Trompeta', 'Voz Soprano'],
            ],
        ];

        foreach ($members as $memberData) {
            $instruments = $memberData['instruments'] ?? [];
            unset($memberData['instruments']);

            $member = User::create([
                ...$memberData,
                'password' => Hash::make('password'),
                'role' => 'member',
                'is_active' => true,
            ]);

            // Asignar instrumentos
            $instrumentIds = Instrument::whereIn('name', $instruments)->pluck('id');
            $member->instruments()->attach($instrumentIds);
        }
    }

    /**
     * Crear canciones
     */
    private function createSongs(): void
    {
        $this->command->info('🎼 Creando canciones...');

        $songs = [
            [
                'title' => 'Dios de pactos',
                'artist' => 'Marcos Witt',
                'key' => 'E',
                'tempo' => 72,
                'duration' => 420,
                'lyrics_url' => 'https://example.com/dios-de-pactos',
                'chords_url' => 'https://example.com/chords/dios-de-pactos',
                'youtube_url' => 'https://youtube.com/watch?v=example1',
            ],
            [
                'title' => 'Aquí estoy',
                'artist' => 'Danilo Montero',
                'key' => 'G',
                'tempo' => 68,
                'duration' => 360,
                'lyrics_url' => 'https://example.com/aqui-estoy',
                'chords_url' => 'https://example.com/chords/aqui-estoy',
                'youtube_url' => 'https://youtube.com/watch?v=example2',
            ],
            [
                'title' => 'Rey de gloria',
                'artist' => 'Marco Barrientos',
                'key' => 'D',
                'tempo' => 76,
                'duration' => 300,
                'lyrics_url' => 'https://example.com/rey-de-gloria',
                'chords_url' => 'https://example.com/chords/rey-de-gloria',
                'youtube_url' => 'https://youtube.com/watch?v=example3',
            ],
            [
                'title' => 'Tu eres santo',
                'artist' => 'Hillsong United',
                'key' => 'C',
                'tempo' => 70,
                'duration' => 380,
                'lyrics_url' => 'https://example.com/tu-eres-santo',
                'chords_url' => 'https://example.com/chords/tu-eres-santo',
                'youtube_url' => 'https://youtube.com/watch?v=example4',
            ],
            [
                'title' => 'El poder de tu amor',
                'artist' => 'Danilo Montero',
                'key' => 'A',
                'tempo' => 65,
                'duration' => 340,
                'lyrics_url' => 'https://example.com/el-poder-de-tu-amor',
                'chords_url' => 'https://example.com/chords/el-poder-de-tu-amor',
                'youtube_url' => 'https://youtube.com/watch?v=example5',
            ],
            [
                'title' => 'Maravilloso Dios',
                'artist' => 'Marcos Witt',
                'key' => 'F',
                'tempo' => 74,
                'duration' => 290,
                'lyrics_url' => 'https://example.com/maravilloso-dios',
                'chords_url' => 'https://example.com/chords/maravilloso-dios',
                'youtube_url' => 'https://youtube.com/watch?v=example6',
            ],
        ];

        foreach ($songs as $song) {
            Song::create($song);
        }
    }

    /**
     * Crear eventos
     */
    private function createEvents(): void
    {
        $this->command->info('📅 Creando eventos...');

        // Eventos próximos
        $events = [
            [
                'name' => 'Culto Dominical',
                'date' => Carbon::now()->addDays(2)->setHour(10)->setMinute(0),
                'location' => 'Templo Principal',
                'description' => 'Culto de adoración y predicación dominical',
            ],
            [
                'name' => 'Servicio de Jóvenes',
                'date' => Carbon::now()->addDays(5)->setHour(18)->setMinute(30),
                'location' => 'Salón de Jóvenes',
                'description' => 'Reunión semanal de jóvenes',
            ],
            [
                'name' => 'Noche de Adoración',
                'date' => Carbon::now()->addDays(7)->setHour(19)->setMinute(0),
                'location' => 'Templo Principal',
                'description' => 'Noche especial de adoración y alabanza',
            ],
            [
                'name' => 'Culto de Oración',
                'date' => Carbon::now()->addDays(9)->setHour(6)->setMinute(0),
                'location' => 'Sala de Oración',
                'description' => 'Culto de oración madrugador',
            ],
        ];

        $leaders = User::where('role', 'leader')->get();
        $members = User::where('role', 'member')->get();
        $songs = Song::all();

        foreach ($events as $index => $eventData) {
            $event = Event::create([
                ...$eventData,
                'created_by' => $leaders->random()->id,
            ]);

            // Asignar miembros al evento
            $assignedMembers = $members->random(min(4, $members->count()));
            foreach ($assignedMembers as $member) {
                $event->users()->attach($member->id, [
                    'status' => ['confirmed', 'pending', 'confirmed'][rand(0, 2)],
                    'notes' => null,
                ]);
            }

            // Asignar canciones al evento
            $assignedSongs = $songs->random(min(4, $songs->count()));
            foreach ($assignedSongs as $song) {
                $event->songs()->attach($song->id, [
                    'order' => rand(1, 5),
                    'notes' => null,
                ]);
            }
        }

        // Crear algunos eventos pasados para histórico
        Event::create([
            'name' => 'Culto Especial de Fin de Año',
            'date' => Carbon::now()->subDays(60)->setHour(20)->setMinute(0),
            'location' => 'Templo Principal',
            'description' => 'Celebración de fin de año',
            'created_by' => $leaders->first()->id,
        ]);

        Event::create([
            'name' => 'Conferencia de Alabanza',
            'date' => Carbon::now()->subDays(30)->setHour(15)->setMinute(0),
            'location' => 'Centro de Convenciones',
            'description' => 'Conferencia especial sobre alabanza',
            'created_by' => $leaders->first()->id,
        ]);
    }

    /**
     * Crear ensayos
     */
    private function createRehearsals(): void
    {
        $this->command->info('🎤 Creando ensayos...');

        $events = Event::where('date', '>', Carbon::now())->get();
        $leaders = User::where('role', 'leader')->get();

        // Crear ensayos para eventos próximos
        foreach ($events as $event) {
            Rehearsal::create([
                'event_id' => $event->id,
                'date' => $event->date->copy()->subDays(rand(1, 3))->setHour(18)->setMinute(0),
                'location' => 'Sala de Ensayos',
                'notes' => 'Ensayo obligatorio para ' . $event->name,
                'created_by' => $leaders->random()->id,
            ]);
        }

        // Crear ensayos sin evento asociado (generales)
        Rehearsal::create([
            'event_id' => null,
            'date' => Carbon::now()->addDays(3)->setHour(17)->setMinute(0),
            'location' => 'Templo Principal',
            'notes' => 'Ensayo general del equipo de alabanza',
            'created_by' => $leaders->first()->id,
        ]);
    }

    /**
     * Crear asistencias
     */
    private function createAttendances(): void
    {
        $this->command->info('✓ Creando asistencias...');

        $members = User::where('role', 'member')->get();
        $rehearsals = Rehearsal::all();
        $events = Event::all();

        // Crear asistencias para ensayos
        foreach ($rehearsals as $rehearsal) {
            foreach ($members->random(min(5, $members->count())) as $member) {
                Attendance::create([
                    'user_id' => $member->id,
                    'rehearsal_id' => $rehearsal->id,
                    'event_id' => null,
                    'status' => ['present', 'absent', 'late'][rand(0, 2)],
                    'notes' => rand(0, 3) === 0 ? 'Llegó un poco tarde por tráfico' : null,
                ]);
            }
        }

        // Crear asistencias para eventos pasados
        $pastEvents = Event::where('date', '<', Carbon::now())->get();
        foreach ($pastEvents as $event) {
            foreach ($members->random(min(5, $members->count())) as $member) {
                Attendance::create([
                    'user_id' => $member->id,
                    'rehearsal_id' => null,
                    'event_id' => $event->id,
                    'status' => ['present', 'present', 'absent'][rand(0, 2)],
                    'notes' => null,
                ]);
            }
        }
    }
}
