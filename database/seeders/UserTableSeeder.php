<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new \App\Models\User();
        $user->name = 'Administrator';
        $user->email = 'admin@siveco.ro';
        $user->password = bcrypt(env('ADMIN_PASS'));
        $user->status = 1;
        $user->email_verified_at = \Carbon\Carbon::now();
        $user->save();

        $user->assignRole('admin');
    }
}
