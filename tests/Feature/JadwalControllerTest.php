<?php

use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('authenticated user can list all jadwal', function () {
    Jadwal::factory()->count(3)->create();

    $response = actingAs($this->user)->getJson('/jadwal');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'data' => [
                '*' => ['id', 'nama', 'waktu_aktif_pertama', 'waktu_aktif_kedua', 'lama_operasi', 'aktif', 'hari'],
            ],
        ]);
});

test('authenticated user can create jadwal', function () {
    $jadwalData = [
        'nama' => 'Jadwal Pagi',
        'waktu_aktif_pertama' => '06:00',
        'waktu_aktif_kedua' => '18:00',
        'lama_operasi' => 30,
        'aktif' => true,
        'hari' => ['senin', 'rabu', 'jumat'],
    ];

    $response = actingAs($this->user)->postJson('/jadwal', $jadwalData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => ['id', 'nama', 'waktu_aktif_pertama', 'lama_operasi'],
        ]);

    assertDatabaseHas('jadwal', [
        'nama' => 'Jadwal Pagi',
        'waktu_aktif_pertama' => '06:00',
        'lama_operasi' => 30,
    ]);
});

test('authenticated user can update jadwal by nama', function () {
    // Create initial jadwal
    $jadwal = Jadwal::factory()->create([
        'nama' => 'Jadwal Pagi',
        'lama_operasi' => 30,
    ]);

    // Update with same nama
    $updateData = [
        'nama' => 'Jadwal Pagi',
        'waktu_aktif_pertama' => '07:00',
        'waktu_aktif_kedua' => '19:00',
        'lama_operasi' => 45,
        'aktif' => false,
        'hari' => ['selasa', 'kamis'],
    ];

    $response = actingAs($this->user)->postJson('/jadwal', $updateData);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Jadwal berhasil diperbarui',
        ]);

    assertDatabaseHas('jadwal', [
        'id' => $jadwal->id,
        'nama' => 'Jadwal Pagi',
        'lama_operasi' => 45,
    ]);

    // Ensure only one record exists
    expect(Jadwal::where('nama', 'Jadwal Pagi')->count())->toBe(1);
});

test('authenticated user can view single jadwal', function () {
    $jadwal = Jadwal::factory()->create();

    $response = actingAs($this->user)->getJson("/jadwal/{$jadwal->id}");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $jadwal->id,
                'nama' => $jadwal->nama,
            ],
        ]);
});

test('authenticated user can update jadwal', function () {
    $jadwal = Jadwal::factory()->create();

    $updateData = [
        'nama' => 'Jadwal Updated',
        'waktu_aktif_pertama' => '07:00',
        'waktu_aktif_kedua' => '19:00',
        'lama_operasi' => 45,
        'aktif' => false,
        'hari' => ['selasa', 'kamis'],
    ];

    $response = actingAs($this->user)->putJson("/jadwal/{$jadwal->id}", $updateData);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Jadwal berhasil diperbarui',
        ]);

    assertDatabaseHas('jadwal', [
        'id' => $jadwal->id,
        'nama' => 'Jadwal Updated',
        'lama_operasi' => 45,
    ]);
});

test('authenticated user can delete jadwal', function () {
    $jadwal = Jadwal::factory()->create();

    $response = actingAs($this->user)->deleteJson("/jadwal/{$jadwal->id}");

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Jadwal berhasil dihapus',
        ]);

    assertDatabaseMissing('jadwal', [
        'id' => $jadwal->id,
    ]);
});

test('guest cannot access jadwal endpoints', function () {
    $jadwal = Jadwal::factory()->create();

    $this->getJson('/jadwal')->assertStatus(401);
    $this->postJson('/jadwal', [])->assertStatus(401);
    $this->getJson("/jadwal/{$jadwal->id}")->assertStatus(401);
    $this->putJson("/jadwal/{$jadwal->id}", [])->assertStatus(401);
    $this->deleteJson("/jadwal/{$jadwal->id}")->assertStatus(401);
});

test('registration route is disabled', function () {
    $response = $this->get('/register');

    $response->assertStatus(404);
});
