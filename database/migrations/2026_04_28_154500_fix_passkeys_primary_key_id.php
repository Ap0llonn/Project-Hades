<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('passkeys')) {
            return;
        }

        if (!Schema::hasColumn('passkeys', 'id')) {
            Schema::table('passkeys', function (Blueprint $table) {
                $table->id();
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('passkeys')) {
            return;
        }

        if (Schema::hasColumn('passkeys', 'id')) {
            Schema::table('passkeys', function (Blueprint $table) {
                $table->dropColumn('id');
            });
        }
    }
};
