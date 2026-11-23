<?php

return [
    'leaflet' => [
        'origin' => 'Pelabuhan Asal',
        'destination' => 'Pelabuhan Tujuan',
        'current_position' => 'Lokasi Milestone Terkini',
        'status' => 'Status',
        'coordinates' => 'Koordinat',
        'carrier' => 'Operator',
        'eta' => 'ETA',
        'waypoint' => 'Titik Transit Koridor',
        'na' => 'T/A',
        'view_details' => 'Lihat Detail',
        'facility_type' => 'Jenis Fasilitas',
        'location' => 'Lokasi',
        'drag_marker_hint' => 'Geser pin untuk menyesuaikan koordinat GPS',
        'tile_error' => 'Petak peta tidak dapat dimuat.',
    ],

    'charts' => [
        'created' => 'Dibuat',
        'booked' => 'Dipesan',
        'picked_up' => 'Dijemput',
        'in_transit' => 'Dalam Perjalanan',
        'delivered' => 'Terkirim',
        'on_time' => 'Tepat Waktu',
        'low' => 'Terlambat Rendah',
        'medium' => 'Terlambat Sedang',
        'high' => 'Terlambat Tinggi',
        'shipments_count' => 'Jumlah Pengiriman',
    ],

    'geocoding' => [
        'loading' => 'Mencari koordinat alamat via OpenStreetMap...',
        'success' => 'Koordinat berhasil ditemukan dan diterapkan!',
        'failed' => 'Gagal menemukan koordinat. Silakan masukkan secara manual.',
        'min_chars' => 'Harap masukkan minimal 3 karakter untuk mencari koordinat.',
        'network_error' => 'Kesalahan jaringan saat mencari koordinat.',
    ],

    'confirm' => [
        'delete_shipment' => 'Apakah Anda yakin ingin menghapus pengiriman ini?',
        'delete_record' => 'Apakah Anda yakin ingin menghapus data ini?',
        'remove_item' => 'Hapus item muatan ini?',
    ],
];
