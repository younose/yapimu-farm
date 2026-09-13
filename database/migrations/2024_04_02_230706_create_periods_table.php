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
        Schema::create('periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date')
                ->nullable();
            $table->date('end_date')
                ->nullable();
            $table->decimal('management_fee', 20, 2)
                ->default(6500000);
            $table->decimal('cooperative_percentage', 5, 2)
                ->default(2);
            $table->decimal('zakat_percentage', 5, 2)
                ->default(2.5);
            $table->enum('status', ['open', 'closed'])
                ->default('closed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodes');
    }
};
