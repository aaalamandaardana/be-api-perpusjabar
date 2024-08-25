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
        Schema::create('borrows', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->dateTime('load_date');
            $table->dateTime('borrow_date');
            $table->uuid('book_id');
            $table->uuid('user_id');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->nullable();

            $table->foreign('book_id')->references('id')->on('books')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            
            $table->unique(['book_id', 'user_id']); // tidak menerima inputan yang sama dengan book_id & user_id (gabungan) sebelumnya
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrows');
    }
};
