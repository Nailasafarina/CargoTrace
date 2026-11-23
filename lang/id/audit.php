<?php

return [
    'title' => 'Jejak Audit Sistem & Kepatuhan',
    'subtitle' => 'Catatan yang tidak dapat diubah dari semua tindakan administratif, pengiriman, dan operasional yang dilakukan di platform CargoTrace.',
    'total_logged' => 'Total Kejadian Tercatat: :total',
    'filters_heading' => 'Filter Jejak Audit',
    'actor' => 'Aktor (Pengguna)',
    'all_actors' => 'Semua Aktor',
    'action' => 'Aksi',
    'all_actions' => 'Semua Aksi',
    'entity_type' => 'Tipe Entitas',
    'all_entities' => 'Semua Entitas',
    'date_from' => 'Dari Tanggal',
    'date_to' => 'Sampai Tanggal',

    'table' => [
        'timestamp' => 'Waktu (UTC)',
        'actor' => 'Aktor',
        'action' => 'Aksi',
        'target_entity' => 'Entitas Sasaran',
        'ip_address' => 'Alamat IP',
        'diff' => 'Perubahan Data / Diff',
    ],

    'diff_modal' => [
        'inspect_button' => 'Periksa Diff &darr;',
        'snapshot_title' => 'Snapshot Audit: :entity #:id',
        'snapshot_subtitle' => 'Aksi: :action pada :time UTC',
        'previous_state' => 'Kondisi Sebelumnya',
        'new_state' => 'Kondisi Baru',
        'no_state_change' => 'Tidak ada perubahan data',
    ],

    'system_actor' => 'Sistem / Tugas Latar Belakang',
    'empty' => 'Tidak ada jejak audit ditemukan yang cocok dengan kriteria pencarian.',
];
