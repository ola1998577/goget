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
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->unsignedBigInteger('store_id');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->string('image'); // صورة المنتج
            $table->float('price', 10, 3)->nullable(); // السعر الأساسي
            $table->unsignedFloat('discount', 8, 3)->nullable(); // الخصم
            $table->float('total_price', 10, 3)->nullable(); // السعر بعد الخصم
            $table->unsignedInteger('amount')->default(0); // الكمية المتوفرة
            $table->string('type')->nullable(); // نوع المنتج
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
