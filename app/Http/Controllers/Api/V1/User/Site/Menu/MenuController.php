<?php

namespace App\Http\Controllers\Api\V1\User\Site\Menu;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\Models\Menu\MenuSystemMessage;
use App\Http\Controllers\Controller;
use App\Models\Menu\Menu;
use App\Models\Menu\MenuTranslation;
use App\Models\Product\Product;
use App\Models\Product\ProductTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Null_;

class MenuController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new MenuSystemMessage(LanguageHelper::getAppLanguage(\request()),
            'menu', LanguageHelper::getCacheDefaultLang());
    }

    public function menus()
    {
        try {
            $query = 'CALL fetchMegaMenu(\'' . LanguageHelper::getAppLanguage(\request()) . '\');';
            $menus = QueryHelper::select($query);
            $menuTree = [];
            foreach ($menus as $node) {
                $menuTree[$node['parent_id']][] = $node;
            }
            $fnBuilder = function ($siblings) use (&$fnBuilder, $menuTree) {
                foreach ($siblings as $k => $sibling) {
                    $id = $sibling['menu_id'];
                    if (isset($menuTree[$id])) {
                        $sibling['children'] = $fnBuilder($menuTree[$id]);
                    } else {
                        $sibling['children'] = null;
                    }
                    $siblings[$k] = $sibling;
                }
                return $siblings;
            };
            $mMenu = [];
            if (!empty($menuTree)) {
                $mMenu = $fnBuilder($menuTree[null]);
            }
            return Response::response200([], $mMenu);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function menuInfo($menuSlug)
    {
        try {
            $language = LanguageHelper::getAppLanguage(\request());
            $menu = MenuTranslation::with(['cover_image' => function ($query) use ($language) {
                $query->where('language', $language);
            }])->where('slug', $menuSlug)
                ->where('language', $language)->firstOrFail();
            return Response::response200([], $menu);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetchMenuChildren($menuSlug)
    {
        try {
            $parentId = MenuTranslation::where('slug', $menuSlug)
                ->where('language', LanguageHelper::getAppLanguage(\request()))->firstOrFail()->menu_id;
            $menus = MenuTranslation::with(['menu_info'])->whereHas('menu_info', function ($query) use ($parentId) {
                $query->where('parent_id', $parentId);
            })->where('language', LanguageHelper::getAppLanguage(\request()))->get();
            return Response::response200([], $menus);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function menuPath(Request $request)
    {
        try {
            $menuId = 0;
            if (!is_null($request['menu_slug'])) {
                $menuId = MenuTranslation::where('slug', $request['menu_slug'])
                    ->where('language', LanguageHelper::getAppLanguage(\request()))
                    ->whereHas('menu_info', function ($query) {
                        $query->where('is_active', 1);
                    })->firstOrFail()->menu_id;

            } else if (!is_null($request['product_slug'])) {
                $menuId = ProductTranslation::where('slug', $request['product_slug'])->first()
                    ->product_info()->first()->defaultMenu()->where('is_active', 1)->firstOrFail()->id;
            }

            $query = 'CALL nodePath(' . $menuId . ',\'' . LanguageHelper::getAppLanguage(\request()) . '\')';
            $menu = QueryHelper::select($query);
            return Response::response200([], $menu);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($exception->getMessage());
            return Response::error500($this->systemMessage->error500());
        }
    }
}
