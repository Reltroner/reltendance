<?php
// database/migrations/2025_09_10_042234_create_attendances_table.php
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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('attend_date'); // tanggal kerja (UTC basis)
            $table->foreignId('shift_id')->nullable()->constrained()->nullOnDelete();

            // Ringkasan harian
            $table->timestamp('first_check_in_at')->nullable();  // UTC
            $table->timestamp('last_check_out_at')->nullable();  // UTC
            $table->unsignedInteger('work_minutes')->default(0);
            $table->unsignedInteger('overtime_minutes')->default(0);
            $table->unsignedInteger('early_leave_minutes')->default(0);
            $table->boolean('is_late')->default(false);

            // present/absent/leave/remote/holiday
            $table->string('status', 20)->default('present');
            $table->string('timezone', 64)->default('UTC'); // prefer client tz

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'attend_date']);
            $table->index(['attend_date']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
