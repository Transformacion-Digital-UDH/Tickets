<?php

namespace Database\Seeders;

use App\Models\Ticket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AsignadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $soporteIds = [3, 4];

        $tickets = Ticket::all();

        foreach ($tickets as $ticket) {
            DB::table('asignados')->insert([
                'tic_id' => $ticket->id,
                'sop_id' => $soporteIds[array_rand($soporteIds)],
                'es_asignado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
