<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Riwayat laporan saya</h2>
            <a href="{{ route('laporan.create') }}"
               class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Buat laporan baru
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 rounded-md border border-green-300 bg-green-50 p-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-md border border-red-300 bg-red-50 p-3 text-sm text-red-800">
                {{ $errors->first() }}
            </div>
        @endif

        @if($laporans->isEmpty())
            <div class="rounded-lg bg-white p-8 text-center shadow-sm">
                <p class="text-gray-600">Anda belum mengirim laporan.</p>
                <a href="{{ route('laporan.create') }}" class="mt-3 inline-block text-sm font-semibold text-blue-600 hover:underline">
                    Laporkan masalah pertama Anda
                </a>
            </div>
        @else
            <ul class="space-y-3">
                @foreach($laporans as $laporan)
                    <li class="flex gap-4 rounded-lg bg-white p-4 shadow-sm">
                        <img src="{{ $laporan->foto_url }}" alt="Foto laporan {{ $laporan->judul }}"
                             class="h-20 w-20 shrink-0 rounded-md object-cover sm:h-24 sm:w-24">

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <a href="{{ route('laporan.show', $laporan->id) }}"
                                   class="font-semibold text-gray-900 hover:text-blue-700">
                                    {{ $laporan->judul }}
                                </a>
                                <x-status-badge :status="$laporan->status" />
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ $laporan->kategori->nama_kategori ?? 'Umum' }} &middot;
                                {{ $laporan->created_at->translatedFormat('d M Y') }}
                            </p>

                            <div class="mt-3 flex items-center gap-4 text-sm">
                                <a href="{{ route('laporan.show', $laporan->id) }}" class="font-semibold text-blue-600 hover:underline">
                                    Lihat detail
                                </a>
                                @if($laporan->status === 'menunggu')
                                    <form action="{{ route('laporan.destroy', $laporan->id) }}" method="POST"
                                          onsubmit="return confirm('Hapus laporan ini? Tindakan ini tidak dapat dibatalkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-semibold text-red-600 hover:underline">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-app-layout>
