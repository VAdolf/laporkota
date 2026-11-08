<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Peta
    |--------------------------------------------------------------------------
    | Titik tengah awal peta pada form laporan dan dashboard monitoring.
    | Ubah lewat .env: MAP_DEFAULT_LAT, MAP_DEFAULT_LNG, MAP_DEFAULT_ZOOM.
    | Nilai bawaan: pusat Kota Surabaya (placeholder untuk "Kota X").
    */
    'map' => [
        'lat' => (float) env('MAP_DEFAULT_LAT', -7.2575),
        'lng' => (float) env('MAP_DEFAULT_LNG', 112.7521),
        'zoom' => (int) env('MAP_DEFAULT_ZOOM', 13),
    ],

];
