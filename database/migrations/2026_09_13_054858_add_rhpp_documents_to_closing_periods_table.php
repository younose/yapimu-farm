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
            $table->string('rhpp_document')->nullable()->after('dividend_nominal');
            $table->string('rhpp_transfer_proof')->nullable()->after('rhpp_document');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('closing_periods', function (Blueprint $table) {
            $table->dropColumn(['rhpp_document', 'rhpp_transfer_proof']);
        });
    }
};
