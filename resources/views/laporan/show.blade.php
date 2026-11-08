<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Detail laporan</h2>
            <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-blue-600 hover:underline">Kembali ke riwayat</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6 lg:px-8">
        @include('laporan._detail', ['laporan' => $laporan])
    </div>
</x-app-layout>
