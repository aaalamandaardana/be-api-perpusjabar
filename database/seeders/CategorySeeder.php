<?php

namespace Database\Seeders;

use App\Models\Category;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $listCategories = [
            ['name' =>  'Novel'],
            ['name' =>  'Komik'],
            ['name' =>  'Teknologi'],
            ['name' =>  'Budaya'],
            ['name' =>  'Manga']
        ];
        
        foreach ($listCategories as $key => $value) {
            Category::create($value);
        }
    }
}