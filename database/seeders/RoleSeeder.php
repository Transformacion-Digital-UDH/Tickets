<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Soporte']);
        Role::firstOrCreate(['name' => 'Usuario']);

        $admin1 = User::create([
            'name' => 'Marvin',
            'apellidos' => 'Campos Deza',
            'email' => 'marvinhectorcamposdeza@udh.edu.pe',
            'password' => Hash::make('987654321'),
            'celular' => '987654321',
            'activo' => true,
            'rol_id' => 1,
            'sed_id' => 1,
        ]);

        $admin2 = User::create([
            'name' => 'Jean Richard',
            'apellidos' => 'Lino Becerra',
            'email' => 'jeanlb.14.d@udh.edu.pe',
            'password' => Hash::make('914210336'),
            'celular' => '914210336',
            'activo' => true,
            'rol_id' => 1,
            'sed_id' => 1,
        ]);

        $soporte1 = User::create([
            'name' => 'Cristina',
            'apellidos' => 'Cardenas Abarrote',
            'email' => 'cristina@udh.edu.pe',
            'password' => Hash::make('password123'),
            'celular' => '987654322',
            'activo' => true,
            'rol_id' => 2,
            'sed_id' => 2,
        ]);

        $soporte2 = User::create([
            'name' => 'Rosario',
            'apellidos' => 'Beteta Arteaga',
            'email' => 'rosario@udh.edu.pe',
            'password' => Hash::make('password123'),
            'celular' => '987654323',
            'activo' => true,
            'rol_id' => 2,
            'sed_id' => 2,
        ]);

        $usuario1 = User::create([
            'name' => 'Marcelina',
            'apellidos' => 'Cartil Martel',
            'email' => 'marcelina@udh.edu.pe',
            'password' => Hash::make('password123'),
            'celular' => '987654323',
            'activo' => true,
            'rol_id' => 3,
            'sed_id' => 3,
        ]);

        $usuario2 = User::create([
            'name' => 'Xiomara',
            'apellidos' => 'Calderon Deza',
            'email' => 'xiomara@udh.edu.pe',
            'password' => Hash::make('password123'),
            'celular' => '987654323',
            'activo' => true,
            'rol_id' => 3,
            'sed_id' => 3,
        ]);

        $admin1->assignRole('Admin');
        $admin2->assignRole('Admin');
        $soporte1->assignRole('Soporte');
        $soporte2->assignRole('Soporte');
        $usuario1->assignRole('Usuario');
        $usuario2->assignRole('Usuario');
    }
}
