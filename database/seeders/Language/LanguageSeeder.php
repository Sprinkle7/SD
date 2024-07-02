<?php

namespace Database\Seeders\Language;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('languages')->insert([[
            'code' => 'en',
            'title' => 'English',
            'default' => 1
        ], ['code' => 'de',
            'title' => 'German',
            'default' => 0
        ]]);
    }
}
