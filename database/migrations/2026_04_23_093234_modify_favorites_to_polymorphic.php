<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->dropForeign(['book_id']);
            $table->dropForeign(['note_id']);
            $table->dropColumn(['book_id', 'note_id']);
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->unsignedBigInteger('favoritable_id')->after('user_id');
            $table->string('favoritable_type')->after('favoritable_id');
        });
    }

    public function down(): void
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->dropColumn(['favoritable_id', 'favoritable_type']);
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->foreignId('book_id')->nullable()->constrained('books')->cascadeOnDelete();
            $table->foreignId('note_id')->nullable()->constrained('digital_notes')->cascadeOnDelete();
        });
    }
};
