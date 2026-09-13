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
        Schema::table('closing_periods', function (Blueprint $table) {
            $table->integer('shares_count')
                ->default(0)
                ->after('dividend_investor');
            $table->decimal('dividend_nominal', 20, 2)
                ->default(0)
                ->after('dividend_investor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
