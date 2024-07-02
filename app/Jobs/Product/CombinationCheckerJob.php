<?php

namespace App\Jobs\Product;

use App\Helper\Combination\CombinationGenerator;
use App\Helper\Combination\Combinations;
use App\Helper\json\JsonD;
use App\Models\Product\Combination\Combination;
use App\Models\Product\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CombinationCheckerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $productId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($productId)
    {
        $this->productId = $productId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Product::where('id', $this->productId)->update(['is_active' => 0]);
        $product = Product::find($this->productId);
        $optionValues = Product::fetchOptionValuesWithOutTitle($product);
        $optionValuesArr = [];
        $optionValuePrice = [];
        $option = [];
        $noSelectOptionCount = 0;
        /**
         * -------------------grouping options
         */
        foreach ($optionValues as $index => $optionValue) {
            $combinations = JsonD::json_decode(($optionValue['option_values']));
            $optionValuesArr[$index] = array_map(function ($a) use ($optionValue) {
                return $a->option_value_id;
            }, $combinations);
            foreach ($combinations as $opv) {
                $optionValuePrice[$opv->option_value_id] = $opv->price;
            }
            $option[$index] = $optionValue['option_id'];
            if ($optionValue['has_no_select'] == 1) {
                $noSelectOptionCount++;
            }
        }
        /**
         * -------------------generate all the combination
         */
        $groupCount = Count($optionValuesArr);
        $freshCombs = [];
        for ($i = 0; $i < $groupCount; $i++) {
            $based = [];
            $based[] = $optionValuesArr[$i];
            $result = CombinationGenerator::generate($based);
            foreach ($result as $index => $r) {
                asort($result[$index]);
            }
            $freshCombs = array_merge($freshCombs, $result);
            $indexes = range($i + 1, $groupCount - 1);

            for ($len = 1; $len < ($groupCount - $i); $len++) {
                $indexCombs = [];
                foreach (new Combinations($indexes, $len) as $substring) {
                    $indexCombs[] = $substring;
                }
                foreach ($indexCombs as $indexComb) {
                    $groupSlice = [];
                    $groupSlice[] = $based[0];
                    foreach ($indexComb as $inc) {
                        $groupSlice[] = $optionValuesArr[$inc];
                    }
                    $result = CombinationGenerator::generate($groupSlice);
                    foreach ($result as $index => $r) {
                        asort($result[$index]);
                    }
                    $freshCombs = array_merge($freshCombs, $result);
                }
            }
        }
        /**
         * -------------------update database with new combination
         */
        $removedCombinations = Combination::where('product_id', $this->productId)
            ->pluck('id', 'combination')->toArray();
        if ($groupCount == $noSelectOptionCount) {
            if (isset($removedCombinations[''])) {
                unset($removedCombinations['']);
            } else {
                Combination::create(['product_id' => $this->productId, 'combination' => '']);
            }
        }

        foreach ($freshCombs as $combValues) {
            $combStr = implode(',', $combValues);
            $price = 0;

            foreach ($combValues as $val) {
                $price = $price + $optionValuePrice[$val];
            }

            if (isset($removedCombinations[$combStr])) {
                $combination = \App\Models\Product\Combination\Combination::find($removedCombinations[$combStr]);
                unset($removedCombinations[$combStr]);
            } else {
                $combination = \App\Models\Product\Combination\Combination::
                create(['product_id' => $this->productId, 'combination' => $combStr]);
                $p2P1Combs = [];
                foreach ($combValues as $val) {
                    if ($val !== "") {
                        $p2P1Combs[] = [
                            'combination_id' => $combination['id'],
                            'option_value_id' => $val
                        ];
                    }
                }
                DB::table('combination_option_value')->insert($p2P1Combs);
            }
            $combination->update(['price' => $price, 'real_price' => $price]);

        }
        if (count($removedCombinations)) {
            $removedCombId = array_values($removedCombinations);
            Combination::whereIn('id', $removedCombId)->delete();
            DB::table('combination_option_value')
                ->whereIn('combination_id', $removedCombId)->delete();
//        \App\Models\Product\Combination\CombinationImage::whereIn('combination_id', $removedCombId)
//            ->update(['combination_id' => null]);
            ///remove files from server
//        Pt2CombinationPt1Combination::whereIn('pt2_combination_id', $removedCombId)->delete();
//        Cart::whereIn('combination_id', $removedCombId)->delete();
        }
    }
}
