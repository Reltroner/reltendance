<?php
// database/migrations/2025_09_10_045450_create_shifts_table.php
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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->time('start_at');           // jam mulai kerja (server UTC)
            $table->time('end_at');             // jam selesai kerja (server UTC)
            $table->unsignedSmallInteger('grace_minutes')->default(15);
            $table->unsignedSmallInteger('work_minutes_target')->default(480);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
