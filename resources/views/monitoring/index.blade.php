<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Monitoring laporan</h2>
    </x-slot>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    @endpush

    @php
        $maksKategori = max(1, $perKategori->max('laporans_count') ?? 0);
        $maksBulan = max(1, $perBulan->max('total') ?? 0);
    @endphp

    <div class="mx-auto max-w-6xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

        {{-- Ringkasan --}}
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-5">
            <div class="rounded-lg bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Total laporan</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $total }}</p>
            </div>
            @foreach($statusCards as $status => $jumlah)
                <div class="rounded-lg bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">{{ ucfirst($status) }}</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $jumlah }}</p>
                </div>
            @endforeach
        </div>

        <div class="rounded-lg bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">Rata-rata waktu penyelesaian</p>
            <p class="mt-1 text-lg font-semibold text-gray-900">
                @if($rataSelesaiHari !== null)
                    {{ number_format($rataSelesaiHari, 1, ',', '.') }} hari
                @else
                    Belum ada laporan selesai
                @endif
            </p>
        </div>

        {{-- Peta sebaran --}}
        <div class="rounded-lg bg-white p-4 shadow-sm sm:p-5">
            <h3 class="text-base font-semibold text-gray-900">Peta sebaran laporan</h3>
            <div id="peta-sebaran" class="mt-3 h-80 w-full rounded-md border border-gray-300 sm:h-96" role="application" aria-label="Peta sebaran laporan"></div>
            <ul class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-sm text-gray-600">
                <li class="flex items-center gap-1.5"><span class="h-3 w-3 rounded-full" style="background:#d97706"></span>Menunggu</li>
                <li class="flex items-center gap-1.5"><span class="h-3 w-3 rounded-full" style="background:#2563eb"></span>Diproses</li>
                <li class="flex items-center gap-1.5"><span class="h-3 w-3 rounded-full" style="background:#16a34a"></span>Selesai</li>
                <li class="flex items-center gap-1.5"><span class="h-3 w-3 rounded-full" style="background:#dc2626"></span>Ditolak</li>
            </ul>
            @if($titik->isEmpty())
                <p class="mt-2 text-sm text-gray-500">Belum ada laporan dengan titik lokasi.</p>
            @endif
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Per kategori --}}
            <div class="rounded-lg bg-white p-4 shadow-sm sm:p-5">
                <h3 class="text-base font-semibold text-gray-900">Laporan per kategori</h3>
                <ul class="mt-4 space-y-3">
                    @forelse($perKategori as $k)
                        <li>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700">{{ $k->nama_kategori }}</span>
                                <span class="font-semibold text-gray-900">{{ $k->laporans_count }}</span>
                            </div>
                            <div class="mt-1 h-2 rounded-full bg-gray-100">
                                <div class="h-2 rounded-full bg-blue-600" style="width: {{ round($k->laporans_count / $maksKategori * 100) }}%"></div>
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-gray-500">Belum ada kategori.</li>
                    @endforelse
                </ul>
            </div>

            {{-- Per bulan --}}
            <div class="rounded-lg bg-white p-4 shadow-sm sm:p-5">
                <h3 class="text-base font-semibold text-gray-900">Laporan 6 bulan terakhir</h3>
                <ul class="mt-4 space-y-3">
                    @foreach($perBulan as $b)
                        <li>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700">{{ $b['label'] }}</span>
                                <span class="font-semibold text-gray-900">{{ $b['total'] }}</span>
                            </div>
                            <div class="mt-1 h-2 rounded-full bg-gray-100">
                                <div class="h-2 rounded-full bg-blue-600" style="width: {{ round($b['total'] / $maksBulan * 100) }}%"></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Terbaru --}}
        <div class="rounded-lg bg-white shadow-sm">
            <div class="flex items-center justify-between p-4 sm:p-5">
                <h3 class="text-base font-semibold text-gray-900">Laporan terbaru</h3>
                <a href="{{ route('kelola.laporan.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">Lihat semua</a>
            </div>
            <ul class="divide-y divide-gray-100 border-t border-gray-100">
                @forelse($terbaru as $l)
                    <li>
                        <a href="{{ route('kelola.laporan.show', $l) }}" class="flex flex-wrap items-center justify-between gap-2 px-4 py-3 hover:bg-gray-50 sm:px-5">
                            <span>
                                <span class="block font-medium text-gray-900">{{ $l->judul }}</span>
                                <span class="text-sm text-gray-500">{{ $l->kategori->nama_kategori ?? 'Umum' }} &middot; {{ $l->created_at->diffForHumans() }}</span>
                            </span>
                            <x-status-badge :status="$l->status" />
                        </a>
                    </li>
                @empty
                    <li class="px-5 py-4 text-sm text-gray-500">Belum ada laporan masuk.</li>
                @endforelse
            </ul>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            (function () {
                const titik = @json($titik);
                const warna = { menunggu: '#d97706', diproses: '#2563eb', selesai: '#16a34a', ditolak: '#dc2626' };

                const peta = L.map('peta-sebaran').setView(
                    [{{ config('laporkota.map.lat') }}, {{ config('laporkota.map.lng') }}],
                    {{ config('laporkota.map.zoom') }}
                );

                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; kontributor <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(peta);

                const batas = [];
                titik.forEach(function (t) {
                    const w = warna[t.status] || '#6b7280';
                    const m = L.circleMarker([t.lat, t.lng], {
                        radius: 9, color: '#ffffff', weight: 2, fillColor: w, fillOpacity: 0.95
                    }).addTo(peta);

                    // Popup dibangun dengan DOM (textContent) agar aman dari XSS.
                    const isi = document.createElement('div');
                    const judul = document.createElement('strong');
                    judul.textContent = t.judul;
                    const info = document.createElement('div');
                    info.textContent = t.kategori + ' - ' + t.status;
                    const tautan = document.createElement('a');
                    tautan.href = t.url;
                    tautan.textContent = 'Lihat detail';
                    isi.append(judul, info, tautan);
                    m.bindPopup(isi);

                    batas.push([t.lat, t.lng]);
                });

                if (batas.length) { peta.fitBounds(batas, { padding: [30, 30], maxZoom: 16 }); }
            })();
        </script>
    @endpush
</x-app-layout>
