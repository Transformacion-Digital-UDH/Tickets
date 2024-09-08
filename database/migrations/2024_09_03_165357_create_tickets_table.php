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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('use_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('cat_id')->constrained('categorias')->cascadeOnDelete();
            $table->foreignId('pri_id')->constrained('prioridads')->cascadeOnDelete();
            $table->foreignId('pab_id')->constrained('pabellons')->cascadeOnDelete();
            $table->string('tic_titulo');
            $table->string('tic_descripcion');
            $table->string('tic_archivo');
            $table->string('tic_estado');
            $table->boolean('tic_activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
