<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Detail laporan</h2>
            <a href="{{ route('kelola.laporan.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">Kembali ke daftar</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 rounded-md border border-green-300 bg-green-50 p-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @include('laporan._detail', ['laporan' => $laporan, 'tampilkanPelapor' => true])

        @if(auth()->user()->hasRole('admin'))
            <div class="mt-6 rounded-lg bg-white p-5 shadow-sm sm:p-6">
                <h3 class="text-base font-semibold text-gray-900">Beri tanggapan dan ubah status</h3>

                @if ($errors->any())
                    <div class="mt-3 rounded-md border border-red-300 bg-red-50 p-3 text-sm text-red-800">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('kelola.laporan.tanggapi', $laporan) }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf

                    <div>
                        <label for="status" class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                        <select id="status" name="status" required class="w-full rounded-md border-gray-300 shadow-sm">
                            @foreach(\App\Models\Laporan::STATUS as $s)
                                <option value="{{ $s }}" @selected(old('status', $laporan->status) === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="catatan" class="mb-1 block text-sm font-medium text-gray-700">Catatan untuk pelapor</label>
                        <textarea id="catatan" name="catatan" rows="4" required class="w-full rounded-md border-gray-300 shadow-sm"
                                  placeholder="Contoh: Tim sudah survei lokasi, perbaikan dijadwalkan Kamis.">{{ old('catatan') }}</textarea>
                    </div>

                    <div>
                        <label for="foto_hasil" class="mb-1 block text-sm font-medium text-gray-700">Foto hasil penanganan (opsional, maksimal 2 MB)</label>
                        <input id="foto_hasil" type="file" name="foto_hasil" accept="image/jpeg,image/png"
                               class="w-full rounded-md border border-gray-300 text-sm shadow-sm">
                        <p id="error-foto-hasil" class="mt-1 hidden text-sm text-red-600" role="alert"></p>
                        <img id="preview-foto-hasil" alt="Pratinjau foto hasil penanganan"
                             class="mt-3 hidden max-h-56 rounded-md object-cover">
                    </div>

                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Simpan tanggapan
                    </button>
                </form>
            </div>
        @endif
    </div>

@push('scripts')
    <script>
        (function () {
            const input = document.getElementById('foto_hasil');
            if (!input) { return; }

            const preview = document.getElementById('preview-foto-hasil');
            const pesan = document.getElementById('error-foto-hasil');
            const batas = 2 * 1024 * 1024; // 2 MB, sama dengan aturan di server

            input.addEventListener('change', function () {
                const f = input.files && input.files[0];

                pesan.classList.add('hidden');
                if (preview.src) { URL.revokeObjectURL(preview.src); }

                if (!f) {
                    preview.classList.add('hidden');
                    preview.removeAttribute('src');
                    return;
                }

                if (f.size > batas) {
                    input.value = '';
                    preview.classList.add('hidden');
                    preview.removeAttribute('src');
                    pesan.textContent = 'Ukuran foto maksimal 2 MB. Pilih foto yang lebih kecil.';
                    pesan.classList.remove('hidden');
                    return;
                }

                preview.src = URL.createObjectURL(f);
                preview.classList.remove('hidden');
            });
        })();
    </script>
@endpush
</x-app-layout>