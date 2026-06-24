<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CititoriUserSeeder extends Seeder
{
    public function run(): void
    {
        $cititori = [
            ['name' => 'Daniela Chirachina',           'email' => 'chirachinad@aquaservtulcea.ro',    'ruta' => 'TULCEA1'],
            ['name' => 'Liliana Simona Boghici',        'email' => 'boghicil@aquaservtulcea.ro',       'ruta' => 'TULCEA2'],
            ['name' => 'Lenuta Teodorof',               'email' => 'teodorofl@aquaservtulcea.ro',      'ruta' => 'TULCEA3'],
            ['name' => 'Dorina Rotaru',                 'email' => 'rotarud@aquaservtulcea.ro',        'ruta' => 'TULCEA4'],
            ['name' => 'Luminita Petrov',               'email' => 'petrovl@aquaservtulcea.ro',        'ruta' => 'TULCEA5'],
            ['name' => 'Stela Ionascu',                 'email' => 'ionascus@aquaservtulcea.ro',       'ruta' => 'TULCEA6'],
            ['name' => 'Georgiana-Nicoleta Giurgiuvanu','email' => 'giurgiuvanug@aquaservtulcea.ro',   'ruta' => 'TULCEA7'],
            ['name' => 'Petrica Manolescu',             'email' => 'manolescup@aquaservtulcea.ro',     'ruta' => 'TULCEA8'],
            ['name' => 'Angela Serghei',                'email' => 'sergheia@aquaservtulcea.ro',       'ruta' => 'TULCEA11'],
        ];

        foreach ($cititori as $date) {
            $user = User::firstOrCreate(
                ['email' => $date['email']],
                [
                    'name'              => $date['name'],
                    'password'          => Hash::make('Schimba@2026!'),
                    'status'            => 1,
                    'notify'            => 0,
                    'email_verified_at' => now(),
                ]
            );

            $user->ruta = $date['ruta'];
            $user->save();
            $user->assignRole('cititor');

            $this->command->info("✓ {$date['name']} — {$date['ruta']}");
        }

        $this->command->info('Cititori creați cu succes.');
    }
}
