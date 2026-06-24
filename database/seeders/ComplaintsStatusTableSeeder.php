<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ComplaintStatus;

class ComplaintsStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = ['Nerezolvata', 'In curs de procesare', 'Rezolvata'];

        foreach ($names as $name) {
            $status = new \App\Models\ComplaintStatus();
            $status->name = $name;
            $status->save();
        }
    }
}
