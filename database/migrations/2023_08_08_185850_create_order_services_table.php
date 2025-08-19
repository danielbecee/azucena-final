<?php
// database/migrations/2023_08_08_185850_create_order_services_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('service_item_id'); // Sin restricción de clave foránea
            $table->foreignId('service_category_id')->nullable();
            $table->string('service_name');
            $table->decimal('price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->integer('quantity')->unsigned()->default(1);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_services');
    }
};
