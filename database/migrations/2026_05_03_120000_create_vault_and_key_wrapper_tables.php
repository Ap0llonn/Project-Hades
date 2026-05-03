<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vault', function (Blueprint $table): void {
            $table->uuid();
            $table->foreignUuid('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('key_wrappers', function (Blueprint $table): void {
            $table->uuid();

            $table->foreignId('vault_id')
                ->constrained('vault')
                ->cascadeOnDelete();

            $table->string('type');

            $table->text('ciphertext');
            $table->string('nonce');

            $table->string('tag')->nullable();

            $table->string('prf_salt')->nullable();
            $table->json('prf_params')->nullable();

            $table->string('credential_id')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamp('revoked_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('key_wrappers');
        Schema::dropIfExists('vault');
    }
};
