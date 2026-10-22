<?php

return [

    /*
    | Nama merek yang tampil di halaman User. Ubah lewat .env (FLEXITAG_BRAND)
    | bila ingin memakai "TapConnect" seperti pada desain Figma.
    */
    'brand' => env('FLEXITAG_BRAND', 'FlexiTag'),

    'tagline' => 'Satu Tap, Seribu Identitas',

    /*
    | Harga satuan untuk tampilan landing page saja. Harga pesanan yang
    | sebenarnya akan dihitung di server pada tahap pemesanan.
    */
    'unit_price' => 55000,

    'contact' => [
        'whatsapp' => env('FLEXITAG_WHATSAPP'),   // contoh: 6281234567890
        'instagram' => env('FLEXITAG_INSTAGRAM'), // contoh: flexitag.id
        'email' => env('FLEXITAG_EMAIL'),
        'location' => 'Makassar, Sulawesi Selatan',
    ],
];
