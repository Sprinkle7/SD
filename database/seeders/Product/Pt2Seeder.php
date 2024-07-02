<?php

namespace Database\Seeders\Product;

use App\Models\Category\Category;
use App\Models\Product\Pivot\Type1\Pt1Combination;
use App\Models\Product\Pivot\Type2\Pt2Category;
use App\Models\Product\Pivot\Type2\Pt2Pt1Combination;
use App\Models\Product\Product;
use App\Models\Product\ProductTranslation;
use Faker\Factory;
use Illuminate\Database\Seeder;
use PHPUnit\Framework\Constraint\Count;

class Pt2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        $categories = \App\Models\Category\Category::with('productType1')->get()
            ->pluck('productType1.*.id', 'id')->toArray(); //[1:[1,2,3,],...,n:[]]
        for ($i = 0; $i < 35; $i++) {
            // create product
            $productCol = ['type' => 2, 'reorder' => 1,
                'cover_image' => '', 'video' => ''];
            $createdProduct = Product::create($productCol);
            ///insert translations
            $title = $faker->name;
            $Desc = $faker->text(250);
            $productT = [['title' => $title, 'benefit_desc' => $Desc,
                'item_desc' => $Desc, 'language' => 'en', 'product_id' => $createdProduct['id']],
                ['title' => $title . 'german', 'benefit_desc' => $Desc,
                    'item_desc' => $Desc, 'language' => 'de', 'product_id' => $createdProduct['id']]];
            ProductTranslation::insert($productT);
            /// attach pt1 and pt1 combs to pt2
            $pt2Pt1Combs = [];
            $catRandIndexes = array_rand($categories, rand(1, 5));
            if (!is_array($catRandIndexes)) {
                $catRandIndexes = [$catRandIndexes];
            }
            //insert category
            $pt2Cat = [];
            foreach ($catRandIndexes as $i) {
                $pt2Cat[] = ['category_id' => $i, 'product_id' => $createdProduct['id'],
                    'arrange' => rand(1, 5), 'has_no_select' => rand(0, 1)];
            }
            Pt2Category::insert($pt2Cat);
            //inset pt1 combinations
            foreach ($catRandIndexes as $catRandIndex) {
                $pt1s = $categories[$catRandIndex];
                $pt1sCount = count($pt1s);
                $pt1sRandIndexes = array_rand($pt1s, rand(1, $pt1sCount));
                if (!is_array($pt1sRandIndexes)) {
                    $pt1sRandIndexes = [$pt1sRandIndexes];
                }
                foreach ($pt1sRandIndexes as $pt1sRandIndex) {
                    $pt1combs = Pt1Combination::where('product_id', $pt1s[$pt1sRandIndex])->pluck('id')->toArray();
                    $pt1CombsCount = count($pt1combs);
                    $pt1CombRandIndexes = array_rand($pt1combs, rand(1, $pt1CombsCount));
                    foreach ($pt1CombRandIndexes as $pt1CombRandIndex) {
                        $pt2Pt1Combs[] = ['pt2_id' => $createdProduct['id'],
                            'category_id' => $catRandIndex, 'pt1_id' => $pt1s[$pt1sRandIndex],
                            'pt1_combination_id' => $pt1combs[$pt1CombRandIndex]];
                    }
                }

            }
            Pt2Pt1Combination::insert($pt2Pt1Combs);
        }
    }
}
