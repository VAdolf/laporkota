<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'LaporKota') }} - Lapor masalah kota, pantau sampai selesai</title>
        <meta name="description" content="Laporkan jalan rusak, sampah, lampu jalan mati, dan saluran tersumbat langsung ke dinas terkait, lalu pantau penanganannya.">

        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/favicon-192.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
        <meta name="theme-color" content="#2563eb">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 font-sans text-slate-800 antialiased">

        {{-- Header --}}
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <a href="{{ url('/') }}" aria-label="Beranda LaporKota">
                    <img src="{{ asset('images/logo-horizontal.png') }}" alt="LaporKota" class="h-9 w-auto">
                </a>

                <nav class="flex items-center gap-2 text-sm font-semibold" aria-label="Akun">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Buka dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="rounded-md px-4 py-2 text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Masuk
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                               class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Daftar
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        <main>
            {{-- Hero --}}
            <section class="mx-auto grid max-w-6xl items-center gap-10 px-4 py-12 sm:px-6 sm:py-16 lg:grid-cols-2 lg:gap-14 lg:px-8 lg:py-20">
                <div>
                    <h1 class="text-4xl font-extrabold leading-tight tracking-tight text-[#182e53] sm:text-5xl">
                        Ada jalan berlubang di depan rumah? Laporkan dari ponsel Anda.
                    </h1>
                    <p class="mt-5 max-w-lg text-lg leading-relaxed text-slate-600">
                        Foto masalahnya, tandai lokasinya di peta, lalu pantau penanganannya
                        sampai petugas melaporkan hasilnya kepada Anda.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                               class="rounded-md bg-blue-600 px-6 py-3 text-base font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Buka dashboard
                            </a>
                        @else
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                   class="rounded-md bg-blue-600 px-6 py-3 text-base font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Daftar dan buat laporan
                                </a>
                            @endif
                            <a href="{{ route('login') }}"
                               class="rounded-md border border-slate-300 bg-white px-6 py-3 text-base font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Saya sudah punya akun
                            </a>
                        @endauth
                    </div>

                    <p class="mt-4 text-sm text-slate-500">Gratis untuk seluruh warga. Cukup email dan kata sandi.</p>
                </div>

                {{-- Peta ilustrasi: pin memakai warna status yang sama dengan aplikasi --}}
                <div class="relative mx-auto w-full max-w-lg">
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <svg viewBox="0 0 480 380" class="block w-full" role="img"
                             aria-label="Ilustrasi peta kota dengan tiga titik laporan berstatus menunggu, diproses, dan selesai">
                            <rect width="480" height="380" fill="#e9eff7"/>
                            {{-- taman dan sungai --}}
                            <rect x="30" y="225" width="120" height="105" rx="10" fill="#cfe8d5"/>
                            <path d="M0 95 C90 70 150 140 240 118 S400 70 480 100 L480 132 C400 108 330 160 240 150 S90 104 0 126 Z" fill="#bcd6ee"/>
                            {{-- jalan --}}
                            <g fill="none" stroke="#ffffff" stroke-linecap="round">
                                <path d="M-10 200 L490 215" stroke-width="16"/>
                                <path d="M190 -10 L215 390" stroke-width="16"/>
                                <path d="M340 -10 L318 390" stroke-width="12"/>
                                <path d="M-10 310 L490 292" stroke-width="10"/>
                                <path d="M40 20 L170 200" stroke-width="8"/>
                                <path d="M400 215 L470 360" stroke-width="8"/>
                            </g>
                            {{-- blok bangunan --}}
                            <g fill="#dbe4f0">
                                <rect x="232" y="228" width="70" height="46" rx="4"/>
                                <rect x="355" y="228" width="80" height="46" rx="4"/>
                                <rect x="232" y="310" width="70" height="46" rx="4"/>
                                <rect x="360" y="312" width="70" height="46" rx="4"/>
                                <rect x="40" y="340" width="110" height="30" rx="4"/>
                            </g>

                            {{-- pin: menunggu, diproses, selesai --}}
                            <g stroke="#ffffff" stroke-width="3">
                                <circle cx="105" cy="178" r="11" fill="#d97706"/>
                                <circle cx="262" cy="196" r="11" fill="#2563eb"/>
                                <circle cx="388" cy="262" r="11" fill="#16a34a"/>
                            </g>
                            <circle cx="262" cy="196" r="22" fill="none" stroke="#2563eb" stroke-opacity=".35" stroke-width="2"/>
                        </svg>

                        {{-- legenda --}}
                        <ul class="flex flex-wrap gap-x-5 gap-y-1 border-t border-slate-200 px-4 py-3 text-sm text-slate-600">
                            <li class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full" style="background:#d97706"></span>Menunggu</li>
                            <li class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full" style="background:#2563eb"></span>Diproses</li>
                            <li class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full" style="background:#16a34a"></span>Selesai</li>
                        </ul>
                    </div>

                    {{-- kartu laporan mengambang --}}
                    <div class="mt-4 rounded-xl border border-slate-200 bg-white p-4 shadow-lg sm:absolute sm:right-[-1rem] sm:top-[-1.25rem] sm:mt-0 sm:w-72">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-900">Jalan berlubang di depan pasar</p>
                            <x-status-badge status="diproses" />
                        </div>
                        <p class="mt-1 text-sm text-slate-500">Tim sudah survei lokasi, perbaikan dijadwalkan Kamis.</p>
                    </div>
                </div>
            </section>

            {{-- Kategori --}}
            <section class="border-y border-slate-200 bg-white">
                <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
                    <h2 class="text-2xl font-bold tracking-tight text-[#182e53]">Masalah apa yang bisa dilaporkan?</h2>

                    <ul class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <li class="flex items-start gap-3 rounded-lg border border-slate-200 p-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-700" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" viewBox="0 0 24 24"><path d="M8.5 3 5 21M15.5 3 19 21M12 4v3M12 10.5v3M12 17v3"/></svg>
                            </span>
                            <div>
                                <p class="font-semibold text-slate-900">Jalan rusak dan berlubang</p>
                                <p class="mt-0.5 text-sm text-slate-500">Lubang, retak, dan aspal terkelupas.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3 rounded-lg border border-slate-200 p-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-700" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                            </span>
                            <div>
                                <p class="font-semibold text-slate-900">Sampah dan lingkungan</p>
                                <p class="mt-0.5 text-sm text-slate-500">Tumpukan sampah liar dan lingkungan kotor.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3 rounded-lg border border-slate-200 p-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-700" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18"/></svg>
                            </span>
                            <div>
                                <p class="font-semibold text-slate-900">Penerangan jalan umum</p>
                                <p class="mt-0.5 text-sm text-slate-500">Lampu jalan mati atau berkedip.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3 rounded-lg border border-slate-200 p-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-700" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 3s6 6.5 6 11a6 6 0 11-12 0c0-4.5 6-11 6-11z"/></svg>
                            </span>
                            <div>
                                <p class="font-semibold text-slate-900">Saluran air dan drainase</p>
                                <p class="mt-0.5 text-sm text-slate-500">Got tersumbat dan genangan air.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>

            {{-- Cara kerja (urutan langkah) --}}
            <section class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-bold tracking-tight text-[#182e53]">Tiga langkah dari laporan sampai selesai</h2>

                <ol class="mt-8 grid gap-6 md:grid-cols-3">
                    <li class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">1</span>
                        <h3 class="mt-4 text-lg font-semibold text-slate-900">Foto dan tandai lokasi</h3>
                        <p class="mt-2 leading-relaxed text-slate-600">
                            Pilih kategori, tulis keterangan singkat, unggah foto, lalu ketuk titik kejadian di peta
                            atau pakai lokasi ponsel Anda.
                        </p>
                    </li>
                    <li class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">2</span>
                        <h3 class="mt-4 text-lg font-semibold text-slate-900">Dinas menindaklanjuti</h3>
                        <p class="mt-2 leading-relaxed text-slate-600">
                            Petugas membaca laporan Anda, mengubah statusnya, dan menulis catatan
                            tentang apa yang sedang dikerjakan.
                        </p>
                    </li>
                    <li class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">3</span>
                        <h3 class="mt-4 text-lg font-semibold text-slate-900">Pantau sampai selesai</h3>
                        <p class="mt-2 leading-relaxed text-slate-600">
                            Lihat riwayat penanganan di halaman laporan Anda, lengkap dengan foto hasil
                            perbaikan dari petugas.
                        </p>
                    </li>
                </ol>
            </section>

            {{-- Untuk dinas dan pimpinan --}}
            <section class="bg-[#182e53]">
                <div class="mx-auto grid max-w-6xl items-center gap-8 px-4 py-12 sm:px-6 md:grid-cols-[1fr_auto] lg:px-8">
                    <div>
                        <img src="{{ asset('images/logo-putih.png') }}" alt="LaporKota" class="h-10 w-auto">
                        <h2 class="mt-5 text-2xl font-bold tracking-tight text-white">Untuk dinas dan pimpinan</h2>
                        <p class="mt-2 max-w-2xl leading-relaxed text-blue-100">
                            Dashboard monitoring menampilkan sebaran laporan di peta, jumlah laporan per status
                            dan kategori, tren enam bulan terakhir, serta rata-rata waktu penyelesaian.
                        </p>
                    </div>
                    @guest
                        <a href="{{ route('login') }}"
                           class="inline-flex justify-center rounded-md bg-white px-6 py-3 text-base font-semibold text-[#182e53] hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[#182e53]">
                            Masuk sebagai petugas
                        </a>
                    @endguest
                </div>
            </section>
        </main>

        <footer class="border-t border-slate-200 bg-white">
            <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-6 text-sm text-slate-500 sm:px-6 lg:px-8">
                <img src="{{ asset('images/logo-horizontal.png') }}" alt="LaporKota" class="h-7 w-auto">
                <p>&copy; {{ date('Y') }} LaporKota. Dibuat sebagai proyek capstone.</p>
            </div>
        </footer>
    </body>
</html>
