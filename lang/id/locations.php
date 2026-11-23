<?php

return [
    'title' => 'Direktori Pusat Logistik Global',
    'subtitle' => 'Pelabuhan laut, depo peti kemas pedalaman, bandara, dan terminal alih kapal',
    'add_location' => 'Daftarkan Lokasi Baru',
    'edit_location' => 'Ubah Lokasi',
    'details_title' => 'Profil Lokasi',
    'departing_shipments' => 'Pengiriman Berangkat',
    'arriving_shipments' => 'Pengiriman Tiba',
    'milestone_events' => 'Peristiwa Pelacakan Dicatat',
    'coords_title' => 'Koordinat Geografis (Format WGS84)',
    'view_osm' => 'Lihat di OpenStreetMap',
    'search_placeholder' => 'Cari nama, kota, negara...',
    'all_types' => 'Semua Jenis Fasilitas',
    'delete_confirm' => 'Apakah Anda yakin ingin menghapus :name?',

    'map' => [
        'title' => 'Peta Lokasi',
        'subtitle' => 'Pemosisian geospasial interaktif OpenStreetMap',
        'no_coordinates' => 'Koordinat Belum Tersedia',
        'no_coordinates_desc' => 'Lokasi ini belum memiliki koordinat GPS yang valid.',
        'set_coordinates' => 'Atur Koordinat',
        'summary_title' => 'Peta Jaringan Logistik Global',
        'summary_subtitle' => 'Ikhtisar spasial interaktif pusat logistik, pelabuhan, dan terminal',
        'toggle_summary_map' => 'Tampilkan/Sembunyikan Peta Jaringan',
        'picker_title' => 'Pilih Titik Lokasi di Peta',
        'picker_hint' => 'Klik pada peta atau geser pin marker untuk menentukan koordinat garis lintang & bujur secara otomatis.',
    ],

    'fields' => [
        'name' => 'Nama Lokasi',
        'code' => 'Kode Port / UN/LOCODE',
        'type' => 'Jenis Fasilitas',
        'city' => 'Kota Pusat',
        'country' => 'Negara',
        'country_code' => 'Kode Negara (ISO 2)',
        'latitude' => 'Garis Lintang',
        'longitude' => 'Garis Bujur',
        'address' => 'Alamat / Lokasi Fisik',
    ],

    'table' => [
        'hub_name' => 'Nama Hub / Fasilitas',
        'type' => 'Jenis',
        'city_country' => 'Negara & Kota',
        'coordinates' => 'Koordinat GPS (WGS84)',
        'shipments_ratio' => 'Pengiriman (Keluar / Masuk)',
        'actions' => 'Aksi',
    ],

    'types' => [
        'port' => 'Pelabuhan',
        'airport' => 'Bandara',
        'warehouse' => 'Gudang',
        'city' => 'Pusat Kota',
    ],

    'geocoding' => [
        'toolbar_title' => 'Bilah Alat Geocoding OpenStreetMap',
        'toolbar_desc' => 'Cari dan isi koordinat geografis secara otomatis dari alamat',
        'lookup_button' => 'Geocode Alamat',
        'idle' => 'Siap untuk geocode',
    ],

    'empty' => 'Belum ada lokasi terdaftar.',
];
