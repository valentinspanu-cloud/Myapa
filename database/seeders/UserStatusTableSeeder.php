<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserStatus;

class UserStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = ['Activ', 'Inactiv'];

        foreach ($names as $name) {
            $status = new \App\Models\UserStatus();
            $status->name = $name;
            $status->save();
        }
    }
}
