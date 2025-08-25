<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos
        $permissions = [
            // Fichas
            'view_fichas',
            'create_fichas',
            'edit_fichas',
            'delete_fichas',
            'import_fichas',

            // Instructores
            'view_instructores',
            'create_instructores',
            'edit_instructores',
            'delete_instructores',

            // Etapa Productiva
            'view_etapa_productiva',
            'create_etapa_productiva',
            'edit_etapa_productiva',
            'delete_etapa_productiva',

            // Bitácoras
            'view_bitacoras',
            'upload_bitacoras',
            'review_bitacoras',
            'delete_bitacoras',

            // Users
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',

            // System
            'access_admin_panel',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $instructorRole = Role::firstOrCreate(['name' => 'instructor']);
        $aprendizRole = Role::firstOrCreate(['name' => 'aprendiz']);
        $coordinacionRole = Role::firstOrCreate(['name' => 'coordinacion']);
        $apoyocoordinacionRole = Role::firstOrCreate(['name' => 'apoyo coordinacion']);
        $apoyoetapaRole = Role::firstOrCreate(['name' => 'apoyo etapa productiva']);

        // Asignar permisos a roles
        // Admin: todos los permisos
        $adminRole->syncPermissions(Permission::all());

        // Instructor: permisos específicos
        $instructorPermissions = [

            'view_bitacoras',
            'review_bitacoras',
            'access_admin_panel',
        ];
        $instructorRole->syncPermissions($instructorPermissions);
        // Coordinacion: permisos intermedios
        $coordinacionPermissions = [
            'view_fichas',
            'view_instructores',
            'view_etapa_productiva'

        ];;
        $coordinacionRole->syncPermissions($coordinacionPermissions);
        // Apoyo Coordinacion: permisos limitados
        $apoyocoordinacionPermissions = [
            'view_fichas',
            'view_instructores',
            'view_etapa_productiva'
        ];
        $apoyocoordinacionRole->syncPermissions($apoyocoordinacionPermissions);

        // Apoyo Etapa Productiva: permisos específicos
        $apoyoetapaPermissions = [
            'view_etapa_productiva',
            'create_etapa_productiva',
            'edit_etapa_productiva',
            'delete_etapa_productiva',
            'view_bitacoras',
            'upload_bitacoras',
            'access_admin_panel',
        ];
        $apoyoetapaRole->syncPermissions($apoyoetapaPermissions);

        // Aprendiz: permisos muy limitados
        $aprendizPermissions = [
            'view_bitacoras',
            'upload_bitacoras',
            'access_admin_panel',
        ];
        $aprendizRole->syncPermissions($aprendizPermissions);
    }
}
