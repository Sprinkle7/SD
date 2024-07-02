<?php

namespace Database\Seeders;

use Database\Seeders\Category\CategorySeeder;
use Database\Seeders\Duration\DurationSeeder;
use Database\Seeders\Footer\FooterSeeder;
use Database\Seeders\Language\LanguageSeeder;
use Database\Seeders\Menu\MenuSeeder;
use Database\Seeders\Option\OptionSeeder;
use Database\Seeders\Product\Pt1Seeder;
use Database\Seeders\Product\Pt2Seeder;
use Database\Seeders\User\RoleSeeder;
use Database\Seeders\User\UserSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            //***always should run
            LanguageSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            FooterSeeder::class
            //
//            CategorySeeder::class,
//            OptionSeeder::class,
//            DurationSeeder::class,
//            MenuSeeder::class,
//            Pt1Seeder::class,
//            Pt2Seeder::class
            //            LanguageReferenceSeeder::class,
        ]);
    }
}
