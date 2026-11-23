<?php

return [
    'title' => 'Pengecualian & Masalah Pengiriman',
    'subtitle' => 'Kendala operasional aktif, penahanan pabean, dan alur kerja penyelesaian',
    'log_exception' => 'Catat Masalah',

    'types' => [
        'customs_hold' => 'Penahanan Bea Cukai',
        'weather_delay' => 'Keterlambatan Cuaca Buruk',
        'port_congestion' => 'Kepadatan Dermaga Pelabuhan',
        'damaged' => 'Kerusakan Muatan',
        'misrouted' => 'Kesalahan Rute Transit',
        'documentation_issue' => 'Masalah Dokumen',
    ],

    'status' => [
        'open' => 'Terbuka',
        'investigating' => 'Dalam Penyelidikan',
        'resolved' => 'Selesai & Selesai',
    ],

    'severity' => [
        'low' => 'Rendah',
        'medium' => 'Sedang',
        'high' => 'Tinggi',
        'critical' => 'Kritis',
    ],

    'form' => [
        'type' => 'Tipe Pengecualian',
        'severity' => 'Tingkat Keparahan',
        'detected_at' => 'Terdeteksi Pada (UTC)',
        'description' => 'Deskripsi & Akar Masalah',
        'resolution_notes' => 'Catatan Penyelesaian',
        'update_status' => 'Perbarui Status',
        'resolve_button' => 'Selesaikan Masalah',
    ],

    'no_exceptions' => 'Tidak ada pengecualian aktif atau historis yang tercatat untuk pengiriman ini.',
];
