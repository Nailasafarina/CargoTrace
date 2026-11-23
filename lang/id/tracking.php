<?php

return [
    'timeline_title' => 'Lini Masa Milestone',
    'timeline_subtitle' => 'Catatan kronologis keberangkatan pelabuhan, titik transit, dan telemetri kedatangan',
    'record_event' => 'Catat Milestone Peristiwa',

    'event_types' => [
        'order_created' => 'Pesanan Dibuat',
        'picked_up' => 'Muatan Dijemput',
        'departed' => 'Kapal Berangkat',
        'in_transit' => 'Dalam Pelayaran',
        'transshipment_arrival' => 'Tiba di Pelabuhan Transit',
        'customs_hold' => 'Tertahan Bea Cukai',
        'customs_cleared' => 'Lolos Bea Cukai',
        'arrived_destination' => 'Tiba di Pelabuhan Tujuan',
        'out_for_delivery' => 'Pengiriman Terakhir',
        'delivered' => 'Terkirim ke Penerima',
        'exception_alert' => 'Peringatan Masalah',
    ],

    'modal' => [
        'title' => 'Tambah Milestone Pelacakan',
        'event_type' => 'Tipe Peristiwa',
        'location' => 'Titik Lokasi',
        'occurred_at' => 'Waktu Kejadian (UTC)',
        'description' => 'Deskripsi Milestone',
        'latitude' => 'Lintang',
        'longitude' => 'Bujur',
        'is_exception' => 'Tandai sebagai Kejadian Khusus',
    ],

    'empty_timeline' => 'Belum ada peristiwa pelacakan tercatat. Catat milestone pertama di atas.',
];
