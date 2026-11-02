<?php

namespace Database\Seeders;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::truncate();

        $now = Carbon::now('UTC')->startOfDay();
        $tomorrow = $now->copy()->addDay();

        // 4 upcoming Saturdays (Acoustic Music Night)
        $saturdays = [];
        $cursor = $tomorrow->copy();
        while (count($saturdays) < 4) {
            if ($cursor->isSaturday()) {
                $saturdays[] = $cursor->copy();
            }
            $cursor->addDay();
        }

        foreach ($saturdays as $index => $saturday) {
            Event::create([
                'title' => 'Game & Groove Acoustic Saturdays',
                'description' => 'Addis Ababa’s finest live acoustic sessions & soulful grooves. Enjoy our signature Ethiopian cuisine, cold draft, and vibrant live bands every Saturday night.',
                'category' => 'Acoustic Saturdays',
                'date' => $saturday,
                'startTime' => '7:00 PM',
                'endTime' => '11:30 PM',
                'imageUrl' => '/images/event-acoustic.jpg',
                'featured' => $index === 0,
            ]);
        }

        // Special Midweek Music Session
        $wednesdayCursor = $tomorrow->copy();
        while (!$wednesdayCursor->isWednesday()) {
            $wednesdayCursor->addDay();
        }

        Event::create([
            'title' => 'Acoustic Soul & Jam Session',
            'description' => 'Unplugged soulful jams and acoustic rhythms in our lounge at Chamber of Commerce Building, Mexico.',
            'category' => 'Live Music',
            'date' => $wednesdayCursor,
            'startTime' => '7:00 PM',
            'endTime' => '10:30 PM',
            'imageUrl' => '/images/event-sports.jpg',
            'featured' => false,
        ]);
    }
}
