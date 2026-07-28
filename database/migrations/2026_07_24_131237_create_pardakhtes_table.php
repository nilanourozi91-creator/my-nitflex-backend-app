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
        Schema::create('pardakhtes', function (Blueprint $table) {
            $table->id();
            $table->date('pay_date')->default(now());
            $table->foreignId('qars_id')->constrained('qarses','id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('pro_id')->constrained('prodects','id')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::dropIfExists('pardakhtes');
    }
};
