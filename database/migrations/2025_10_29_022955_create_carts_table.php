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
    if (Schema::hasTable('carts')) {
        return;
    }

    Schema::create('carts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
        $table->unsignedInteger('qty');
        $table->decimal('price', 10, 2);
        $table->boolean('selected')->default(true);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('carts');
}


};
