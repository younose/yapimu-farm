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
        Schema::create('withdraws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('user_id')->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->decimal('amount', 20, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'canceled'])
                ->default('pending');
            $table->string('note')
                ->nullable();
            $table->timestamp('processed_at')
                ->nullable();
            $table->foreignId('processed_by')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('proof')
                ->nullable();
            $table->string('description')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraws');
    }
};
