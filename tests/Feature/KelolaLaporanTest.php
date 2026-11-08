<?php

use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\Tanggapan;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $this->kategori = Kategori::create(['nama_kategori' => 'Sampah']);
    $this->warga = User::factory()->create(['role' => 'warga']);
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->eksekutif = User::factory()->create(['role' => 'eksekutif']);
    $this->laporan = Laporan::create([
        'user_id' => $this->warga->id,
        'kategori_id' => $this->kategori->id,
        'judul' => 'Tumpukan sampah',
        'deskripsi' => 'Menutup saluran.',
        'foto_bukti' => 'laporan_bukti/a.jpg',
        'lat' => -7.25,
        'lng' => 112.75,
    ]);
});

test('admin memberi tanggapan: status berubah dan riwayat tersimpan', function () {
    $this->actingAs($this->admin)
        ->post("/kelola/laporan/{$this->laporan->id}/tanggapan", [
            'status' => 'selesai',
            'catatan' => 'Sampah sudah diangkut.',
            'foto_hasil' => UploadedFile::fake()->image('hasil.jpg')->size(300),
        ])
        ->assertRedirect(route('kelola.laporan.show', $this->laporan));

    expect($this->laporan->fresh()->status)->toBe('selesai');

    $t = Tanggapan::first();
    expect($t->laporan_id)->toBe($this->laporan->id)
        ->and($t->user_id)->toBe($this->admin->id)
        ->and($t->status)->toBe('selesai')
        ->and($t->catatan)->toBe('Sampah sudah diangkut.');
    Storage::disk('public')->assertExists($t->foto_hasil);
});

test('tanggapan tanpa foto tetap diterima', function () {
    $this->actingAs($this->admin)
        ->post("/kelola/laporan/{$this->laporan->id}/tanggapan", ['status' => 'diproses', 'catatan' => 'Sedang dijadwalkan.'])
        ->assertSessionHasNoErrors();

    expect($this->laporan->fresh()->status)->toBe('diproses');
    expect(Tanggapan::first()->foto_hasil)->toBeNull();
});

test('status tidak valid dan catatan kosong ditolak', function () {
    $this->actingAs($this->admin)
        ->post("/kelola/laporan/{$this->laporan->id}/tanggapan", ['status' => 'bebas', 'catatan' => ''])
        ->assertSessionHasErrors(['status', 'catatan']);

    expect($this->laporan->fresh()->status)->toBe('menunggu');
    expect(Tanggapan::count())->toBe(0);
});

test('eksekutif dan warga tidak boleh menanggapi laporan', function () {
    foreach ([$this->eksekutif, $this->warga] as $user) {
        $this->actingAs($user)
            ->post("/kelola/laporan/{$this->laporan->id}/tanggapan", ['status' => 'selesai', 'catatan' => 'x'])
            ->assertForbidden();
    }

    expect($this->laporan->fresh()->status)->toBe('menunggu');
});

test('form tanggapan hanya tampil untuk admin', function () {
    $this->actingAs($this->admin)->get("/kelola/laporan/{$this->laporan->id}")
        ->assertOk()->assertSee('Simpan tanggapan');

    $this->actingAs($this->eksekutif)->get("/kelola/laporan/{$this->laporan->id}")
        ->assertOk()->assertDontSee('Simpan tanggapan');
});

test('warga melihat riwayat tanggapan pada detail laporannya', function () {
    Tanggapan::create(['laporan_id' => $this->laporan->id, 'user_id' => $this->admin->id, 'status' => 'diproses', 'catatan' => 'Tim sedang menuju lokasi.']);

    $this->actingAs($this->warga)->get("/laporan/{$this->laporan->id}")
        ->assertOk()
        ->assertSee('Tim sedang menuju lokasi.');
});

test('filter status dan pencarian pada daftar laporan', function () {
    Laporan::create(['user_id' => $this->warga->id, 'kategori_id' => $this->kategori->id, 'judul' => 'Lampu mati', 'deskripsi' => 'PJU padam', 'foto_bukti' => 'x.jpg', 'status' => 'selesai']);

    $this->actingAs($this->admin)->get('/kelola/laporan?status=selesai')
        ->assertOk()->assertSee('Lampu mati')->assertDontSee('Tumpukan sampah');

    $this->actingAs($this->admin)->get('/kelola/laporan?q=sampah')
        ->assertOk()->assertSee('Tumpukan sampah')->assertDontSee('Lampu mati');

    // status sembarang diabaikan, tidak error
    $this->actingAs($this->admin)->get('/kelola/laporan?status=ngawur')->assertOk();
});
