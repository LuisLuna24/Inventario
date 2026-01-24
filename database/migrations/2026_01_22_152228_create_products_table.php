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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories');
            $table->string('name', 150)->unique();
            $table->text('description')->nullable();
            $table->string('sku', 50)->unique()->nullable();
            $table->string('barcode', 100)->unique()->nullable();
            $table->decimal('price', 12, 2)->default('0.00');
            $table->decimal('cost', 12, 2)->default('0.00');
            $table->integer('stock')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
