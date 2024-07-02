<?php

namespace Database\Seeders\Product;

use App\Jobs\product\Type1\Faker\FillPt1CombinationInfoJob;
use App\Jobs\Product\Type1\Pt1CombinationCheckerJob;
use App\Models\Category\Category;
use App\Models\Option\Option;
use App\Models\Product\Pivot\Pt1OptionValue;
use App\Models\Product\Pivot\Type1\Pt1Category;
use App\Models\Product\Product;
use App\Models\Product\ProductTranslation;
use Faker\Factory;
use Illuminate\Database\Seeder;

class Pt1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        $categories = Category::pluck('id')->toArray(); //[1,2,...,n]
        $options = Option::with('values')->select(['id'])->get()->pluck('values.*.id', 'id')->toArray();//[1:[1,2,3,],...,n:[]]
        for ($i = 0; $i < 35; $i++) {
            // create product
            $productCol = ['type' => 1, 'reorder' => rand(0, 1),
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
            /// insert categories for product
            ///select random cats
            $catNumber = rand(1, 5);
            $rndCatsIndex = array_rand($categories, $catNumber);
            if (!is_array($rndCatsIndex)) {
                $rndCatsIndex = [$rndCatsIndex];
            }
            $cats = [];
            foreach ($rndCatsIndex as $cat) {
                $cats[] = ['category_id' => $categories[$cat], 'product_id' => $createdProduct['id']];
            }
            Pt1Category::insert($cats);
            //attach random cats options and option values
            $selectedOptVal = [];
            $optionNumber = rand(1, 4);
            $randOptionsIndex = array_rand($options, $optionNumber);
            if (!is_array($randOptionsIndex)) {
                $randOptionsIndex = [$randOptionsIndex];
            }
            foreach ($randOptionsIndex as $index) {
                $optionValues = $options[$index];
                $optionValueCount = count($optionValues);
                $randOptionValuesIndex = array_rand($optionValues, rand(1, $optionValueCount));
                if (!is_array($randOptionValuesIndex)) {
                    $randOptionValuesIndex = [$randOptionValuesIndex];
                }
                foreach ($randOptionValuesIndex as $opValIndex) {
                    $selectedOptVal[] = [
                        'option_id' => $index,
                        'option_value_id' => $optionValues[$opValIndex],
                        'product_id' => $createdProduct['id']
                    ];
                }
            }
            Pt1OptionValue::insert($selectedOptVal);
            dispatch(new Pt1CombinationCheckerJob($createdProduct['id']));
            dispatch(new FillPt1CombinationInfoJob($createdProduct['id']));
        }
    }
}
