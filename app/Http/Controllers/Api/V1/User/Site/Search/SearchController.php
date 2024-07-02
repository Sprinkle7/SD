<?php

namespace App\Http\Controllers\Api\V1\User\Site\Search;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\Models\Menu\MenuSystemMessage;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Models\Menu\Menu;
use App\Models\Menu\MenuTranslation;
use App\Models\Product\ProductTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'product', LanguageHelper::getCacheDefaultLang());
    }

    public function searchProducts(Request $request)
    {
        try {
            $language = LanguageHelper::getAppLanguage($request);

            $query = ProductTranslation::query()->with([
                'product_info:id,price,cover_image,default_combination_id,default_menu_id',
                'product_info.workingDay' => function ($query) {
                    $query->orderBy('price', 'DESC');
                },
                'product_info.workingDay.durationTranslation' => function ($query) use ($language) {
                    $query->where('language', $language);
                },
                'product_info.defaultCombination:id,price,additional_price',
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
            ])
                ->where('language', $language)
                ->whereHas('product_info', function ($query) {
                    $query->where('is_active', 1);
                });

            if (isset($request['title'])) {
                $query->where('title', 'like', '%' . $request['title'] . '%');
            }

            $products = tap($query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(QueryHelper::perPage($request)))->map(function ($p) {
                    $p->product_info->working_day = $p->product_info->workingDay->take(1);
                    unset($p->product_info->workingDay);
                    return $p;
                });
            $menus = Menu::select(['id', 'level', 'arrange', 'parent_id'])->with([
                'menuT' => function ($query) use ($language) {
                    $query->select('id', 'menu_id', 'title', 'language')->where('language', $language);
                },
                'parent:id,level,arrange,parent_id',
                'parent.menuT' => function ($query) use ($language) {
                    $query->select('id', 'menu_id', 'title', 'language')->where('language', $language);
                }
            ])->whereHas('menuT', function ($q) use ($request, $language) {
                $q->where('language', $language)->where('title', 'like', '%' . $request['title'] . '%');
            })->where('is_active', 1)->limit(10)->get();
            return Response::response200([], ['products' => $products, 'menu' => $menus]);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
