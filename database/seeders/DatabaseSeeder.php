<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SedeSeeder::class);
        $this->call(RolSeeder::class);
        $this->call(CategoriaSeeder::class);
        $this->call(PrioridadSeeder::class);
        $this->call(PabellonSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(AulaSeeder::class);
        $this->call(TicketSeeder::class);
        $this->call(AsignadoSeeder::class);
    }
}
