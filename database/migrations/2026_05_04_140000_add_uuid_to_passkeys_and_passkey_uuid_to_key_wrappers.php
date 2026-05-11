<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('passkeys') && !Schema::hasColumn('passkeys', 'uuid')) {
            Schema::table('passkeys', function (Blueprint $table): void {
                $table->uuid('uuid')->nullable()->after('id');
                $table->index('uuid');
            });
        }

        if (Schema::hasTable('passkeys') && Schema::hasColumn('passkeys', 'uuid')) {
            DB::table('passkeys')
                ->select('id')
                ->whereNull('uuid')
                ->orderBy('id')
                ->chunkById(100, function ($rows): void {
                    foreach ($rows as $row) {
                        DB::table('passkeys')
                            ->where('id', $row->id)
                            ->update(['uuid' => (string) Str::uuid()]);
                    }
                });
        }

        if (Schema::hasTable('key_wrappers') && !Schema::hasColumn('key_wrappers', 'passkey_uuid')) {
            Schema::table('key_wrappers', function (Blueprint $table): void {
                $table->uuid('passkey_uuid')->nullable()->after('credential_id');
                $table->index(['type', 'passkey_uuid'], 'key_wrappers_type_passkey_uuid_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('key_wrappers') && Schema::hasColumn('key_wrappers', 'passkey_uuid')) {
            Schema::table('key_wrappers', function (Blueprint $table): void {
                $table->dropIndex('key_wrappers_type_passkey_uuid_idx');
                $table->dropColumn('passkey_uuid');
            });
        }

        if (Schema::hasTable('passkeys') && Schema::hasColumn('passkeys', 'uuid')) {
            Schema::table('passkeys', function (Blueprint $table): void {
                $table->dropIndex(['uuid']);
                $table->dropColumn('uuid');
            });
        }
    }
};

