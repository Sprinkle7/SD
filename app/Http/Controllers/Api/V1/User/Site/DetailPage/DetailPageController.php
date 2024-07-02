<?php

namespace App\Http\Controllers\Api\V1\User\Site\DetailPage;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\Models\Product\Pt2SystemMessage;
use App\Http\Controllers\Controller;
use App\Models\Product\Combination\Combination;
use App\Models\Product\Product;
use App\Models\Product\ProductTranslation;
use App\Models\ShipingInfo\ShippingInfoTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailPageController extends Controller
{

    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new Pt2SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'product', LanguageHelper::getCacheDefaultLang());
    }

    public function fetchProduct($productSlug)
    {
        try {
            $prodcut = ProductTranslation::with([
                'product_info',
                'product_info.files' => function ($query) {
                    $query->select('id', 'product_id', 'path', 'type', 'arrange')->orderBy('arrange', 'DESC');
                }])
                ->where('slug', $productSlug)
                ->whereHas('product_info', function ($query) {
                    $query->where('is_active', 1);
                })->firstOrFail();
            return Response::response200([], $prodcut);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
    
    public function fetchProductOptionsID($productId)
    {
        try {
            $language = LanguageHelper::getAppLanguage(\request());
            $product = Product::where('id', $productId)->firstOrFail();
            $productOptions = Product::fetchOptionsWithValuesWithTitle($product, $language);
            $options['options'] = $productOptions;
            return Response::response200([], $options);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }


    public function fetchProductOptions($productSlug)
    {
        try {
            $language = LanguageHelper::getAppLanguage(\request());
            $product = ProductTranslation::where('slug', $productSlug)->firstOrFail()->product_info()->with([
                'services.serviceTranlation' => function ($query) use ($language) {
                    $query->where('language', $language);
                },
                'services.serviceValue.serviceValueTranslation' => function ($query) use ($language) {
                    $query->where('language', $language);
                },
                'workingDay:id,duration',
                'workingDay.durationTranslation' => function ($query) use ($language) {
                    $query->where('language', $language);
                },
                'defaultCombination:id,real_price,additional_price',
                'defaultCombination.optionValues',
                'defaultCombination.images' => function ($query) {
                    $query->orderBy('id', 'ASC');
                },
                'discounts' => function ($query) {
                    $query->select('id', 'product_id', 'quantity', 'percent')->orderBy('quantity');
                }])->where('is_active', 1)->first();
            $productOptions = Product::fetchOptionsWithValuesWithTitle($product, $language);
            $excludes = DB::table('excluded_option_values')->select('exclude', 'from')
                ->where('product_id', $product['id'])->get();
            foreach ($excludes as $exclude) {
                $options['excluded'][$exclude->exclude][] = $exclude->from;
                $options['excluded'][$exclude->from][] = $exclude->exclude;
            }
            $excludes = DB::table('excluded_services')->select('exclude', 'from')
                ->where('product_id', $product['id'])->get();
            foreach ($excludes as $exclude) {
                $options['excluded_services'][$exclude->exclude][] = $exclude->from;
                $options['excluded_services'][$exclude->from][] = $exclude->exclude;
            }
            $options['options'] = $productOptions;
            $options['services'] = $product['services'];
            $options['delivery'] = $product['workingDay'];
            $options['default_combination'] = $product['defaultCombination'];
            $options['discounts'] = $product['discounts'];

            return Response::response200([], $options);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }


    public function FetchProductComments()
    {

    }

    public function fetchProductInfos($productSlug)
    {
        try {
            $language = LanguageHelper::getAppLanguage(\request());

            $product = ProductTranslation::where('slug', $productSlug)->firstOrFail()->product_info()->with([
                'technicalInfos' => function ($query) use ($language) {
                    $query->where('language', $language);
                }, 'portfoliosImage' => function ($query) use ($language) {
                    $query->orderBy('arrange', 'DESC');
                }])->first();
            $info['technical_infos'] = $product['technicalInfos'];
            $info['portfolios'] = $product['portfoliosImage'];

            $info['shipping_info'] = ShippingInfoTranslation::where('language', $language)->first();
            return Response::response200([], $info);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetchCombinationInfo(Request $request, $productSlug)
    {
        try {
            $product = ProductTranslation::where('slug', $productSlug)->firstOrFail();
            $combination = Combination::with(['images' => function ($query) {
            }])->where('product_id', $product['product_id'])->where('is_active', 1)
                ->where('combination', $request['combination'])->first();
            return Response::response200([], $combination);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetchProductionDelay()
    {

    }
}
