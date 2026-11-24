<?php

namespace Tests\Feature;

use App\Models\Pekerjaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewRenderTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->user = User::first();
    }

    public function test_dashboard_renders(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertStatus(200);
    }

    public function test_command_center_renders(): void
    {
        $response = $this->actingAs($this->user)->get(route('monitoring.balai'));
        $response->assertStatus(200);
    }

    public function test_pekerjaan_index_renders(): void
    {
        $response = $this->actingAs($this->user)->get(route('pekerjaan.index'));
        $response->assertStatus(200);
    }

    public function test_pekerjaan_saya_renders(): void
    {
        $response = $this->actingAs($this->user)->get(route('pekerjaan.saya'));
        $response->assertStatus(200);
    }

    public function test_pekerjaan_show_renders(): void
    {
        $pekerjaan = Pekerjaan::first();
        if ($pekerjaan) {
            $response = $this->actingAs($this->user)->get(route('pekerjaan.show', $pekerjaan));
            $response->assertStatus(200);
        }
    }
}
