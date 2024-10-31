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
            $table->foreignId('use_id')->constrained('users');
            $table->foreignId('cat_id')->constrained('categorias');
            $table->foreignId('pri_id')->nullable()->constrained('prioridads');
            $table->foreignId('pab_id')->constrained('pabellons');
            $table->foreignId('aul_id')->constrained('aulas');
            $table->string('tic_titulo')->nullable();
            $table->text('tic_descripcion')->nullable();
            $table->string('tic_archivo')->nullable();
            $table->string('tic_estado')->nullable();
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
