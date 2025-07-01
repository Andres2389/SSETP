<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class GenerarPermisos extends Command
{
    protected $signature = 'permisos:generar {recurso}';
    protected $description = 'Genera permisos CRUD para un recurso';

    public function handle()
    {
        $recurso = strtolower($this->argument('recurso'));

        $acciones = ['ver', 'crear', 'editar', 'eliminar'];

        foreach ($acciones as $accion) {
            $permiso = "$accion $recurso";
            if (!Permission::where('name', $permiso)->exists()) {
                Permission::create(['name' => $permiso]);
                $this->info("✅ Permiso creado: $permiso");
            } else {
                $this->warn("⚠️ Ya existe: $permiso");
            }
        }
    }
}
