<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationStatus;

class NotificationStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = ['Trimisa', 'Nu a fost trimisa', 'In curs de trimitere'];

        foreach ($names as $name) {
            $status = new \App\Models\NotificationStatus();
            $status->name = $name;
            $status->save();
        }
    }
}
