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
        Schema::create('prodect_ds', function (Blueprint $table) {
            $table->id();
            $table->date('expire_date')->default(now());
            $table->date('min_date')->default(now());
            $table->string('Amount');
            $table->integer('price');
            $table->integer('stock');
            $table->foreignId('pro_id')->constrained('prodects','id')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prodect_ds');
    }
};
