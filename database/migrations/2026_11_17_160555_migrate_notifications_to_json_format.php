<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $notifications = DB::table('notifications')->get();
        foreach ($notifications as $notif) {
            $msg = $notif->message;
            json_decode($msg);
            if (json_last_error() !== JSON_ERROR_NONE) {
                if (preg_match('/Shipment\s+([A-Z0-9-]+)\s+has an active exception:\s*(.*)\./', $msg, $m)) {
                    $json = json_encode([
                        'key' => 'notifications.messages.active_exception',
                        'params' => [
                            'number' => $m[1],
                            'type' => trim($m[2]),
                        ],
                    ]);
                } else {
                    $json = json_encode([
                        'key' => 'notifications.messages.legacy',
                        'params' => ['text' => $msg],
                    ]);
                }
                DB::table('notifications')->where('id', $notif->id)->update(['message' => $json]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
