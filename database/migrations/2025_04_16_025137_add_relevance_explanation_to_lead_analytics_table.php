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
        Schema::table('lead_analytics', function (Blueprint $table) {
            $table->text('relevance_explanation')->nullable()->after('complexity_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead_analytics', function (Blueprint $table) {
            $table->dropColumn('relevance_explanation');
        });
    }
};
