<?php

return [
    'title' => 'Konfigurasi & Parameter Sistem',
    'subtitle' => 'Kelola ambang batas operasional global dan kriteria keterlambatan ETA dinamis',
    'updated_status' => 'Pengaturan ambang batas keterlambatan berhasil diperbarui.',
    'card_title' => 'Pengaturan Ambang Batas Keterlambatan',
    'card_subtitle' => 'Konfigurasikan toleransi jam sebelum keterlambatan ETA memicu peringatan',
    'threshold_label' => 'Ambang Batas Peringatan Keterlambatan (Jam) *',
    'hours_suffix' => 'Jam',
    'group_presets' => 'Preset Standar:',
    'group_standard' => '24j (Standar Grup)',
    're_evaluate_now' => 'Evaluasi ulang semua pengiriman aktif segera',
    're_evaluate_desc' => 'Menerapkan ambang batas baru di semua koridor pengiriman aktif setelah disimpan.',
    'save_button' => 'Simpan & Terapkan Konfigurasi',
    'scale_title' => 'Skala Tingkat Keterlambatan',
    'scale_intro' => 'Setelah keterlambatan melampaui ambang batas aktif, sistem secara otomatis mengelompokkannya sesuai standar SLA:',
    'low_desc' => 'Variasi jadwal kecil; penyangga waktu di pelabuhan.',
    'medium_desc' => 'Memerlukan pemantauan transit dan pemberitahuan pelanggan.',
    'high_desc' => 'Gangguan besar; eskalasi operasional segera.',
    'automation_title' => 'Otomasi Terjadwal',
    'automation_desc' => 'Perintah Artisan shipments:evaluate-delays didaftarkan berjalan setiap jam untuk mengaudit transit kapal aktif.',
];
