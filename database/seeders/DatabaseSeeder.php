<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CmsTableSeeder::class);
        $this->call(SettingTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(ComplaintsStatusTableSeeder::class);
        $this->call(NotificationStatusTableSeeder::class);
        $this->call(UserStatusTableSeeder::class);
    }
}
