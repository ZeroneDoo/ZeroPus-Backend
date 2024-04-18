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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->index();
            $table->string('username')->index();
            $table->string('email')->nullable();
            $table->text("alamat")->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('credit')->default(0);
            $table->string("profile")->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
