<?php

namespace App\Http\Controllers\Api\V1\User\Site\MiddlePage;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Models\Menu\Menu;
use App\Models\Menu\MenuTranslation;
use App\Models\Product\ProductTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MiddlePageController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'product', LanguageHelper::getCacheDefaultLang());
    }

    public function products(Request $request, $menu)
    {
        try {
            // $createTableSqlString = "alter table holidays add column title varchar(255) default null;";
            // DB::statement($createTableSqlString);
            $menu = MenuTranslation::where('slug', $menu)->firstOrFail()->menu_info;
            $language = LanguageHelper::getAppLanguage($request);
            $products = $menu->
            productsTranslation()->with([
                'product_info:id,price,cover_image,reorder,default_combination_id,default_menu_id',
                'product_info.files' => function ($query) {
                    $query->select('id','product_id','path','type','arrange')->orderBy('arrange', 'DESC');
                },
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
            })
            ->orderBy(isset($request['order_by_title']) ?
                ($request['order_by_title'] == 'price' ? 'product_info.price' : $request['order_by_title']) : 'id'
                , isset($request['order_by']) ? $request['order_by'] : 'DESC')
            ->paginate(QueryHelper::perPage($request));
            
            foreach($products as $product) {
                $productid = @$product->product_id;
                $category = @$product->pivot->menu_id;
                $data = DB::select("SELECT * FROM product_sort_category WHERE product_id = ? AND sort_category_id = ?",[$productid, $category]);
                $product['sort'] = (!empty($data)) ? (int)$data[0]->arrange : 0;
            }

            return Response::response200([], $products);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function slugs(Request $request, $menu)
    {
        try {
            $menu = MenuTranslation::where('slug', $menu)->firstOrFail()->menu_info;
            $language = LanguageHelper::getAppLanguage($request);
            $products = $menu->
            productsTranslation()->where('language', $language)
            ->paginate(QueryHelper::perPage($request));
            return Response::response200([], $products);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
