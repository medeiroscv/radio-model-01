<?php

namespace Database\Seeders;

use App\Models\NewsCategory;
use Illuminate\Database\Seeder;

class NewsCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Entretenimento', 'slug' => 'entretenimento', 'color' => '#ef4444', 'sort_order' => 1],
            ['name' => 'Música', 'slug' => 'musica', 'color' => '#8b5cf6', 'sort_order' => 2],
            ['name' => 'Esportes', 'slug' => 'esportes', 'color' => '#10b981', 'sort_order' => 3],
            ['name' => 'Brasil', 'slug' => 'brasil', 'color' => '#3b82f6', 'sort_order' => 4],
            ['name' => 'Mundo', 'slug' => 'mundo', 'color' => '#0ea5e9', 'sort_order' => 5],
            ['name' => 'Tecnologia', 'slug' => 'tecnologia', 'color' => '#6366f1', 'sort_order' => 6],
            ['name' => 'Celebridades', 'slug' => 'celebridades', 'color' => '#f59e0b', 'sort_order' => 7],
            ['name' => 'Regional', 'slug' => 'regional', 'color' => '#14b8a6', 'sort_order' => 8],
        ];

        foreach ($categories as $category) {
            NewsCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}