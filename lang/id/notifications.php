<?php

return [
    'title' => 'Pusat Notifikasi',
    'subtitle' => 'Peringatan operasional real-time, peristiwa pelacakan, pembaruan status keterlambatan, dan pemberitahuan masalah.',
    'unread_count' => ':count Belum Dibaca',
    'all_caught_up' => 'Semua Sudah Dibaca',
    'mark_all_read' => 'Tandai Semua Sudah Dibaca',
    'mark_read' => 'Tandai dibaca',
    'read_status' => 'Dibaca',
    'open_shipment' => 'Buka Pengiriman',

    'empty' => [
        'title' => 'Tidak Ada Notifikasi',
        'description' => 'Anda tidak memiliki notifikasi atau peringatan operasional saat ini.',
    ],

    'messages' => [
        'shipment_delay_alert' => 'Peringatan keterlambatan pengiriman :number (:classification): :reason',
        'shipment_delay_internal' => 'Peringatan Operator: :number ditandai keterlambatan :classification (:delay_hours jam).',
        'shipment_exception_alert' => 'Pengecualian pengiriman :number tercatat [:type]: :description',
        'shipment_exception_internal' => 'Peringatan pengecualian pada :number [:type - Tingkat: :severity]. Tindakan operator diperlukan.',
        'shipment_exception_resolved' => 'Pengecualian pengiriman :number telah diselesaikan [:type].',
        'shipment_milestone_updated' => 'Status pengiriman :number diperbarui ke :status: :description',
        'active_exception' => 'Pengiriman :number memiliki masalah aktif: :type.',
        'legacy' => ':text',
    ],
];
