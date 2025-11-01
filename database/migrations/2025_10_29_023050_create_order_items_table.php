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
        Schema::create('order_items', function (Blueprint $t) {
        $t->id();
        $t->foreignId('order_id')->constrained()->cascadeOnDelete();
        $t->foreignId('product_id')->constrained()->cascadeOnDelete();
        $t->string('name');                               // สำรองชื่อ ณ เวลาซื้อ
        $t->decimal('price',10,2);                        // ราคา ณ เวลาซื้อ
        $t->unsignedInteger('qty');
        $t->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
