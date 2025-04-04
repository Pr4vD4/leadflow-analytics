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
        Schema::table('companies', function (Blueprint $table) {
            $table->integer('description_quality_score')->nullable()->after('description')
                ->comment('Оценка качества описания компании (1-10)');
            $table->json('ai_analysis')->nullable()->after('description_quality_score')
                ->comment('Результаты анализа описания компании AI');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('description_quality_score');
            $table->dropColumn('ai_analysis');
            //
        });
    }
};
