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
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
        $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
        $table->date('meeting_date')->nullable();
        $table->time('meeting_time')->nullable();
        $table->string('meeting_location')->nullable();
        $table->enum('status', ['pending', 'accepted', 'completed', 'cancelled'])->default('pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
