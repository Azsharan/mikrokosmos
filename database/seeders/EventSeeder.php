<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Event;
use App\Models\EventType;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id');
        $eventTypes = EventType::pluck('id');

        Event::factory()
            ->count(15)
            ->make()
            ->each(function (Event $event) use ($categories, $eventTypes) {
                $event->category_id = $categories->random();
                $event->event_type_id = $eventTypes->random();
                $event->save();
            });
    }
}
