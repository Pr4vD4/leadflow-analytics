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
        Schema::table('leads', function (Blueprint $table) {
            $table->timestamp('first_response_at')->nullable()->after('updated_at');
            $table->timestamp('resolved_at')->nullable()->after('first_response_at');
            $table->integer('response_time_minutes')->nullable()->after('resolved_at');
            $table->integer('resolution_time_minutes')->nullable()->after('response_time_minutes');
            $table->timestamp('status_changed_at')->nullable()->after('resolution_time_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'first_response_at',
                'resolved_at',
                'response_time_minutes',
                'resolution_time_minutes',
                'status_changed_at'
            ]);
        });
    }
};
