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
        Schema::create('foundation_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('date');
            $table->string('description');
            $table->decimal('debit', 20, 2)
                ->nullable()
                ->default(0);
            $table->decimal('credit', 20, 2)
                ->nullable()
                ->default(0);
            $table->decimal('balance', 20, 2)
                ->nullable()
                ->default(0);
            $table->string('proof')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foundations');
    }
};
