<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Product;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\Models\Product\ProductSystemMessage;
use App\Http\Controllers\Controller;
use App\Jobs\Product\CombinationCheckerJob;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductOptionController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new ProductSystemMessage(LanguageHelper::getAppLanguage(\request()),
            'product', LanguageHelper::getCacheDefaultLang());
    }

    public function attachOption(Request $request, Product $product, $language)
    {
        try {
            $removedOptions = DB::table('option_product')->where('product_id', $product['id'])
                ->pluck('option_id', 'option_id');

            $newOptions = [];
            $newOptionsTitle = [];
            foreach ($request['options'] as $index => $option) {
                if (isset($removedOptions[$option['option_id']])) {

                    unset($removedOptions[$option['option_id']]);

                    DB::table('option_product')
                        ->where('product_id', $product['id'])
                        ->where('option_id', $option['option_id'])
                        ->update(['arrange' => $option['arrange'], 'has_no_select' => $option['has_no_select']]);

                    DB::table('option_product_translation')
                        ->updateOrInsert(
                            [
                                'product_id' => $product['id'],
                                'option_id' => $option['option_id'],
                                'language' => $language,
                            ], ['title' => $option['title']]);
                } else {
                    $newOptions[$index] = $option;
                    $newOptions[$index]['product_id'] = $product['id'];
                    $newOptionsTitle[] = [
                        'product_id' => $product['id'],
                        'option_id' => $option['option_id'],
                        'language' => $language,
                        'title' => $option['title']
                    ];
                    unset($newOptions[$index]['title']);
                }
            }

            if (count($removedOptions) > 0) {
                DB::table('option_product')
                    ->where('product_id', $product['id'])
                    ->whereIn('option_id', $removedOptions)->delete();

                DB::table('option_value_product')
                    ->where('product_id', $product['id'])
                    ->whereIn('option_id', $removedOptions)->delete();

                DB::table('option_product_translation')
                    ->where('product_id', $product['id'])
                    ->whereIn('option_id', $removedOptions)->delete();
            }

            if (count($newOptions) > 0) {
                DB::table('option_product')->insert($newOptions);
                DB::table('option_product_translation')->insert($newOptionsTitle);
            }

            dispatch(new CombinationCheckerJob($product['id']));

            return Response::response200([$this->systemMessage->optionAttached()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($exception->getMessage());
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function addOptionTranslation(Request $request, Product $product, $language)
    {
        try {
            foreach ($request['options'] as $option) {
                DB::table('option_product_translation')
                    ->updateOrInsert(
                        [
                            'product_id' => $product['id'],
                            'option_id' => $option['option_id'],
                            'language' => $language,
                        ], ['title' => $option['title']]);
            }
            return Response::response200([$this->systemMessage->optionTranslationAttached()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetchOption(Product $product, $language)
    {
        try {
            return Response::response200([], Product::fetchOptions($product, $language));
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }


    public function attachOptionValue(Request $request, Product $product)
    {
        try {
            $removedOptionValues = DB::table('option_value_product')
                ->where('product_id', $product['id'])
                ->pluck('option_value_id', 'option_value_id');
            $newOptionValues = [];
            foreach ($request['optionValues'] as $index => $optionValues) {
                if (isset($removedOptionValues[$optionValues['option_value_id']])) {
                    unset($removedOptionValues[$optionValues['option_value_id']]);
                    DB::table('option_value_product')
                        ->where('product_id', $product['id'])
                        ->where('option_value_id', $optionValues['option_value_id'])
                        ->update([
                            'arrange' => $optionValues['arrange'],
                            'price' => $optionValues['price'],
                            'stock' => $optionValues['stock']
                        ]);
                } else {
                    $newOptionValues[$index] = $optionValues;
                    $newOptionValues[$index]['product_id'] = $product['id'];

                }
            }

            if (count($removedOptionValues) > 0) {
                DB::table('option_value_product')
                    ->where('product_id', $product['id'])
                    ->whereIn('option_value_id', $removedOptionValues)->delete();
            }

            if (count($newOptionValues) > 0) {
                DB::table('option_value_product')->insert($newOptionValues);
            }

            ///run job to update combination
            dispatch(new CombinationCheckerJob($product['id']));

            return Response::response200([$this->systemMessage->optionValueAttached()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($exception->getTrace());
            return Response::error500($this->systemMessage->error500());
        }
    }


    public function fetchOptionValue(Product $product, $language)
    {
        try {

            try {
                return Response::response200([], Product::fetchOptionValues($product, $language));
            } catch (ModelNotFoundException $exception) {
                return Response::error404($this->systemMessage->error404());
            } catch (\Exception $exception) {
                return Response::error500($this->systemMessage->error500());
            }
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetchOptionWithTitle(Product $product, $language)
    {
        try {
            return Response::response200([], Product::fetchOptionsWithValuesWithTitle($product, $language));
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetchExcludes(Product $product)
    {
        try {
            return Response::response200([],
                DB::table('excluded_option_values')
                    ->select('exclude', 'from')->where('product_id', $product['id'])->get());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function excludeOptionValues(Request $request, Product $product)
    {

        try {
            $values = [];
            foreach ($request['optionValues'] as $option_value) {
                $values[] = ['product_id' => $product['id'],
                    'exclude' => $option_value['exclude'], 'from' => $option_value['from']];
            }
            DB::table('excluded_option_values')->where('product_id', $product['id'])->delete();
            DB::table('excluded_option_values')->insert($values);
            return Response::response200([$this->systemMessage->excludeServiceValue()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
