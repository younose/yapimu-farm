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
            $table->decimal('rhpp_nominal', 20, 2)->nullable()->after('rhpp_document');
            $table->decimal('rhpp_transfer_nominal', 20, 2)->nullable()->after('rhpp_transfer_proof');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('closing_periods', function (Blueprint $table) {
            $table->dropColumn(['rhpp_nominal', 'rhpp_transfer_nominal']);
        });
    }
};
