<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLaporanRequest;
use App\Models\Kategori;
use App\Models\Laporan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    public function create()
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('laporan.create', compact('kategoris'));
    }

    public function store(StoreLaporanRequest $request)
    {
        $data = $request->validated();

        $fotoPath = $request->file('foto_bukti')->store('laporan_bukti', 'public');

        Laporan::create([
            'user_id'     => Auth::id(),
            'kategori_id' => $data['kategori_id'],
            'judul'       => $data['judul'],
            'deskripsi'   => $data['deskripsi'],
            'lat'         => $data['lat'],
            'lng'         => $data['lng'],
            'foto_bukti'  => $fotoPath,
            'status'      => Laporan::STATUS_MENUNGGU,
        ]);

        return redirect()->route('dashboard')->with('success', 'Laporan Anda berhasil dikirim.');
    }

    /** Detail laporan milik warga yang sedang login. */
    public function show($id)
    {
        $laporan = Laporan::with(['kategori', 'tanggapans.user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('laporan.show', compact('laporan'));
    }

    public function destroy($id)
    {
        $laporan = Laporan::where('user_id', Auth::id())->findOrFail($id);

        // Laporan yang sudah ditangani tidak boleh dihapus agar data monitoring tetap utuh.
        if ($laporan->status !== Laporan::STATUS_MENUNGGU) {
            return redirect()->route('dashboard')
                ->withErrors(['laporan' => 'Laporan yang sudah diproses tidak dapat dihapus.']);
        }

        if ($laporan->foto_bukti && Storage::disk('public')->exists($laporan->foto_bukti)) {
            Storage::disk('public')->delete($laporan->foto_bukti);
        }

        $laporan->delete();

        return redirect()->route('dashboard')->with('success', 'Laporan berhasil dihapus.');
    }
}
