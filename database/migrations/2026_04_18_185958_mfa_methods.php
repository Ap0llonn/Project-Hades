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
        Schema::create('mfa_methods', function (Blueprint $table) {
            $table->foreignUuid('user_id')->primary()->constrained()->cascadeOnDelete();
            $table->boolean('email')->default(true);
            $table->boolean('totp')->default(false);
            $table->json('recovery_codes')->nullable();
            $table->boolean('recovery_codes_show')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mfa_methods');
    }
};
