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
        Schema::create('product_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id'); // المفتاح الأجنبي لربط المنتج
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->string('title'); // عنوان المنتج بلغة محددة
            $table->text('description')->nullable(); // وصف المنتج بلغة محددة
            $table->string('language', 2); // رمز اللغة (مثل en أو ar)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_translations');
    }
};
