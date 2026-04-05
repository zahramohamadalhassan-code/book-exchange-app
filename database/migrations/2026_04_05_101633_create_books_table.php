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
    Schema::create('books', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
        $table->string('title');
        $table->string('author')->nullable();
        $table->string('cover_image_url')->nullable();
        $table->enum('condition', ['excellent', 'good', 'fair', 'poor']);
        $table->enum('offer_type', ['sale', 'exchange', 'donate']);
        $table->decimal('price', 8, 2)->nullable(); // يسمح بقيمة فارغة في حال التبرع
        $table->enum('status', ['available', 'pending', 'sold'])->default('available');
        $table->enum('moderation_status', ['pending', 'approved', 'rejected'])->default('pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
