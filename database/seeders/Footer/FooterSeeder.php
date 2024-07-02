<?php

namespace Database\Seeders\Footer;

use App\Models\Footer\FooterSection;
use App\Models\Footer\FooterSectionTranslation;
use Illuminate\Database\Seeder;

class FooterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FooterSection::insert([[], [], []]);
    }
}
