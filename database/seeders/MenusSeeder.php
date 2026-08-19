<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenusSeeder extends Seeder
{
    public function run(): void
    {
        $mainMenu = Menu::firstOrCreate(
            ['slug' => 'main-menu'],
            ['name' => 'Menu principal', 'location' => 'main', 'is_active' => true]
        );

        $items = [
            ['label' => 'Ao Vivo', 'route' => 'home', 'sort_order' => 1],
            ['label' => 'Promoções', 'route' => 'promotions.index', 'sort_order' => 2],
            ['label' => 'Entretenimento', 'route' => 'news.index', 'sort_order' => 3],
            ['label' => 'Música', 'route' => 'songs.index', 'sort_order' => 4],
            ['label' => 'Programação', 'route' => 'schedule.index', 'sort_order' => 5],
            ['label' => 'A Rádio', 'route' => 'about', 'sort_order' => 6],
            ['label' => 'Contato', 'route' => 'contact', 'sort_order' => 7],
        ];

        foreach ($items as $item) {
            MenuItem::firstOrCreate(
                ['menu_id' => $mainMenu->id, 'label' => $item['label']],
                $item
            );
        }

        $footerMenu = Menu::firstOrCreate(
            ['slug' => 'footer-menu'],
            ['name' => 'Menu do rodapé', 'location' => 'footer', 'is_active' => true]
        );

        $footerItems = [
            ['label' => 'Programação', 'route' => 'schedule.index', 'sort_order' => 1],
            ['label' => 'Promoções', 'route' => 'promotions.index', 'sort_order' => 2],
            ['label' => 'Notícias', 'route' => 'news.index', 'sort_order' => 3],
            ['label' => 'Contato', 'route' => 'contact', 'sort_order' => 4],
        ];

        foreach ($footerItems as $item) {
            MenuItem::firstOrCreate(
                ['menu_id' => $footerMenu->id, 'label' => $item['label']],
                $item
            );
        }
    }
}