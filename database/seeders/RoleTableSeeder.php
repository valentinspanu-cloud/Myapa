<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $names = [
            'admin' => 'Administrator',
            'consumer' => 'Consumator',
            'complaints_manager' => 'Responsabil Sesizari',
            'notifications_manager' => 'Responsabil Notificari',
	    'closingwater_manager' => 'Responsabil inchidere apa',
            'bulletinanalysis_manager' => 'Responsabil buletin analize'
        ];

        foreach ($names as $machineName => $displayName) {
            $role = new Role();
            $role->name = $machineName;
            $role->guard_name = 'web';
            $role->save();
        }
    }
}
