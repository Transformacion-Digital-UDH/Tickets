<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Categoria::create([
            'cat_nombre' => 'COMPUTADORAS',
            'cat_activo' => true,
        ]);

        Categoria::create([
            'cat_nombre' => 'TECLADOS',
            'cat_activo' => true,
        ]);

        Categoria::create([
            'cat_nombre' => 'TELEVISORES',
            'cat_activo' => true,
        ]);

        Categoria::create([
            'cat_nombre' => 'CARPETAS',
            'cat_activo' => true,
        ]);

        Categoria::create([
            'cat_nombre' => 'TECLADO',
            'cat_activo' => true,
        ]);

        Categoria::create([
            'cat_nombre' => 'SOFTWARE',
            'cat_activo' => true,
        ]);

        Categoria::create([
            'cat_nombre' => 'HARDWARE',
            'cat_activo' => true,
        ]);
    }
}
