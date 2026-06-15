<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Cms;

class CmsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $pages = [
            'despre-noi' => 'Despre noi',
            'servicii' => 'Servicii',
            'anunturi-publice' => 'Anunturi publice',
            'contact' => 'Contact',
            'termeni-si-conditii' => 'Termeni si conditii',
            'gdpr' => 'GDPR',
            'politica-cookie' => 'Politica cookie',
            'puncte-de-lucru' => 'Puncte de lucru',
            'agentii' => 'Agentii'
        ];

        foreach ($pages as $slug => $title) {
            $page = new \App\Models\Cms();
            $page->title = $title;
            $page->slug = $slug;
            $page->status = 'Activ';
            $page->content = "Pagina in constructie";
            $page->save();
        }
    }
}
