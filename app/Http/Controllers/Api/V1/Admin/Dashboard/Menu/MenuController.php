<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Menu;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\Models\Menu\MenuSystemMessage;
use App\Helper\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Dashboard\Menu\AddMenuTranslationRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Menu\AttachProductRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Menu\CreateMenuRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Menu\UpdateMenuRequest;
use App\Models\Menu\Menu;
use App\Models\Menu\SortCategory;
use App\Models\Product\Product;
use App\Models\Product\ProductTranslation;
use App\Models\Menu\MenuCoverImage;
use App\Models\Menu\MenuTranslation;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class MenuController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new MenuSystemMessage(LanguageHelper::getAppLanguage(\request()),
            'menu', LanguageHelper::getCacheDefaultLang());
    }

    private function checkParent(Request $request)
    {
        //this function check the parent menu that be chosen
        /**
         * if the difference between the levels of out menu and its parent
         * is greater than 1 it's not possible to creating or updating the menu
         * */
        if ($request['level'] > 1) {
            $parentMenu = Menu::findOrFail($request['parent_id']);
            $difference = $request['level'] - $parentMenu['level'];
            if ($difference != 1) {
                throw new ValidationException($this->systemMessage->wrongParentMenu());
            }
        }
    }

    public function create(CreateMenuRequest $request)
    {
        try {
            $this->checkParent($request);

            $menu = Menu::create(Menu::generateMenuCollection($request));
            $menuTranslation = MenuTranslation::generateMenuTransCollection($request);
            $menuTranslation['menu_id'] = $menu['id'];
            $trans = MenuTranslation::create($menuTranslation);

            if (isset($request['thumbnail_image']) && !is_null($request['thumbnail_image'])) {
                Uploader::moveFile($request['thumbnail_image'], 'image', 'temp', 'menu');
            }
            foreach ($request['cover_images'] as $image) {
                MenuCoverImage::where('id', $image['id'])
                    ->update(['menu_id' => $menu['id'], 'link' => $image['link']]);
            }

            $trans['menu_info'] = $menu;
            return Response::response200([$this->systemMessage->create()], $trans);

        } catch (ValidationException $exception) {
            return Response::error400($exception->getMessage());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function addTranslation(AddMenuTranslationRequest $request, $menuId)
    {
        try {
            $menuT = MenuTranslation::where('menu_id', $menuId)->where('language', $request['language'])->first();
            if (is_null($menuT)) {
                $menu = MenuTranslation::generateMenuTransCollection($request);
                $menu['menu_id'] = $menuId;
                MenuTranslation::create($menu);
                foreach ($request['cover_images'] as $image) {
                    MenuCoverImage::where('id', $image['id'])
                        ->update(['menu_id' => $menuId, 'link' => $image['link']]);
                }
            }
            return Response::response200($this->systemMessage->addTranslation());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function update(UpdateMenuRequest $request, $menuId, $language)
    {
        try {
            $this->checkParent($request);
            $menu = Menu::with('cover_image')->findOrFail($menuId);
            $menuTrans = MenuTranslation::where('menu_id', $menuId)
                ->where('language', $language)->firstOrFail();

            if (isset($request['thumbnail_image']) && !is_null($request['thumbnail_image']) && $menu['thumbnail_image'] != $request['thumbnail_image']) {
                Uploader::moveFile($request['thumbnail_image'], 'image', 'temp', 'menu');
            }
            foreach ($request['cover_images'] as $image) {
                MenuCoverImage::where('id', $image['id'])
                    ->update(['menu_id' => $menu['id'], 'link' => $image['link']]);
            }

            $menuCollection = Menu::generateMenuCollection($request);
            $menu->update($menuCollection);
            $menuTransCollection = MenuTranslation::generateMenuTransCollection($request);
            $menuTrans->update($menuTransCollection);

            return Response::response200($this->systemMessage->update());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (ValidationException $exception) {
            return Response::error400($exception->getMessage());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function search(Request $request)
    {
        try {
            if(isset($request['products']) && $request['products'] == "getthem") {

                $menus = DB::select('select * from product_sort_category JOIN product_translations ON product_sort_category.product_id = product_translations.product_id where product_sort_category.sort_category_id = ?', [$request['parent_id']]);
                // $query = Product::query()
                // ->select('default_menu_id', 'id','reorder')
                // ->where('default_menu_id', $request['parent_id'])
                // ->with('product_translations');

                // $menus = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                    // ->paginate(QueryHelper::perPage($request));
            } else {

                $query = MenuTranslation::query()
                    ->select('menu_id', 'title', 'language')
                    ->with('menu_info');
                
                if (isset($request['language'])) {
                    $query->where('language', $request['language']);
                }
                
                if (isset($request['title'])) {
                    $query->where('title', 'like', '%' . $request['title'] . '%');
                }
                
                if (isset($request['parent_id'])) {
                    $query->whereHas('menu_info', function ($query) use ($request) {
                        $query->where('parent_id', $request['parent_id']);
                    });
                }
                
                if (isset($request['level'])) {
                    if($request['level'] == '3') {
                        $request['level'] = 2;
                    }
                    $query->whereHas('menu_info', function ($query) use ($request) {
                        $query->where('level', $request['level']);
                    });
                }
                
                $menus = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                    ->paginate(QueryHelper::perPage($request));
            }


          
            return Response::response200([], $menus);
        } catch (\Exception $exception) {
            $errorMessage = $exception->getMessage();
            return Response::error500($errorMessage);
        }
    }

    public function delete($menuId)
    {
        try {

            $menu = Menu::with('cover_image')->where('id', $menuId)->firstOrFail();
            $count = $menu->products()->count();
            if ($count > 0) {
                throw new BadRequestException($this->systemMessage->unableToDelete());
            }
            Uploader::deleteFromStorage($menu['thumbnail_image'], 'image', 'menu');
            foreach ($menu['cover_image'] as $coverImage) {
                Uploader::deleteFromStorage($coverImage['path'], 'image', 'menu');
            }
            MenuCoverImage::where('menu_id', $menuId)->delete();
            MenuTranslation::where('menu_id', $menuId)->delete();
            $menu->products()->sync([]);
            $menu->delete();

            return Response::response200($this->systemMessage->delete());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (BadRequestException $exception) {
            return Response::error400($exception->getMessage());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());

        }
    }

    public function fetch($menuId, $language)
    {
        try {
            $menu = MenuTranslation::with(['menu_info', 'cover_image' => function ($query) use ($language) {
                $query->where('language', $language);
            }])->where('menu_id', $menuId)
                ->where('language', $language)->firstOrFail();
            return Response::response200([], $menu);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function activate($id)
    {
        try {
            $menu = Menu::findOrFail($id);
            Menu::where('parent_id', $id)->update(['is_active' => 1]);
            $menu->update(['is_active' => 1]);
            return Response::response200($this->systemMessage->activate());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function get_all(Request $request) {
        try {
            $query = DB::select('SELECT product_id,title FROM product_translations');
            return Response::response200([], $query);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function getbycategory(Request $request) {
        try {
            $id = $request['menuId'];
            $query = DB::select('SELECT product_id,title FROM product_translations JOIN products ON products.id = product_translations.product_id WHERE products.default_menu_id = ?', [$id]);
            return Response::response200([], $query);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    

    public function deactivate($id)
    {
        try {
            $menu = Menu::findOrFail($id);
            Menu::where('parent_id', $id)->update(['is_active' => 0]);
            $menu->update(['is_active' => 0]);
            return Response::response200($this->systemMessage->deactivate());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function updateArrange(Request $request)
    {
        try {
            if (isset($request['menus']['level']) && $request['menus']['level'] === 'products') {
                $menuItems = $request['menus']['menus'];
                foreach ($menuItems as $menu) {
                    DB::update(
                        'UPDATE product_sort_category SET arrange = ? WHERE sort_category_id = ? AND product_id = ?',
                        [$menu['arrange'], $menu['menu_id'], $menu['id']]
                    );
                }
            } else {
                $menuItems = $request['menus']['menus'];
                foreach ($menuItems as $menu) {
                    Menu::where('id', $menu['id'])->update(['arrange' => $menu['arrange']]);
                }
            }
            return Response::response200($this->systemMessage->update());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }


    public function attachProducts(AttachProductRequest $request, $menuId)
    {
        try {
            $menu = Menu::findorFail($menuId);
            $menu->products()->sync($request['products_id']);
            return Response::response200([$this->systemMessage->projectAttached()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetchProducts($menuId, $language)
    {
        try {
            $menu = Menu::findorFail($menuId);
            $projects = $menu->productsTranslation()->where('language', $language)->get();
            return Response::response200([], $projects);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
