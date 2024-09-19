<?php

namespace Database\Seeders;

use App\Models\Aula;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AulaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Aula::create([
            'pab_id' => 1,
            'aul_numero' => 'Aula 1',
            'aul_activo' => true,
        ]);

        Aula::create([
            'pab_id' => 2,
            'aul_numero' => 'Aula 2',
            'aul_activo' => true,
        ]);

        Aula::create([
            'pab_id' => 3,
            'aul_numero' => 'Aula 3',
            'aul_activo' => true,
        ]);

        Aula::create([
            'pab_id' => 4,
            'aul_numero' => 'Aula 4',
            'aul_activo' => true,
        ]);

        Aula::create([
            'pab_id' => 5,
            'aul_numero' => 'Aula 5',
            'aul_activo' => true,
        ]);

        Aula::create([
            'pab_id' => 6,
            'aul_numero' => 'Aula 6',
            'aul_activo' => true,
        ]);
    }
}
