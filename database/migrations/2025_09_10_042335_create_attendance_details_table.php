<?php
// database/migrations/2025_09_10_042335_create_attendance_details_table.php
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
        Schema::create('attendance_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('attendance_id')->constrained('attendances')->cascadeOnDelete();

            // check_in atau check_out (maks 1 masing-masing per attendance)
            $table->enum('type', ['check_in', 'check_out']);

            // waktu kejadian sebenarnya (UTC); support offline sync
            $table->timestamp('occurred_at')->useCurrent();

            // Offline id dari device untuk dedup (UUID v4 dari mobile)
            $table->uuid('client_uuid')->nullable();
            $table->unique(['attendance_id', 'type']); // 1x jenis per hari
            $table->unique(['client_uuid']);           // cegah double submit

            // Lokasi
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('accuracy_m', 6, 2)->nullable();
            $table->string('address', 255)->nullable();

            // Validasi geofence
            $table->foreignId('geofence_id')->nullable()
                  ->constrained('geofences')->nullOnDelete();

            // Media & metadata device
            $table->string('photo_path', 2048)->nullable(); // simpan path disk
            $table->string('ip_address', 45)->nullable();
            $table->string('device_model', 100)->nullable();
            $table->string('device_os', 50)->nullable();

            // Sumber aksi
            $table->string('source', 20)->default('mobile');          // mobile/web/admin
            $table->string('location_source', 20)->nullable();        // gps/wifi/manual/beacon/qr
            $table->string('client_timezone', 64)->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['attendance_id', 'occurred_at']);
            $table->index(['geofence_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_details');
    }
};
