<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Buat laporan baru</h2>
    </x-slot>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    @endpush

    <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="rounded-lg bg-white p-5 shadow-sm sm:p-6">

            @if ($errors->any())
                <div class="mb-5 rounded-md border border-red-300 bg-red-50 p-3 text-sm text-red-800">
                    <p class="font-semibold">Laporan belum terkirim. Periksa isian berikut:</p>
                    <ul class="mt-1 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label for="kategori_id" class="mb-1 block text-sm font-medium text-gray-700">Kategori permasalahan</label>
                    <select id="kategori_id" name="kategori_id" required class="w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Pilih kategori</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" @selected(old('kategori_id') == $kategori->id)>
                                {{ $kategori->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="judul" class="mb-1 block text-sm font-medium text-gray-700">Judul laporan</label>
                    <input id="judul" type="text" name="judul" value="{{ old('judul') }}" required maxlength="255"
                           class="w-full rounded-md border-gray-300 shadow-sm"
                           placeholder="Contoh: Jalan berlubang di depan pasar">
                </div>

                <div>
                    <label for="deskripsi" class="mb-1 block text-sm font-medium text-gray-700">Deskripsi dan patokan lokasi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" required
                              class="w-full rounded-md border-gray-300 shadow-sm"
                              placeholder="Jelaskan kondisi kerusakan dan patokan lokasi, misalnya depan Toko Makmur Jaya, Jl. A. Yani No. 12.">{{ old('deskripsi') }}</textarea>
                </div>

                <div>
                    <span class="mb-1 block text-sm font-medium text-gray-700">Lokasi kejadian</span>
                    <p class="mb-2 text-sm text-gray-500">Ketuk peta atau geser penanda ke titik kejadian. Anda juga bisa memakai lokasi perangkat.</p>

                    <button type="button" id="btn-lokasi"
                            class="mb-2 inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Gunakan lokasi saya
                    </button>

                    <div id="peta" class="h-72 w-full rounded-md border border-gray-300 sm:h-80" role="application" aria-label="Peta pemilihan lokasi"></div>
                    <p id="info-lokasi" class="mt-2 text-sm text-gray-600" aria-live="polite">Lokasi belum dipilih.</p>

                    <input type="hidden" name="lat" id="lat" value="{{ old('lat') }}">
                    <input type="hidden" name="lng" id="lng" value="{{ old('lng') }}">
                </div>

                <div>
                    <label for="foto_bukti" class="mb-1 block text-sm font-medium text-gray-700">Foto bukti (JPG atau PNG, maksimal 2 MB)</label>
                    <input id="foto_bukti" type="file" name="foto_bukti" accept="image/jpeg,image/png" required
                           class="w-full rounded-md border border-gray-300 text-sm shadow-sm">
                    <img id="preview-foto" alt="Pratinjau foto bukti" class="mt-3 hidden max-h-56 rounded-md object-cover">
                </div>

                <button type="submit"
                        class="w-full rounded-md bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Kirim laporan
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            (function () {
                const pusat = [{{ config('laporkota.map.lat') }}, {{ config('laporkota.map.lng') }}];
                const zoomAwal = {{ config('laporkota.map.zoom') }};

                const inputLat = document.getElementById('lat');
                const inputLng = document.getElementById('lng');
                const info = document.getElementById('info-lokasi');
                const tombol = document.getElementById('btn-lokasi');

                const lat0 = parseFloat(inputLat.value);
                const lng0 = parseFloat(inputLng.value);
                const punyaNilaiLama = !isNaN(lat0) && !isNaN(lng0) && !(lat0 === 0 && lng0 === 0);

                const peta = L.map('peta').setView(punyaNilaiLama ? [lat0, lng0] : pusat, punyaNilaiLama ? 17 : zoomAwal);

                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; kontributor <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(peta);

                let penanda = null;

                function setTitik(lat, lng, geser) {
                    inputLat.value = lat.toFixed(7);
                    inputLng.value = lng.toFixed(7);
                    info.textContent = 'Lokasi dipilih: ' + lat.toFixed(6) + ', ' + lng.toFixed(6);

                    if (penanda) {
                        penanda.setLatLng([lat, lng]);
                    } else {
                        penanda = L.marker([lat, lng], { draggable: true }).addTo(peta);
                        penanda.on('dragend', function () {
                            const p = penanda.getLatLng();
                            setTitik(p.lat, p.lng, false);
                        });
                    }
                    if (geser) { peta.setView([lat, lng], Math.max(peta.getZoom(), 17)); }
                }

                if (punyaNilaiLama) { setTitik(lat0, lng0, false); }

                peta.on('click', function (e) { setTitik(e.latlng.lat, e.latlng.lng, false); });

                tombol.addEventListener('click', function () {
                    if (!navigator.geolocation) {
                        info.textContent = 'Peramban ini tidak mendukung lokasi. Pilih titik secara manual pada peta.';
                        return;
                    }
                    info.textContent = 'Mencari lokasi perangkat...';
                    navigator.geolocation.getCurrentPosition(
                        function (pos) { setTitik(pos.coords.latitude, pos.coords.longitude, true); },
                        function () {
                            info.textContent = 'Lokasi perangkat tidak dapat diakses. Izinkan akses lokasi atau pilih titik secara manual pada peta.';
                        },
                        { enableHighAccuracy: true, timeout: 10000 }
                    );
                });

                // Pratinjau foto
                const foto = document.getElementById('foto_bukti');
                const preview = document.getElementById('preview-foto');
                foto.addEventListener('change', function () {
                    const f = foto.files && foto.files[0];
                    if (!f) { preview.classList.add('hidden'); return; }
                    preview.src = URL.createObjectURL(f);
                    preview.classList.remove('hidden');
                });
            })();
        </script>
    @endpush
</x-app-layout>
