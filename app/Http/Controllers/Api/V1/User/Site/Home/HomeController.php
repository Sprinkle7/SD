<?php

namespace App\Http\Controllers\Api\V1\User\Site\Home;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\Setting\ProductionDelay;
use App\Models\Tax\Tax;
// use App\Helper\Setting\Tax;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Models\Home\Home;
use App\Models\Download\Download;
use App\Models\Product\ProductTranslation;
use App\Models\ShipingInfo\ShippingInfoTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'home', LanguageHelper::getCacheDefaultLang());
    }

    public function homeInfo()
    {
       try {
            $language = LanguageHelper::getAppLanguage(\request());
            $home = Home::with([
                'home_info' => function ($query) use ($language) {
                    $query->select('id', 'home_id', 'description', 'language')->where('language', $language);
                },
                'sliderImage' => function ($query) use ($language) {
                    $query->select('id', 'slider_id', 'path', 'mobile_path', 'link', 'language', 'sorting')->where('language', $language)->whereNotNull('path');
                },
                'sections.section_info' => function ($query) use ($language) {
                    $query->select('h_section_id', 'title', 'language')->where('language', $language);
                },
                'sections.productT' => function ($query) use ($language) {
                    $query->where('language', $language)->OrderBy('arrange','ASC');
                },
                'sections.productT.product_info' => function ($q) {
                    $q->select(['id', 'cover_image', 'default_combination_id', 'default_menu_id', 'price']);
                },
                'sections.productT.product_info.workingDay.durationTranslation' => function ($query) use ($language) {
                    $query->where('language', $language);
                },
                'sections.productT.product_info.defaultCombination:id,real_price,additional_price',
                'sections.productT.product_info.defaultMenu' => function ($query) {
                    $query->select('id', 'level', 'parent_id')->where('is_active', 1);
                },
                'sections.productT.product_info.defaultMenu.menuT' => function ($query) use ($language) {
                    $query->select('id', 'menu_id', 'title', 'language')->where('language', $language);
                },
                'sections.productT.product_info.defaultMenu.parent:id,level,parent_id',
                'sections.productT.product_info.defaultMenu.parent.menuT' => function ($query) use ($language) {
                    $query->select('id', 'menu_id', 'title', 'language')->where('language', $language);
                },
            ])->where('is_active', 1)->firstOrFail()->toArray();
            
            ////fetch the latest products
            foreach ($home['sections'] as $index => $section) {
                if ($section['type'] === 'latest') {
                    $products = ProductTranslation::with(['product_info' => function ($q) {
                        $q->select(['id', 'cover_image','is_active', 'default_combination_id', 'default_menu_id', 'price']);
                    },
                        'product_info.defaultCombination:id,real_price,additional_price',
                        'product_info.defaultMenu' => function ($query) {
                            $query->select('id', 'level', 'parent_id')->where('is_active', 1);
                        },
                        'product_info.defaultMenu.menuT' => function ($query) use ($language) {
                            $query->select('id', 'menu_id', 'title', 'language')->where('language', $language);
                        },
                        'product_info.defaultMenu.parent:id,level,parent_id',
                        'product_info.defaultMenu.parent.menuT' => function ($query) use ($language) {
                            $query->select('id', 'menu_id', 'title', 'language')->where('language', $language);
                        },
                    ])->where('language', $language)
                        ->whereHas('product_info',function ($query) {
                            $query->where('is_active', 1);
                        })
                        ->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                        ->limit(10)->get()->toArray();
                    ////where is active condition should be aded
                    // $info['shipping_info'] = ShippingInfoTranslation::where('language', $language)->first();

                    $home['sections'][$index]['product_t'] = $products;
                }
            }
            return Response::response200([], $home);
       } catch (ModelNotFoundException $exception) {
           return Response::error404($this->systemMessage->error404());
       } catch (\Exception $exception) {
           return Response::error500($this->systemMessage->error500());
       }
    }

    public function fetchTax()
    {
        try {
            $tax = Tax::where('id', '=', 0)->first();
            return Response::response200([], ['tax' => $tax->tax]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetchWorkingDelay()
    {
        try {
            $delay = ProductionDelay::fetch();
            return Response::response200([], $delay);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function download()
    {
        try {
            $downloads = DB::table('downloads')
            ->join('download_files', 'downloads.id', '=', 'download_files.download_id')
            ->select('downloads.*', 'download_files.image as file_name download', 'download_files.title as file_title')
            ->get();
            return Response::response200('Downloads fetched successfully.', $downloads);
        } catch (\Exception $exception) {
           return Response::error500($this->systemMessage->error500());
        }
    }
}
