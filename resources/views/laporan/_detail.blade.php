{{-- Partial detail laporan: dipakai halaman warga dan halaman kelola. Butuh $laporan (dengan kategori, tanggapans.user). --}}
<div class="overflow-hidden rounded-lg bg-white shadow-sm">
    <img src="{{ $laporan->foto_url }}" alt="Foto bukti laporan {{ $laporan->judul }}"
         class="max-h-96 w-full object-cover">

    <div class="space-y-4 p-5 sm:p-6">
        <div class="flex flex-wrap items-start justify-between gap-2">
            <h3 class="text-lg font-semibold text-gray-900">{{ $laporan->judul }}</h3>
            <x-status-badge :status="$laporan->status" />
        </div>

        <dl class="grid grid-cols-1 gap-x-6 gap-y-2 text-sm sm:grid-cols-2">
            <div>
                <dt class="text-gray-500">Kategori</dt>
                <dd class="font-medium text-gray-900">{{ $laporan->kategori->nama_kategori ?? 'Umum' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Dilaporkan</dt>
                <dd class="font-medium text-gray-900">{{ $laporan->created_at->translatedFormat('d F Y, H:i') }}</dd>
            </div>
            @isset($tampilkanPelapor)
                <div>
                    <dt class="text-gray-500">Pelapor</dt>
                    <dd class="font-medium text-gray-900">{{ $laporan->user->name ?? '-' }}</dd>
                </div>
            @endisset
        </dl>

        <div>
            <h4 class="text-sm font-medium text-gray-500">Deskripsi</h4>
            <p class="mt-1 whitespace-pre-line text-gray-800">{{ $laporan->deskripsi }}</p>
        </div>

        @if($laporan->hasLocation())
            <div>
                <h4 class="mb-2 text-sm font-medium text-gray-500">Lokasi</h4>
                <div id="peta-detail" class="h-64 w-full rounded-md border border-gray-300" role="application" aria-label="Peta lokasi laporan"></div>
                <p class="mt-2 text-sm text-gray-600">
                    {{ number_format($laporan->lat, 6) }}, {{ number_format($laporan->lng, 6) }}
                    &middot;
                    <a class="font-semibold text-blue-600 hover:underline" target="_blank" rel="noopener"
                       href="https://www.openstreetmap.org/?mlat={{ $laporan->lat }}&mlon={{ $laporan->lng }}#map=18/{{ $laporan->lat }}/{{ $laporan->lng }}">
                        Buka di OpenStreetMap
                    </a>
                </p>
            </div>
        @else
            <p class="text-sm text-gray-500">Laporan ini tidak memiliki titik koordinat. Lihat patokan lokasi pada deskripsi.</p>
        @endif
    </div>
</div>

<div class="mt-6 rounded-lg bg-white p-5 shadow-sm sm:p-6">
    <h3 class="text-base font-semibold text-gray-900">Riwayat penanganan</h3>

    @php($riwayat = $laporan->tanggapans->sortBy('created_at'))

    @if($riwayat->isEmpty())
        <p class="mt-2 text-sm text-gray-500">Belum ada tanggapan dari petugas.</p>
    @else
        <ol class="mt-4 space-y-5 border-l-2 border-gray-200 pl-5">
            @foreach($riwayat as $t)
                <li class="relative">
                    <span class="absolute -left-[1.6rem] top-1.5 h-2.5 w-2.5 rounded-full bg-blue-600"></span>
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        @if($t->status)<x-status-badge :status="$t->status" />@endif
                        <span class="text-gray-500">
                            {{ $t->created_at->translatedFormat('d M Y, H:i') }}
                            @if($t->user) &middot; {{ $t->user->name }} @endif
                        </span>
                    </div>
                    <p class="mt-1 whitespace-pre-line text-gray-800">{{ $t->catatan }}</p>
                    @if($t->foto_hasil_url)
                        <img src="{{ $t->foto_hasil_url }}" alt="Foto hasil penanganan"
                             class="mt-2 max-h-56 rounded-md object-cover">
                    @endif
                </li>
            @endforeach
        </ol>
    @endif
</div>

@if($laporan->hasLocation())
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    @endpush
    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            (function () {
                const titik = [{{ $laporan->lat }}, {{ $laporan->lng }}];
                const peta = L.map('peta-detail', { scrollWheelZoom: false }).setView(titik, 17);
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; kontributor <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(peta);
                L.marker(titik).addTo(peta);
            })();
        </script>
    @endpush
@endif
