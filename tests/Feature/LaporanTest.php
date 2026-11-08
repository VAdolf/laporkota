<?php

use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $this->kategori = Kategori::create(['nama_kategori' => 'Jalan Rusak']);
    $this->warga = User::factory()->create(['role' => 'warga']);
});

function dataLaporan(Kategori $kategori, array $override = []): array
{
    return array_merge([
        'kategori_id' => $kategori->id,
        'judul' => 'Jalan berlubang',
        'deskripsi' => 'Lubang dalam di depan pasar.',
        'lat' => -7.2575,
        'lng' => 112.7521,
        'foto_bukti' => UploadedFile::fake()->image('bukti.jpg', 600, 400)->size(500),
    ], $override);
}

test('form laporan menampilkan peta dan memuat Leaflet', function () {
    $this->actingAs($this->warga)
        ->get('/lapor')
        ->assertOk()
        ->assertSee('id="peta"', false)
        ->assertSee('leaflet.js', false)
        ->assertSee('Jalan Rusak');
});

test('warga dapat mengirim laporan lengkap dengan koordinat', function () {
    $this->actingAs($this->warga)
        ->post('/lapor', dataLaporan($this->kategori))
        ->assertRedirect(route('dashboard'));

    $laporan = Laporan::first();
    expect($laporan)->not->toBeNull()
        ->and($laporan->user_id)->toBe($this->warga->id)
        ->and($laporan->status)->toBe('menunggu')
        ->and($laporan->lat)->toBe(-7.2575);

    Storage::disk('public')->assertExists($laporan->foto_bukti);
});

test('laporan ditolak bila lokasi belum dipilih', function () {
    $this->actingAs($this->warga)
        ->post('/lapor', dataLaporan($this->kategori, ['lat' => '', 'lng' => '']))
        ->assertSessionHasErrors(['lat', 'lng']);

    expect(Laporan::count())->toBe(0);
});

test('koordinat 0,0 dianggap belum memilih lokasi', function () {
    $this->actingAs($this->warga)
        ->post('/lapor', dataLaporan($this->kategori, ['lat' => 0, 'lng' => 0]))
        ->assertSessionHasErrors('lat');
});

test('koordinat di luar rentang ditolak', function () {
    $this->actingAs($this->warga)
        ->post('/lapor', dataLaporan($this->kategori, ['lat' => 120, 'lng' => 400]))
        ->assertSessionHasErrors(['lat', 'lng']);
});

test('foto lebih dari 2 MB ditolak', function () {
    $this->actingAs($this->warga)
        ->post('/lapor', dataLaporan($this->kategori, [
            'foto_bukti' => UploadedFile::fake()->image('besar.jpg')->size(3000),
        ]))
        ->assertSessionHasErrors('foto_bukti');
});

test('riwayat hanya menampilkan laporan milik sendiri', function () {
    $lain = User::factory()->create();
    Laporan::create(['user_id' => $this->warga->id, 'kategori_id' => $this->kategori->id, 'judul' => 'Milik saya', 'deskripsi' => 'x', 'foto_bukti' => 'a.jpg', 'lat' => -7.2, 'lng' => 112.7]);
    Laporan::create(['user_id' => $lain->id, 'kategori_id' => $this->kategori->id, 'judul' => 'Milik orang lain', 'deskripsi' => 'x', 'foto_bukti' => 'b.jpg', 'lat' => -7.2, 'lng' => 112.7]);

    $this->actingAs($this->warga)->get('/dashboard')
        ->assertOk()
        ->assertSee('Milik saya')
        ->assertDontSee('Milik orang lain');
});

test('warga tidak bisa melihat atau menghapus laporan orang lain', function () {
    $lain = User::factory()->create();
    $laporan = Laporan::create(['user_id' => $lain->id, 'kategori_id' => $this->kategori->id, 'judul' => 'Rahasia', 'deskripsi' => 'x', 'foto_bukti' => 'b.jpg']);

    $this->actingAs($this->warga)->get("/laporan/{$laporan->id}")->assertNotFound();
    $this->actingAs($this->warga)->delete("/laporan/{$laporan->id}")->assertNotFound();
    expect(Laporan::count())->toBe(1);
});

test('laporan berstatus menunggu dapat dihapus beserta fotonya', function () {
    $this->actingAs($this->warga)->post('/lapor', dataLaporan($this->kategori));
    $laporan = Laporan::first();

    $this->actingAs($this->warga)->delete("/laporan/{$laporan->id}")->assertRedirect(route('dashboard'));

    expect(Laporan::count())->toBe(0);
    Storage::disk('public')->assertMissing($laporan->foto_bukti);
});

test('laporan yang sudah diproses tidak dapat dihapus', function () {
    $laporan = Laporan::create(['user_id' => $this->warga->id, 'kategori_id' => $this->kategori->id, 'judul' => 'Diproses', 'deskripsi' => 'x', 'foto_bukti' => 'a.jpg', 'status' => 'diproses']);

    $this->actingAs($this->warga)->delete("/laporan/{$laporan->id}")->assertSessionHasErrors('laporan');

    expect(Laporan::count())->toBe(1);
});

test('status diproses dan ditolak tampil dengan label yang benar di dashboard', function () {
    foreach (['diproses' => 'Diproses', 'ditolak' => 'Ditolak'] as $status => $label) {
        Laporan::create(['user_id' => $this->warga->id, 'kategori_id' => $this->kategori->id, 'judul' => "L-$status", 'deskripsi' => 'x', 'foto_bukti' => 'a.jpg', 'status' => $status]);
    }

    $this->actingAs($this->warga)->get('/dashboard')
        ->assertSee('bg-blue-100', false)
        ->assertSee('bg-red-100', false)
        ->assertSee('Diproses')
        ->assertSee('Ditolak');
});

test('detail laporan menampilkan peta bila ada koordinat', function () {
    $laporan = Laporan::create(['user_id' => $this->warga->id, 'kategori_id' => $this->kategori->id, 'judul' => 'Dengan peta', 'deskripsi' => 'x', 'foto_bukti' => 'a.jpg', 'lat' => -7.25, 'lng' => 112.75]);

    $this->actingAs($this->warga)->get("/laporan/{$laporan->id}")
        ->assertOk()
        ->assertSee('id="peta-detail"', false)
        ->assertSee('leaflet.js', false);
});
