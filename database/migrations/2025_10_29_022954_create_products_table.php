<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();                     // BIGINT unsigned
            $table->string('name');
            $table->decimal('price', 10, 2)->default(0);
            // ใส่ฟิลด์เพิ่มได้ตามต้องการ เช่น stock, sku ฯลฯ
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

