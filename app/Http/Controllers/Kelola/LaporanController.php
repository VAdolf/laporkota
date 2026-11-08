<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Pengelolaan laporan oleh admin (ubah status + tanggapan).
 * Eksekutif hanya boleh melihat (index & show).
 */
class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $status = in_array($request->status, Laporan::STATUS, true) ? $request->status : null;
        $keyword = trim((string) $request->q);

        $laporans = Laporan::with(['kategori', 'user'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($request->filled('kategori_id'), fn ($q) => $q->where('kategori_id', $request->kategori_id))
            ->when($keyword !== '', function ($q) use ($keyword) {
                $q->where(function ($w) use ($keyword) {
                    $w->where('judul', 'like', "%{$keyword}%")
                      ->orWhere('deskripsi', 'like', "%{$keyword}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('kelola.laporan.index', compact('laporans', 'kategoris', 'status', 'keyword'));
    }

    public function show(Laporan $laporan)
    {
        $laporan->load(['kategori', 'user', 'tanggapans.user']);

        return view('kelola.laporan.show', compact('laporan'));
    }

    public function tanggapi(Request $request, Laporan $laporan)
    {
        $data = $request->validate([
            'status'     => ['required', Rule::in(Laporan::STATUS)],
            'catatan'    => ['required', 'string', 'max:2000'],
            'foto_hasil' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ], [
            'status.required'  => 'Pilih status laporan.',
            'status.in'        => 'Status yang dipilih tidak valid.',
            'catatan.required' => 'Catatan tanggapan wajib diisi.',
            'foto_hasil.image' => 'Foto hasil harus berupa gambar.',
            'foto_hasil.mimes' => 'Foto hasil harus berformat JPG atau PNG.',
            'foto_hasil.max'   => 'Ukuran foto hasil maksimal 2 MB.',
        ]);

        DB::transaction(function () use ($request, $laporan, $data) {
            $fotoPath = $request->hasFile('foto_hasil')
                ? $request->file('foto_hasil')->store('tanggapan_hasil', 'public')
                : null;

            $laporan->tanggapans()->create([
                'user_id'    => Auth::id(),
                'status'     => $data['status'],
                'catatan'    => $data['catatan'],
                'foto_hasil' => $fotoPath,
            ]);

            $laporan->update(['status' => $data['status']]);
        });

        return redirect()
            ->route('kelola.laporan.show', $laporan)
            ->with('success', 'Tanggapan tersimpan dan status laporan diperbarui.');
    }
}
