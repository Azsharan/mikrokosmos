<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Board Games', 'slug' => 'board-games'],
            ['name' => 'Card Games', 'slug' => 'card-games'],
            ['name' => 'Role Playing Games', 'slug' => 'role-playing-games'],
            ['name' => 'Miniatures', 'slug' => 'miniatures'],
            ['name' => 'Accessories', 'slug' => 'accessories'],
        ];

        foreach ($categories as $data) {
            Category::factory()->create($data);
        }
    }
}
