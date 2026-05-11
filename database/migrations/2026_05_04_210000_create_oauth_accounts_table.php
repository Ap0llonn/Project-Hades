<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oauth_accounts', function (Blueprint $table): void {
            $table->id();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider', 40);
            $table->string('provider_user_id', 191);
            $table->string('provider_email')->nullable();
            $table->string('provider_name')->nullable();
            $table->text('provider_avatar')->nullable();
            $table->text('token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('linked_at')->nullable();
            $table->timestamp('unlinked_at')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_user_id'], 'oauth_accounts_provider_uid_unique');
            $table->unique(['user_id', 'provider'], 'oauth_accounts_user_provider_unique');
            $table->index(['user_id', 'unlinked_at'], 'oauth_accounts_user_unlinked_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oauth_accounts');
    }
};

