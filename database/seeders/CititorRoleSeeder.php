<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CititorRoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permisiuni modul cititor
        $permisiuni = [
            'cititor.lista',
            'cititor.store',
            'supervisor.lista',
            'supervisor.confirma',
            'supervisor.export',
        ];

        foreach ($permisiuni as $p) {
            Permission::firstOrCreate(
                ['name' => $p, 'guard_name' => 'web'],
                ['display_name' => $p]
            );
        }

        // Rol cititor de teren
        $rolCititor = Role::firstOrCreate(
            ['name' => 'cititor', 'guard_name' => 'web'],
            ['display_name' => 'Cititor Contor']
        );
        $rolCititor->syncPermissions([
            'cititor.lista',
            'cititor.store',
        ]);

        // Rol supervisor citiri
        $rolSupervisor = Role::firstOrCreate(
            ['name' => 'supervisor_citiri', 'guard_name' => 'web'],
            ['display_name' => 'Supervisor Citiri']
        );
        $rolSupervisor->syncPermissions([
            'cititor.lista',
            'cititor.store',
            'supervisor.lista',
            'supervisor.confirma',
            'supervisor.export',
        ]);

        $this->command->info('Roluri cititor + supervisor_citiri create cu succes.');
    }
}
