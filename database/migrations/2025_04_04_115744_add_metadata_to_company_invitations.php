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
        Schema::table('company_invitations', function (Blueprint $table) {
            $table->json('metadata')->nullable()->after('activated_by_user_id')
                  ->comment('Дополнительные метаданные приглашения в JSON формате');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_invitations', function (Blueprint $table) {
            $table->dropColumn('metadata');
        });
    }
};
