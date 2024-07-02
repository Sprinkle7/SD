<?php

use App\Helper\Combination\CombinationGenerator;
use App\Helper\Sort\BubbleSort;
use App\Mail\ForgetPassword;
use App\Models\Product\Product;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use MathPHP\Probability\Combinatorics;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
//    return storage_path('app/OrderCsv/').'64acf25eb1d8a1689055838.csv';
//    return \Illuminate\Support\Facades\URL::to('/api/fetch/invoice?file=').'64acf25eb1d8a1689055838.csv';
//    return url('/api/fetch/invoice?file=') . '64acf25eb1d8a1689055838.csv';

//    $user = \App\Models\User::find(32);
//    $file = '64acf25eb1d8a1689055838.csv';
//    Mail::to('nima48672@gmail.com')->queue((new \App\Mail\NewInvoice($user, $file))->onQueue('mail'));
//
//    dispatch(new \App\Jobs\Cart\Invoice\GenerateCSVJob('16862168346481a082ecf95'));
    try {
//        return env('WEBHOOK_SECRET');
//        \Illuminate\Support\Facades\Storage::disk('local')->put('file.txt','sdfsdf' );
        $path = storage_path('logs/laravel.log');
//        $path = storage_path('app/file.txt');
        return response()->download($path);
    } catch (Exception $exception) {
        return response()->json($exception->getMessage());
    }
//    return \App\Models\Invoice\InvoiceAddressProduct::find(22);
//    dispatch(new \App\Jobs\Cart\Invoice\GenerateXmlJob('16862168346481a082ecf95'));
//    dispatch(new \App\Jobs\Cart\Invoice\GenerateXmlJob('16854381886475beecd0197'));
    return 0;
    $p = new \App\Helper\InvoiceXml\ProductXml();
    $x = new SimpleXMLElement($p->xmlObject());
    dd($p->generate($x));
//    Product::where('id', 58)->update(['is_active' => 0]);
    $product = Product::find(194);
    $optionValues = Product::fetchOptionValuesWithOutTitle($product);
    return \App\Helper\json\JsonD::json_decode($optionValues[0]['option_values'], true);
    return $optionValues;
    $optionValues = Product::fetchOptionValuesWithOutTitle($product);
    $optionValuesArr = [];
    $optionValuePrice = [];
    $option = [];
    $noSelectOptionCount = 0;
    ///group options
    foreach ($optionValues as $index => $optionValue) {
        $combinations = json_decode($optionValue['option_values']);
//        return $combinations;
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
//    return $optionValuePrice;
//    return $noSelectOptionCount;
//    return $optionValuesArr;

    ////generate all the combination
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
            foreach (new \App\Helper\Combination\Combinations($indexes, $len) as $substring) {
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

    ////update database with new combination
    $removedCombinations = App\Models\Product\Combination\Combination::where('product_id', 58)
        ->pluck('id', 'combination')->toArray();
    if ($groupCount == $noSelectOptionCount) {
        if (isset($removedCombinations[''])) {
            unset($removedCombinations['']);
        } else {
            App\Models\Product\Combination\Combination::create(['product_id' => $this->productId, 'combination' => '']);
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
            create(['product_id' => 58, 'combination' => $combStr]);
            $p2P1Combs = [];
            foreach ($combValues as $val) {
                if ($val !== "") {
                    $p2P1Combs[] = [
                        'combination_id' => $combination['id'],
                        'option_value_id' => $val
                    ];
                }
            }
            \Illuminate\Support\Facades\DB::table('combination_option_value')->insert($p2P1Combs);
        }
        $combination->update(['price' => $price, 'real_price' => $price]);

    }
    if (count($removedCombinations)) {
        $removedCombId = array_values($removedCombinations);
        \App\Models\Product\Combination\Combination::whereIn('id', $removedCombId)->delete();
        \Illuminate\Support\Facades\DB::table('combination_option_value')
            ->whereIn('combination_id', $removedCombId)->delete();
//        \App\Models\Product\Combination\CombinationImage::whereIn('combination_id', $removedCombId)
//            ->update(['combination_id' => null]);
        ///remove files from server
//        Pt2CombinationPt1Combination::whereIn('pt2_combination_id', $removedCombId)->delete();
//        Cart::whereIn('combination_id', $removedCombId)->delete();
    }

    return $freshCombs;
//    return count($freshCombs);
//    $x = [];
//    foreach(new \App\Helper\Combination\Combinations([1,2,3,4],3) as $substring){
//        $x[] =$substring;
//    }

//    return $x;
//    $nCk  = Combinatorics::combinations(4,2);

//    return $nCk;
    $combination = [];
//    function combinations(array $myArray, $choose) {
//        global $result, $combination;
//
//        $n = count($myArray);
//
//        function inner ($start, $choose_, $arr, $n) {
//            global $result, $combination;
//
//            if ($choose_ == 0) array_push($result,$combination);
//            else for ($i = $start; $i <= $n - $choose_; ++$i) {
//                array_push($combination, $arr[$i]);
//                inner($i + 1, $choose_ - 1, $arr, $n);
//                array_pop($combination);
//            }
//        }
//        inner(0, $choose, $myArray, $n);
//        return $result;
//    }
//    return combinations([1,2,3,4],2);
//    $len = 1;
//    $com = [];
//    for ($x = 1; $x < 4; $x++) {
//        for ($y = $x + 1; $y + $len < 4; $y = $y + $len) {
//
//        }
//    }
//    return 0;
    $a = [
        "10/17",
        "2/11",
        "3/5"
    ];
//    BubbleSort::sort($a);
//    return $a;
    $pt2Comb = Product::fetchPt2Pt1CombinationsGroup(11);
    $pt2ComArr = [];
    $categories = [];
    $noSelectOptionCount = 0;
    foreach ($pt2Comb as $index => $group) {
        $combinations = json_decode($group['combinations']);
        $pt2ComArr[$index] = array_map(function ($a) use ($group) {
            return $group['category_id'] . '/' . $a;
        }, $combinations);
        $categories[$index] = $group['category_id'];
        if ($group['has_no_select'] == 1) {
            $noSelectOptionCount++;
        }
    }
//    $pt2ComArr = [
//        [
//            "10/7",
//            "10/17",
//            "10/19",
//
//        ],
//        [
//            "3/5",
//            "3/4",
//            "3/12",
//        ],
//        [
//            "2/11",
//            "2/10",
//        ],
//    ];
//    $pt2ComArr = [
//        [
//            "10/7",
//            "10/17",
//            "10/19",
//            "10/20",
//            "10/21",
//            "10/22",
//        ],
//        [
//            "8/5",
//            "8/4",
//            "8/12",
//        ],
//        [
//            "7/11",
//            "7/10",
//        ],
//        [
//            "6/14",
//            "6/15",
//            "6/16",
//        ],
//        [
//            "9/7",
//            "9/8",
//            "9/9",
//        ]
//    ];
//return $pt2ComArr;
    $groupCount = Count($pt2ComArr);
    $freshCombs = [];
    for ($i = 0; $i < $groupCount; $i++) {
        $based = [];
        $based[] = $pt2ComArr[$i];
        $result = CombinationGenerator::generate($based);
        foreach ($result as $index => $r) {
            BubbleSort::sort($result[$index]);
        }
        $freshCombs = array_merge($freshCombs, $result);
        $indexes = range($i + 1, $groupCount - 1);

        for ($len = 1; $len < ($groupCount - $i); $len++) {
            $indexCombs = [];
            foreach (new \App\Helper\Combination\Combinations($indexes, $len) as $substring) {
                $indexCombs[] = $substring;
            }
            foreach ($indexCombs as $indexComb) {
                $groupSlice = [];
                $groupSlice[] = $based[0];
                foreach ($indexComb as $inc) {
                    $groupSlice[] = $pt2ComArr[$inc];
                }
                $result = CombinationGenerator::generate($groupSlice);
                foreach ($result as $index => $r) {
                    BubbleSort::sort($result[$index]);
                }
                $freshCombs = array_merge($freshCombs, $result);
            }
        }
    }
//    return $pt2Comb;
//    return $pt2ComArr;
//    return $categories;
//    return $noSelectOptionCount;
//    return $groupCount;
    return $freshCombs;
    return count($freshCombs);
});


