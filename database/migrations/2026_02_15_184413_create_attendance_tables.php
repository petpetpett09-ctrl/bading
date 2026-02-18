<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Table for actual Daily Attendance Logs
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->string('status')->default('Present'); // e.g., On-Time, Late, Absent
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });

        // 2. Table for Shift Management (The Modal Data)
        Schema::create('employee_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('dept_code'); // HRM, SCM, FIN, MAN, INV, ORD, WAR, CRM, ECO
            $table->enum('shift_type', ['Morning', 'Afternoon', 'Graveyard']);
            $table->date('effective_date'); // The specific date set in your modal
            $table->string('schedule_range')->default('8AM - 5PM'); // Display string
            $table->timestamps();

            // Ensures an employee doesn't have two different shifts on the same day
            $table->unique(['user_id', 'effective_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_shifts');
        Schema::dropIfExists('attendance_logs');
    }
};
