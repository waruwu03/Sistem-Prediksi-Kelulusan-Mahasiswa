<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_admin_can_open_dashboard(): void
    {
        $response = $this->actingAs(User::factory()->create())->get('/');

        $response->assertOk();
        $response->assertSee('Sistem Prediksi Kelulusan Mahasiswa');
    }
}
