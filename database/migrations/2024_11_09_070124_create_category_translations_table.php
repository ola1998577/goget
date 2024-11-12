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
        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id'); // المفتاح الأجنبي لربط الفئة
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->string('title'); // عنوان الفئة بلغة محددة
            $table->string('language', 2); // رمز اللغة (مثل en أو ar)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_translations');
    }
};
