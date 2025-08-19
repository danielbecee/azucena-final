<?php
// database/migrations/2025_08_08_000013_create_assignments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('order_item_id')->nullable()->constrained('order_items');
            $table->dateTime('assigned_at')->useCurrent();
            $table->dateTime('completed_at')->nullable();
            $table->foreignId('status_id')->constrained('states');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
