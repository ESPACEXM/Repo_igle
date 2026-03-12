<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prueba que el dashboard se cargue correctamente para un usuario autenticado.
     */
    public function test_dashboard_loads()
    {
        $user = User::factory()->create(['role' => 'leader']);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }
}
