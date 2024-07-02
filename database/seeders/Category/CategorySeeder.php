<?php

namespace Database\Seeders\Category;

use App\Models\Category\Category;
use App\Models\Category\CategoryTranslation;
use Faker\Factory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = ['LED', 'left LED', 'right LED', 'Wall', 'right Wall', 'left Wall',
            'flag', 'desk', 'weights', 'paper'];
        foreach ($categories as $category) {
            $cat = Category::create([]);
            $catTrans = [];
            $catTrans[] = ['title' => $category, 'category_id' => $cat['id'], 'language' => 'en'];
            $catTrans[] = ['title' => $category . ' german', 'category_id' => $cat['id'], 'language' => 'de'];
            CategoryTranslation::insert($catTrans);
        }

        $faker = Factory::create();
        for ($i = 0; $i < 50; $i++) {
            $cat = Category::create([]);
            $catTrans = [];
            $title = $faker->name;
            $catTrans[] = ['title' => $title, 'category_id' => $cat['id'], 'language' => 'en'];
            $catTrans[] = ['title' => $title . ' german', 'category_id' => $cat['id'], 'language' => 'de'];
            CategoryTranslation::insert($catTrans);
        }
    }
}
