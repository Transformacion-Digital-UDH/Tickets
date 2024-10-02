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
        // Tickets en estado Abierto
        Ticket::create([
            'use_id' => 5,
            'cat_id' => 1,
            'pri_id' => 1,
            'pab_id' => 5,
            'aul_id' => 1,
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
            'aul_id' => 2,
            'tic_titulo' => 'TITULO 2',
            'tic_descripcion' => 'DESCRIPCION 2',
            'tic_archivo' => 'ARCHIVO 2',
            'tic_estado' => 'Abierto',
            'tic_activo' => true,
        ]);

        // Tickets en estado Asignado
        Ticket::create([
            'use_id' => 5,
            'cat_id' => 3,
            'pri_id' => 3,
            'pab_id' => 1,
            'aul_id' => 3,
            'tic_titulo' => 'TITULO 3',
            'tic_descripcion' => 'DESCRIPCION 3',
            'tic_archivo' => 'ARCHIVO 3',
            'tic_estado' => 'Asignado',
            'tic_activo' => true,
        ]);

        Ticket::create([
            'use_id' => 6,
            'cat_id' => 4,
            'pri_id' => 2,
            'pab_id' => 2,
            'aul_id' => 4,
            'tic_titulo' => 'TITULO 4',
            'tic_descripcion' => 'DESCRIPCION 4',
            'tic_archivo' => 'ARCHIVO 4',
            'tic_estado' => 'Asignado',
            'tic_activo' => true,
        ]);

        // Tickets en estado En Progreso
        Ticket::create([
            'use_id' => 5,
            'cat_id' => 1,
            'pri_id' => 1,
            'pab_id' => 5,
            'aul_id' => 1,
            'tic_titulo' => 'TITULO 5',
            'tic_descripcion' => 'DESCRIPCION 5',
            'tic_archivo' => 'ARCHIVO 5',
            'tic_estado' => 'En progreso',
            'tic_activo' => true,
        ]);

        Ticket::create([
            'use_id' => 6,
            'cat_id' => 2,
            'pri_id' => 2,
            'pab_id' => 6,
            'aul_id' => 2,
            'tic_titulo' => 'TITULO 6',
            'tic_descripcion' => 'DESCRIPCION 6',
            'tic_archivo' => 'ARCHIVO 6',
            'tic_estado' => 'En progreso',
            'tic_activo' => true,
        ]);

        // Tickets en estado Resuelto
        Ticket::create([
            'use_id' => 5,
            'cat_id' => 3,
            'pri_id' => 3,
            'pab_id' => 1,
            'aul_id' => 3,
            'tic_titulo' => 'TITULO 7',
            'tic_descripcion' => 'DESCRIPCION 7',
            'tic_archivo' => 'ARCHIVO 7',
            'tic_estado' => 'Resuelto',
            'tic_activo' => true,
        ]);

        Ticket::create([
            'use_id' => 6,
            'cat_id' => 4,
            'pri_id' => 2,
            'pab_id' => 2,
            'aul_id' => 4,
            'tic_titulo' => 'TITULO 8',
            'tic_descripcion' => 'DESCRIPCION 8',
            'tic_archivo' => 'ARCHIVO 8',
            'tic_estado' => 'Resuelto',
            'tic_activo' => true,
        ]);

        // Tickets en estado Cerrado
        Ticket::create([
            'use_id' => 5,
            'cat_id' => 1,
            'pri_id' => 1,
            'pab_id' => 5,
            'aul_id' => 1,
            'tic_titulo' => 'TITULO 9',
            'tic_descripcion' => 'DESCRIPCION 9',
            'tic_archivo' => 'ARCHIVO 9',
            'tic_estado' => 'Cerrado',
            'tic_activo' => true,
        ]);

        Ticket::create([
            'use_id' => 6,
            'cat_id' => 2,
            'pri_id' => 2,
            'pab_id' => 6,
            'aul_id' => 2,
            'tic_titulo' => 'TITULO 10',
            'tic_descripcion' => 'DESCRIPCION 10',
            'tic_archivo' => 'ARCHIVO 10',
            'tic_estado' => 'Cerrado',
            'tic_activo' => true,
        ]);

        // Tickets en estado Reabierto
        Ticket::create([
            'use_id' => 5,
            'cat_id' => 3,
            'pri_id' => 3,
            'pab_id' => 1,
            'aul_id' => 3,
            'tic_titulo' => 'TITULO 11',
            'tic_descripcion' => 'DESCRIPCION 11',
            'tic_archivo' => 'ARCHIVO 11',
            'tic_estado' => 'Reabierto',
            'tic_activo' => true,
        ]);

        Ticket::create([
            'use_id' => 6,
            'cat_id' => 4,
            'pri_id' => 2,
            'pab_id' => 2,
            'aul_id' => 4,
            'tic_titulo' => 'TITULO 12',
            'tic_descripcion' => 'DESCRIPCION 12',
            'tic_archivo' => 'ARCHIVO 12',
            'tic_estado' => 'Reabierto',
            'tic_activo' => true,
        ]);
    }
}
