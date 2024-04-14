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
            $table->string("title")->index();
            $table->text("description")->nullable();
            $table->string("photo")->nullable();
            $table->string("source")->nullable();
            $table->string("penulis")->index()->nullable();
            $table->string("penerbit")->index()->nullable();
            $table->integer("amount")->default(0);
            $table->boolean("is_rent")->default(false);
            $table->timestamps();
            $table->softDeletes();
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
