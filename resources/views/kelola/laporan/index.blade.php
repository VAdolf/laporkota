<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Laporan masuk</h2>
    </x-slot>

    <div class="mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-8">
        <form method="GET" action="{{ route('kelola.laporan.index') }}"
              class="mb-5 grid grid-cols-1 gap-3 rounded-lg bg-white p-4 shadow-sm sm:grid-cols-4">
            <input type="search" name="q" value="{{ $keyword }}" placeholder="Cari judul atau deskripsi"
                   class="rounded-md border-gray-300 text-sm shadow-sm sm:col-span-2" aria-label="Cari laporan">

            <select name="status" class="rounded-md border-gray-300 text-sm shadow-sm" aria-label="Filter status">
                <option value="">Semua status</option>
                @foreach(\App\Models\Laporan::STATUS as $s)
                    <option value="{{ $s }}" @selected($status === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>

            <select name="kategori_id" class="rounded-md border-gray-300 text-sm shadow-sm" aria-label="Filter kategori">
                <option value="">Semua kategori</option>
                @foreach($kategoris as $k)
                    <option value="{{ $k->id }}" @selected((string) request('kategori_id') === (string) $k->id)>{{ $k->nama_kategori }}</option>
                @endforeach
            </select>

            <div class="flex gap-2 sm:col-span-4">
                <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Terapkan</button>
                <a href="{{ route('kelola.laporan.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Atur ulang</a>
            </div>
        </form>

        @if($laporans->isEmpty())
            <div class="rounded-lg bg-white p-8 text-center text-gray-600 shadow-sm">
                Tidak ada laporan yang cocok dengan filter ini.
            </div>
        @else
            <ul class="divide-y divide-gray-100 overflow-hidden rounded-lg bg-white shadow-sm">
                @foreach($laporans as $laporan)
                    <li>
                        <a href="{{ route('kelola.laporan.show', $laporan) }}" class="flex gap-4 p-4 hover:bg-gray-50">
                            <img src="{{ $laporan->foto_url }}" alt="" class="h-16 w-16 shrink-0 rounded-md object-cover">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <span class="font-semibold text-gray-900">{{ $laporan->judul }}</span>
                                    <x-status-badge :status="$laporan->status" />
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $laporan->kategori->nama_kategori ?? 'Umum' }} &middot;
                                    {{ $laporan->user->name ?? '-' }} &middot;
                                    {{ $laporan->created_at->translatedFormat('d M Y') }}
                                </p>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="mt-4">{{ $laporans->links() }}</div>
        @endif
    </div>
</x-app-layout>
