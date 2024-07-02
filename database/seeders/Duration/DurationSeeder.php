<?php

namespace Database\Seeders\Duration;

use App\Models\Duration\Duration;
use App\Models\Duration\DurationTranslation;
use Illuminate\Database\Seeder;

class DurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $durations = [
            ['duration' => 3, 'title' => 'standard - 3 working day'],
            ['duration' => 5, 'title' => 'standard - 5 working day'],
            ['duration' => 7, 'title' => 'standard - 7 working day'],
            ['duration' => 2, 'title' => 'express - 48h'],
            ['duration' => 1, 'title' => 'express - 24h']
        ];

        foreach ($durations as $duration) {
            $du = Duration::create(['duration' => $duration['duration']]);
            $duTrans = [];
            $duTrans[] = ['title' => $duration['title'], 'duration_id' => $du['id'], 'language' => 'en'];
            $duTrans[] = ['title' => $duration['title'] . ' german', 'duration_id' => $du['id'], 'language' => 'de'];
            DurationTranslation::insert($duTrans);
        }
    }
}
