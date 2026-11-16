<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number', 50)->unique();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('carrier_id')->constrained('carriers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('origin_location_id')->constrained('locations')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('destination_location_id')->constrained('locations')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('cargo_type');
            $table->decimal('weight_kg', 12, 2);
            $table->decimal('volume_cbm', 10, 2);
            $table->dateTime('expected_departure_at');
            $table->dateTime('expected_arrival_at');
            $table->dateTime('estimated_arrival_at')->nullable();
            $table->string('current_status', 30)->default('CREATED')->index();
            $table->string('delay_status', 30)->default('ON_TIME')->index();
            $table->integer('delay_hours')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();

            // Composite indexes for fast query search & filtering
            $table->index(['customer_id', 'current_status']);
            $table->index(['current_status', 'delay_status']);
            $table->index(['carrier_id', 'current_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
