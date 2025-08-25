<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            TipoDocumentoSeeder::class,
            UserSeeder::class,
            FichaSeeder::class,
            InstructorSeeder::class,
            EtapaProductivaSeeder::class,
        ]);
    }
}