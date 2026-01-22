<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\EventType;
use Illuminate\Database\Seeder;

class EventTypeSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id');

        $types = ['Tournament', 'Community Meetup', 'Workshop', 'Online League'];

        foreach ($types as $type) {
            EventType::factory()->create([
                'name' => $type,
                'category_id' => $categories->random(),
            ]);
        }
    }
}
