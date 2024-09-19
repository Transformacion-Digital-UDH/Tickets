<?php

namespace Database\Seeders;

use App\Models\Ticket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ticket::create([
            'use_id' => 5,
            'cat_id' => 1,
            'pri_id' => 1,
            'pab_id' => 5,
            'tic_titulo' => 'TITULO 1',
            'tic_descripcion' => 'DESCRIPCION 1',
            'tic_archivo' => 'ARCHIVO 1',
            'tic_estado' => 'Abierto',
            'tic_activo' => true,
        ]);

        Ticket::create([
            'use_id' => 6,
            'cat_id' => 2,
            'pri_id' => 2,
            'pab_id' => 6,
            'tic_titulo' => 'TITULO 2',
            'tic_descripcion' => 'DESCRIPCION 2',
            'tic_archivo' => 'ARCHIVO 2',
            'tic_estado' => 'Cerrado',
            'tic_activo' => true,
        ]);

        Ticket::create([
            'use_id' => 5,
            'cat_id' => 3,
            'pri_id' => 3,
            'pab_id' => 1,
            'tic_titulo' => 'TITULO 3',
            'tic_descripcion' => 'DESCRIPCION 3',
            'tic_archivo' => 'ARCHIVO 3',
            'tic_estado' => 'En progreso',
            'tic_activo' => true,
        ]);

        Ticket::create([
            'use_id' => 6,
            'cat_id' => 4,
            'pri_id' => 2,
            'pab_id' => 2,
            'tic_titulo' => 'TITULO 4',
            'tic_descripcion' => 'DESCRIPCION 4',
            'tic_archivo' => 'ARCHIVO 4',
            'tic_estado' => 'Finalizado',
            'tic_activo' => true,
        ]);
    }
}
