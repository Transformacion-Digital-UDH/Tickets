<?php

namespace Database\Seeders;

use App\Models\Aula;
use Illuminate\Database\Seeder;

class AulaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $totalAulasPorPiso = 7; // Ahora hay 7 aulas por piso
        $pabellon = 6; // Solo queremos generar para el pabellón 6
        $pisos = 6; // Pabellón 6 tiene 6 pisos

        for ($piso = 1; $piso <= $pisos; $piso++) {
            for ($aula = 1; $aula <= $totalAulasPorPiso; $aula++) {
                $numeroAula = sprintf("%d%02d", $piso, $aula); // Formato de 3 dígitos: Piso + Aula (ej: 101, 102...)
                Aula::create([
                    'pab_id' => $pabellon,
                    'aul_numero' => $numeroAula,
                    'aul_activo' => true,
                ]);
            }
        }
    }
}
