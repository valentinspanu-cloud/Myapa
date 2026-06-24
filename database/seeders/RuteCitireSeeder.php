<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RuteCitireSeeder extends Seeder
{
    public function run(): void
    {
        $rute = ['TULCEA1','TULCEA2','TULCEA3','TULCEA4','TULCEA5','TULCEA6','TULCEA7','TULCEA8','TULCEA11'];

        foreach ($rute as $ruta) {
            DB::table('rute_citire')->updateOrInsert(
                ['nume' => $ruta],
                ['activa' => true, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        $this->command->info('Rute citire inserate: ' . count($rute));
    }
}
