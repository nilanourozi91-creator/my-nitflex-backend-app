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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->decimal('pay',7,2);
            $table->foreignId('user_id')->constrained('users','id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('pro_id')->constrained('products','id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('qars_id')->constrained('installments','id')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
