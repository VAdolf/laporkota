<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Laporan;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller
{
    public function index()
    {
        $total = Laporan::count();

        $perStatus = Laporan::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusCards = collect(Laporan::STATUS)->mapWithKeys(
            fn ($s) => [$s => (int) ($perStatus[$s] ?? 0)]
        );

        $perKategori = Kategori::withCount('laporans')
            ->orderByDesc('laporans_count')
            ->get();

        // Tren 6 bulan terakhir (dihitung di PHP agar kompatibel MySQL maupun SQLite).
        $awal = now()->startOfMonth()->subMonths(5);
        $dibuat = Laporan::where('created_at', '>=', $awal)
            ->get(['created_at'])
            ->groupBy(fn ($l) => $l->created_at->format('Y-m'))
            ->map->count();

        $perBulan = collect(range(0, 5))->map(function ($i) use ($awal, $dibuat) {
            $bulan = $awal->copy()->addMonths($i);

            return [
                'label' => $bulan->translatedFormat('M Y'),
                'total' => (int) ($dibuat[$bulan->format('Y-m')] ?? 0),
            ];
        });

        // Rata-rata waktu penyelesaian (hari): dari laporan dibuat sampai tanggapan berstatus selesai.
        $rataSelesaiHari = Laporan::where('status', Laporan::STATUS_SELESAI)
            ->with(['tanggapans' => fn ($q) => $q->where('status', Laporan::STATUS_SELESAI)->latest()])
            ->get()
            ->map(function ($l) {
                $t = $l->tanggapans->first();

                return $t ? abs($l->created_at->diffInHours($t->created_at)) / 24 : null;
            })
            ->filter(fn ($v) => $v !== null)
            ->avg();

        $terbaru = Laporan::with(['kategori', 'user'])->latest()->take(5)->get();

        $titik = Laporan::with('kategori')
            ->whereNotNull('lat')->whereNotNull('lng')
            ->get()
            ->filter(fn ($l) => $l->hasLocation())
            ->map(fn ($l) => [
                'judul'    => $l->judul,
                'kategori' => $l->kategori->nama_kategori ?? 'Umum',
                'status'   => $l->status,
                'lat'      => $l->lat,
                'lng'      => $l->lng,
                'url'      => route('kelola.laporan.show', $l),
            ])
            ->values();

        return view('monitoring.index', compact(
            'total', 'statusCards', 'perKategori', 'perBulan', 'rataSelesaiHari', 'terbaru', 'titik'
        ));
    }
}
