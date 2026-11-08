@props(['status'])

@php
    $map = [
        'menunggu' => ['bg-amber-100 text-amber-800', 'Menunggu'],
        'diproses' => ['bg-blue-100 text-blue-800', 'Diproses'],
        'selesai'  => ['bg-green-100 text-green-800', 'Selesai'],
        'ditolak'  => ['bg-red-100 text-red-800', 'Ditolak'],
    ];
    [$warna, $label] = $map[$status] ?? ['bg-gray-100 text-gray-800', ucfirst((string) $status)];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold $warna"]) }}>
    {{ $label }}
</span>
