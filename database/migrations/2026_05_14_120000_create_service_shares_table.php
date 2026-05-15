<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_shares', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignUuid('owner_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('recipient_user_id')->constrained('users')->cascadeOnDelete();
            $table->json('key_envelope');
            $table->timestamps();

            $table->unique(['service_id', 'recipient_user_id']);
            $table->index('owner_user_id');
            $table->index('recipient_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_shares');
    }
};
