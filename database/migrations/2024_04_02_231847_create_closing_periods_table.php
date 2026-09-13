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
        Schema::create('closing_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->decimal('revenue', 20, 2)
                ->default(0);
            $table->decimal('expenses', 20, 2)
                ->default(0);
            $table->decimal('net_profit', 20, 2)
                ->default(0);
            $table->decimal('management_fee', 20, 2)
                ->default(0);
            $table->decimal('cooperative_fee', 20, 2)
                ->default(0);
            $table->decimal('zakat', 20, 2)
                ->default(0);
            $table->decimal('dividend', 20, 2)
                ->default(0);
            $table->decimal('dividend_foundation', 20, 2)
                ->default(0);
            $table->decimal('dividend_investor', 20, 2)
                ->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('closing_periods');
    }
};
