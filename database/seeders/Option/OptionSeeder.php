<?php

namespace Database\Seeders\Option;

use App\Models\Option\Option;
use App\Models\Option\OptionTranslation;
use App\Models\Option\OptionValue;
use App\Models\Option\OptionValueTranslation;
use Faker\Factory;
use Illuminate\Database\Seeder;

class OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $options = [
            ['title' => 'color', 'values' => ['red', 'blue', 'green', 'white', 'black']],
            ['title' => 'material', 'values' => ['iron', 'wood', 'glass']],
            ['title' => 'size', 'values' => ['1*2', '1*3', '1*4', '2*3', '3*4']],
            ['title' => 'weight', 'values' => ['1k', '2k', '3k']],
            ['title' => 'height', 'values' => ['1m', '2m', '3m']],
            ['title' => 'print', 'values' => ['with pic', 'no pic']],
        ];

        foreach ($options as $option) {
            $opt = Option::create([]);
            $optTrans = [];
            $optTrans[] = ['title' => $option['title'], 'option_id' => $opt['id'], 'language' => 'en'];
            $optTrans[] = ['title' => $option['title'] . ' german', 'option_id' => $opt['id'], 'language' => 'de'];
            OptionTranslation::insert($optTrans);

            $optValTrans = [];
            foreach ($option['values'] as $value) {
                $optVal = OptionValue::create(['option_id' => $opt['id']]);
                $optValTrans[] = ['title' => $value,
                    'option_value_id' => $optVal['id'], 'option_id' => $opt['id'], 'language' => 'en'];
                $optValTrans[] = ['title' => $value . ' german',
                    'option_value_id' => $optVal['id'], 'option_id' => $opt['id'], 'language' => 'de'];
            }
            OptionValueTranslation::insert($optValTrans);
        }

        $facker = Factory::create();
        for ($i = 0; $i < 50; $i++) {
            $opt = Option::create([]);
            $optTitle = $facker->name;
            $optTrans = [];
            $optTrans[] = ['title' => $optTitle, 'option_id' => $opt['id'], 'language' => 'en'];
            $optTrans[] = ['title' => $optTitle . ' german', 'option_id' => $opt['id'], 'language' => 'de'];
            OptionTranslation::insert($optTrans);

            $optValTrans = [];
            $optValNumber = rand(3, 10);

            for ($j = 0; $j < $optValNumber; $j++) {
                $optVal = OptionValue::create(['option_id' => $opt['id']]);
                $optValTrans[] = ['title' => 'value' . $optTitle . $j,
                    'option_value_id' => $optVal['id'], 'option_id' => $opt['id'], 'language' => 'en'];
                $optValTrans[] = ['title' => 'value' . $optTitle . $j . ' german',
                    'option_value_id' => $optVal['id'], 'option_id' => $opt['id'], 'language' => 'de'];
            }
            OptionValueTranslation::insert($optValTrans);
        }
    }
}
