<?php

namespace Database\Seeders;

use App\Models\Prioridad;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrioridadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Prioridad::create([
            'pri_nombre' => 'Alta',
            'pri_activo' => true,
        ]);

        Prioridad::create([
            'pri_nombre' => 'Media',
            'pri_activo' => true,
        ]);

        Prioridad::create([
            'pri_nombre' => 'Baja',
            'pri_activo' => true,
        ]);
    }
}
