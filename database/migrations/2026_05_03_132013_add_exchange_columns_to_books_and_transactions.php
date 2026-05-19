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
        Schema::table('books', function (Blueprint $table) {
            $table->string('exchange_for')->nullable()->after('offer_type');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('offered_book_id')->nullable()->after('book_id')->constrained('books')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['offered_book_id']);
            $table->dropColumn('offered_book_id');
        });

        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('exchange_for');
        });
    }
};
