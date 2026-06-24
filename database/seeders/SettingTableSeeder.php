<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Setting;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            [
                'key' => 'email',
                'label' => 'Email contact',
                'comment' => 'Adresa ce apare in partea de jos a site-ului',
                'value' => 'test@test.ro'
            ],

            [
                'key' => 'phone',
                'label' => 'Telefon contact',
                'comment' => 'Telefon ce apare in partea de jos a site-ului',
                'value' => '0721 000 000'
            ],

            [
                'key' => 'from_complaint',
                'label' => 'Adresa "De la" pentru sesizari',
                'comment' => 'Aceasta adresa va aparea in campul "De la / From" in emailurile de sesizari trimise catre consumator',
                'value' => 'example@example.ro'
            ],

            [
                'key' => 'from_notification',
                'label' => 'Adresa "De la" pentru notificari',
                'comment' => 'Aceasta adresa va aparea in campul "De la / From" in emailurile de notificari trimise catre consumator',
                'value' => 'example@example.ro'
            ],

            [
                'key' => 'period',
                'label' => 'Perioada transmitere index',
                'comment' => 'Ex. 1-15, daca in perioada intra si sfarsitul lunii, se adauga doar inceputul perioadei, 
                    ex: 18 (inseamna 18-30, 18-31, 18-28, in functie de luna)',
                'value' => 18
            ],

            [
                'key' => 'bank',
                'label' => 'Contul bancar',
                'comment' => 'Contul bancar ce va fi folosit de procesatorul de plati',
                'value' => "12||12"
            ]

        ];

        foreach ($settings as $set) {
            $setting = new \App\Models\Setting();
            $setting->key = $set['key'];
            $setting->label = $set['label'];
            $setting->comment = $set['comment'];
            $setting->value = $set['value'];
            $setting->save();
        }
    }
}
