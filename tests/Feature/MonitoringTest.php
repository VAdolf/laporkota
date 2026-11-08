<?php

use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\Tanggapan;
use App\Models\User;

test('monitoring menampilkan ringkasan, peta, dan rata-rata penyelesaian', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $warga = User::factory()->create();
    $k = Kategori::create(['nama_kategori' => 'Drainase']);

    $selesai = Laporan::create(['user_id' => $warga->id, 'kategori_id' => $k->id, 'judul' => 'Got tersumbat', 'deskripsi' => 'x', 'foto_bukti' => 'a.jpg', 'lat' => -7.25, 'lng' => 112.75, 'status' => 'selesai']);
    $selesai->forceFill(['created_at' => now()->subDays(2)])->save();
    Tanggapan::create(['laporan_id' => $selesai->id, 'user_id' => $admin->id, 'status' => 'selesai', 'catatan' => 'Beres']);

    Laporan::create(['user_id' => $warga->id, 'kategori_id' => $k->id, 'judul' => 'Tanpa lokasi', 'deskripsi' => 'x', 'foto_bukti' => 'b.jpg', 'lat' => 0, 'lng' => 0]);

    $res = $this->actingAs($admin)->get('/monitoring')->assertOk();

    $res->assertSee('Total laporan')
        ->assertSee('Drainase')
        ->assertSee('2,0 hari')
        ->assertSee('peta-sebaran', false)
        ->assertSee('Got tersumbat');

    // laporan berkoordinat 0,0 tidak ikut di peta (hanya 1 titik)
    expect(substr_count($res->getContent(), '"status":"selesai"'))->toBe(1);
    expect($res->getContent())->not->toContain('"judul":"Tanpa lokasi"');
});

test('monitoring tidak error saat belum ada data', function () {
    $eksekutif = User::factory()->create(['role' => 'eksekutif']);

    $this->actingAs($eksekutif)->get('/monitoring')
        ->assertOk()
        ->assertSee('Belum ada laporan selesai');
});
