<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Product;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\Models\Product\ProductSystemMessage;
use App\Helper\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Dashboard\Product\Typ2\AddTranslationPt2Request;
use App\Http\Requests\Api\V1\Admin\Dashboard\Product\Typ2\CreatePt2Request;
use App\Http\Requests\Api\V1\Admin\Dashboard\Product\Typ2\UpdatePt2Request;
use App\Jobs\Product\RemoveDeactivatedProductFromCartJob;
use App\Models\Discount\Discount;
use App\Models\Product\Combination\Combination;
use App\Models\Product\Combination\CombinationImage;
use App\Models\Product\Pivot\DurationProduct;
use App\Models\Product\Product;
use App\Models\Menu\SortCategory;
use App\Models\Product\ProductFile;
use App\Models\Product\ProductTranslation;
use App\Models\Service\ServiceValue;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ProductController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new ProductSystemMessage(LanguageHelper::getAppLanguage(\request()),
            'product', LanguageHelper::getCacheDefaultLang());
    }

    public function duplicate(Request $request, $productId)
    {
        try {
            $product = DB::select("SELECT * FROM products WHERE id = ?", [$productId]);
            if (!empty($product)) {
                $pro = $product[0];
                
                 $id = DB::table('products')->insertGetId(array(
                    'code' => $pro->code.'_copy', 
                    'price' => $pro->price, 
                    'reorder' => $pro->reorder, 
                    'cover_image' => $pro->cover_image, 
                    'data_sheet_pdf' => $pro->data_sheet_pdf, 
                    'assembly_pdf' => $pro->assembly_pdf, 
                    'zip' => $pro->zip, 
                    'is_active' => $pro->is_active, 
                    'portfolio_id' => $pro->portfolio_id, 
                    'default_combination_id' => $pro->default_combination_id, 
                    'default_menu_id' => $pro->default_menu_id
                ));

                $duration_product = DB::select("SELECT * FROM duration_product WHERE product_id = ?",[$productId]);

                if(!empty($duration_product)) {
                    foreach ($duration_product as $value) {
                        DB::table('duration_product')->insertGetId(array(
                            'product_id' => $id, 
                            'price' => $value->price, 
                            'duration_id' => $value->duration_id, 
                            'default_value' => $value->default_value
                        ));
                    }
                }

                $excluded_services = DB::select("SELECT * FROM excluded_services WHERE product_id = ?",[$productId]);

                if(!empty($excluded_services)) {
                    foreach ($excluded_services as $value) {
                        DB::table('excluded_services')->insertGetId(array(
                            'product_id' => $id, 
                            'exclude' => $value->exclude, 
                            'from' => $value->from
                        ));
                    }
                }

                $option_product = DB::select("SELECT * FROM option_product WHERE product_id = ?",[$productId]);
                if(!empty($option_product)) {
                    foreach ($option_product as $value) {
                        DB::table('option_product')->insert(array(
                            'product_id' => $id, 
                            'option_id' => $value->option_id, 
                            'has_no_select' => $value->has_no_select, 
                            'arrange' => $value->arrange
                        ));
                    }
                }

                $option_product_translation = DB::select("SELECT * FROM option_product_translation WHERE product_id = ?",[$productId]);
                if(!empty($option_product_translation)) {
                    foreach ($option_product_translation as $value) {
                        DB::table('option_product_translation')->insert(array(
                            'product_id' => $id, 
                            'option_id' => $value->option_id, 
                            'title' => $value->title, 
                            'language' => $value->language
                        ));
                    }
                }

                $product_files = DB::select("SELECT * FROM product_files WHERE product_id = ?",[$productId]);
                if(!empty($product_files)) {
                    foreach ($product_files as $value) {
                        DB::table('product_files')->insert(array(
                            'product_id' => $id, 
                            'path' => $value->path, 
                            'type' => $value->type, 
                            'arrange' => $value->arrange
                        ));
                    }
                }

                $product_service = DB::select("SELECT * FROM product_service WHERE product_id = ?",[$productId]);
                if(!empty($product_service)) {
                    foreach ($product_service as $value) {
                        DB::table('product_service')->insert(array(
                            'product_id' => $id, 
                            'service_id' => $value->service_id, 
                            'has_no_select' => $value->has_no_select
                        ));
                    }
                }

                $product_sort_category = DB::select("SELECT * FROM product_sort_category WHERE product_id = ?",[$productId]);
                if(!empty($product_sort_category)) {
                    foreach ($product_sort_category as $value) {
                        DB::table('product_sort_category')->insert(array(
                            'product_id' => $id, 
                            'sort_category_id' => $value->sort_category_id, 
                            'arrange' => $value->arrange
                        ));
                    }
                }

                $product_technical_info = DB::select("SELECT * FROM product_technical_info WHERE product_id = ?",[$productId]);
                if(!empty($product_technical_info)) {
                    foreach ($product_technical_info as $value) {
                        DB::table('product_technical_info')->insert(array(
                            'product_id' => $id, 
                            'technical_info_id' => $value->technical_info_id, 
                            'arrange' => $value->arrange
                        ));
                    }
                }

                $product_translations = DB::select("SELECT * FROM product_translations WHERE product_id = ?",[$productId]);
                if(!empty($product_translations)) {
                    foreach ($product_translations as $value) {
                        DB::table('product_translations')->insert(array(
                            'product_id' => $id, 
                            'title' => $value->title.'_copy', 
                            'slug' => $value->slug.'-copy',
                            'benefit_desc' => $value->benefit_desc, 
                            'item_desc' => $value->item_desc, 
                            'feature_desc' => $value->feature_desc, 
                            'language' => $value->language
                        ));
                    }
                }

                $option_value_product = DB::select("SELECT * FROM option_value_product WHERE product_id = ?",[$productId]);
                if(!empty($option_value_product)) {
                    foreach ($option_value_product as $value) {
                        DB::table('option_value_product')->insert(array(
                            'product_id' => $id, 
                            'option_id' => $value->option_id, 
                            'option_value_id' => $value->option_value_id, 
                            'price' => $value->price, 
                            'stock' => $value->stock, 
                            'arrange' => $value->arrange
                        ));
                    }
                }

                $menu_product = DB::select("SELECT * FROM menu_product WHERE product_id = ?",[$productId]);
                if(!empty($menu_product)) {
                    foreach ($menu_product as $value) {
                        DB::table('menu_product')->insert(array(
                            'menu_id' => $value->menu_id,
                            'product_id' => $id,
                        ));
                    }
                }

                $excluded_option_values = DB::select("SELECT * FROM excluded_option_values WHERE product_id = ?",[$productId]);
                if(!empty($excluded_option_values)) {
                    foreach ($excluded_option_values as $value) {
                        DB::table('excluded_option_values')->insert(array(
                            'product_id' => $id, 
                            'exclude' => $value->exclude, 
                            'from' => $value->from
                        ));
                    }
                }

            }
            return Response::response200([$this->systemMessage->duplicate()], $product);
        } catch (ModelNotFoundException $exception) {
            print_r($exception);
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function create(CreatePt2Request $request)
    {
        try {
            if (isset($request['cover_image'])) {
                Uploader::moveFile($request['cover_image'], 'image', 'temp', 'product');
            }

            if (isset($request['data_sheet_pdf'])) {
                Uploader::moveFile($request['data_sheet_pdf'], 'file', 'temp', 'product/datasheet');
            }
            if (isset($request['assembly_pdf'])) {
                Uploader::moveFile($request['assembly_pdf'], 'file', 'temp', 'product/assembly');
            }
            if (isset($request['zip'])) {
                Uploader::moveFile($request['zip'], 'file', 'temp', 'product/zip');
            }

            $productCollection = Product::generateProductCollection($request);
            unset($productCollection['reorder']);

            $product = Product::create($productCollection);
            if (isset($request['video'])) {
                $ids = [];
                foreach ($request['video'] as $video) {
                    Uploader::moveFile($video['path'], 'video', 'temp', 'product');
                    $ids[] = $video['id'];
                    ProductFile::whereIn('id', $ids)->update(['product_id' => $product['id'], 'arrange' => $product['arrange']]);
                }
            }
            $productTrans = ProductTranslation::generateProductCollection($request);
            $productTrans['product_id'] = $product['id'];

            $productTrans = ProductTranslation::create($productTrans);

            $productTrans['product_info'] = $product;

            return Response::response200([$this->systemMessage->create()], $productTrans);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function addTranslation(AddTranslationPt2Request $request, $productId)
    {
        try {
            Product::findOrFail($productId);

            $productTrans = ProductTranslation::where('product_id', $productId)
                ->where('language', $request['language'])->first();
            $productTransCollection = ProductTranslation::generateProductCollection($request);

            if (is_null($productTrans)) {
                $productTransCollection['product_id'] = $productId;
                ProductTranslation::create($productTransCollection);
            } else {
                throw new ValidationException($this->systemMessage->duplicateTranslation());
            }
            return Response::response200([$this->systemMessage->addTranslation()]);
        } catch (ValidationException $exception) {
            return Response::error400($exception->getMessage());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function update(UpdatePt2Request $request, $productId, $language)
    {
        try {
            $product = Product::findOrFail($productId);
            $productTrans = ProductTranslation::where('product_id', $productId)
                ->where('language', $language)->first();
            if (isset($request['cover_image']) && $product['cover_image'] != $request['cover_image']) {
                Uploader::moveFile($request['cover_image'], 'image', 'temp', 'product');
            }
            if (isset($request['video']) && !is_null($request['video'])) {
                $ids = [];
                foreach ($request['video'] as $video) {
                    if (Uploader::fileExistInStorage($video['path'], 'video', 'temp')) {
                        Uploader::moveFile($video['path'], 'video', 'temp', 'product');
                    }
                    $ids[] = $video['id'];
                    ProductFile::whereIn('id', $ids)->update(['product_id' => $product['id'], 'arrange' => $product['arrange']]);
                }
            }

            if (isset($request['data_sheet_pdf']) && $product['data_sheet_pdf'] !== $request['data_sheet_pdf']) {
                Uploader::moveFile($request['data_sheet_pdf'], 'file', 'temp', 'product/datasheet');
            }
            if (isset($request['assembly_pdf']) && $product['assembly_pdf'] !== $request['assembly_pdf']) {
                Uploader::moveFile($request['assembly_pdf'], 'file', 'temp', 'product/assembly');
            }
            if (isset($request['zip']) && $product['zip'] !== $request['zip']) {
                Uploader::moveFile($request['zip'], 'file', 'temp', 'product/zip');
            }

            $productCollection = Product::generateProductCollection($request);
            unset($productCollection['reorder']);
            unset($productCollection['type']);
            $product->update($productCollection);
            if (!is_null($productTrans)) {
                $productTransCol = ProductTranslation::generateProductCollection($request);
                $productTrans->update($productTransCol);
            }

            return Response::response200([$this->systemMessage->update()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404();
        } catch (\Exception $exception) {
        //            return Response::error500($exception->getMessage());  
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($productId)
    {
        try {
            $product = Product::with('files')->findOrFail($productId);
            $product->technicalInfos()->sync([]);
            ProductTranslation::where('product_id', $productId)->delete();
            DurationProduct::where('product_id', $productId)->delete();
            DB::table('option_product')->where('product_id', $productId)->delete();
            DB::table('option_value_product')->where('product_id', $productId)->delete();
            $combinations = Combination::select('id')->where('product_id', $productId)->pluck('id');
            if (count($combinations) > 0) {
                CombinationImage::whereIn('combination_id', $combinations)->update(['combination_id' => null]);
                DB::table('combination_option_value')->whereIn('combination_id', $combinations)->delete();
            }
            Combination::where('product_id', $productId)->delete();

            // if (!is_null($product['cover_image']))
            //     Uploader::deleteFromStorage($product['cover_image'], 'image', 'product');
            // if (!is_null($product['files']))
            //     foreach ($product['files'] as $video) {
            //         Uploader::deleteFromStorage($video['path'], 'video', 'product');
            //     }
            // if (!is_null($product['data_sheet_pdf']))
            //     Uploader::deleteFromStorage($product['data_sheet_pdf'], 'file', 'product/datasheet');
            // if (!is_null($product['assembly_pdf']))
            //     Uploader::deleteFromStorage($product['assembly_pdf'], 'file', 'product/assembly');
            // if (!is_null($product['zip']))
            //     Uploader::deleteFromStorage($product['zip'], 'file', 'product/zip');

            ProductFile::where('product_id', $product['id'])->delete();
            $product->delete();

            //delete related files filed should be a job
            //delete from cart
            dispatch(new RemoveDeactivatedProductFromCartJob($productId));
            return Response::response200([$this->systemMessage->delete()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($productId, $language)
    {
        try {
            $product = ProductTranslation::with(['product_info' => function ($query) {
                $query->select(['id', 'code', 'price', 'reorder', 'cover_image', 'video',
                    'data_sheet_pdf', 'assembly_pdf', 'zip', 'is_active', 'default_menu_id']);
            }, 'product_info.files'])->where('product_id', $productId)->
            where('language', $language)->firstOrFail();
            return Response::response200([], $product);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    // public function search(Request $request)
    // {
    //     try {
    //         $query = ProductTranslation::query()
    //             ->select('title', 'language', 'product_id')
    //             ->with('product_info');
    //         if (isset($request['language'])) {
    //             $query->where('language', $request['language']);
    //         }

    //         if (isset($request['title'])) {
    //             $query->where('title', 'like', '%' . $request['title'] . '%');
    //         }

    //         if (isset($request['code'])) {
    //             $query->where('code', 'like', '%' . $request['code'] . '%');
    //         }

    //         if (isset($request['product_id'])) {
    //             $query->where('product_id', $request['product_id']);
    //         }

    //         if (isset($request['type'])) {
    //             $query->whereHas('product_info', function ($query) use ($request) {
    //                 $query->where('type', $request['type']);
    //             });
    //         }

    //         if (isset($request['is_active'])) {
    //             $query->whereHas('product_info', function ($query) use ($request) {
    //                 $query->where('is_active', $request['is_active']);
    //             });
    //         }

    //         $categories = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
    //             ->paginate(QueryHelper::perPage($request));

    //         return Response::response200([], $categories);
    //     } catch (\Exception $exception) {
    //         return Response::error500($this->systemMessage->error500());
    //     }
    // }

    public function search(Request $request)
    {
        // try {
            $query = ProductTranslation::query()
                ->select('title', 'language', 'product_id')
                ->with('product_info');

            if (isset($request['language']) && !empty($request['language'])) {
                $query->where('language', $request['language']);
            }

            if (isset($request['title']) && !empty($request['title'])) {
                $query->where('title', 'like', '%' . $request['title'] . '%');
            }

            if (isset($request['code']) && !empty($request['code'])) {
                $query->whereHas('product', function ($query) use ($request) {
                    $query->where('code', $request['code']);
                });
            }
            
            // if (isset($request['product_id'])) {
            //     $query->where('product_id', $request['product_id']);
            // }

            if (isset($request['type']) && !empty($request['type'])) {
                $query->whereHas('product_info', function ($query) use ($request) {
                    $query->where('type', $request['type']);
                });
            }

            if (isset($request['is_active']) && !empty($request['is_active'])) {
                $query->whereHas('product_info', function ($query) use ($request) {
                    $query->where('is_active', $request['is_active']);
                });
            }
            
           
            $categories = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(QueryHelper::perPage($request));

            return Response::response200([], $categories);
        // } catch (\Exception $exception) {
        //     return Response::error500($this->systemMessage->error500());
        // }
    }


    public function activate($productId)
    {
        try {
            $product = Product::with(['menus','defaultCombination'])->findOrFail($productId);

            if (count($product['menus']) == 0 || is_null($product['defaultCombination']) ||
                $product['default_combination_id'] == 0) {
                throw new BadRequestException($this->systemMessage->menuRequired());
            }
            $product->update(['is_active' => 1]);
            return Response::response200($this->systemMessage->activate());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (BadRequestException $exception) {
            return Response::error400($exception->getMessage());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function deactivate($productId)
    {
        try {
            $product = Product::select('id')->findOrFail($productId);
            $product->update(['is_active' => 0]);

            /// remove product form cart
            dispatch(new RemoveDeactivatedProductFromCartJob($productId));
            return Response::response200($this->systemMessage->deactivate());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function attachTechnicalInfo(Request $request, $productId)
    {
        try {
            $product = Product::findOrFail($productId);

            $technical_infos = [];
            foreach ($request['technical_infos'] as $technical_info) {
                $technical_infos[$technical_info['id']] = ['arrange' => $technical_info['arrange']];
            }
            $product->updateTechnicalInfos()->sync($technical_infos);
            return Response::response200($this->systemMessage->attachTechnicalInfo());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetchTechnicalInfo($productId)
    {
        try {
            $product = Product::findOrFail($productId);
            $techInfo = $product->technicalInfos()
                ->select('technical_info_translations.id', 'technical_info_translations.technical_info_id', 'title', 'language')
                ->where('language', LanguageHelper::getAppLanguage(\request()))->get();
            return Response::response200([], $techInfo);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function attachPortfolio(Request $request, $productId)
    {
        try {
            $product = Product::findOrFail($productId);
            $product->update(['portfolio_id' => $request['portfolio_id']]);
            return Response::response200($this->systemMessage->attachPortfolio());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetchPortfolio($productId)
    {
        try {
            $product = Product::findOrFail($productId);
            $portfolio = $product->portfolio()->first();
            return Response::response200([], $portfolio);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function attachDiscount(Request $request, $productId)
    {
        try {
            $discounts = Discount::where('product_id', $productId)->pluck('id', 'id');
            foreach ($request['discounts'] as $discount) {
                if (isset($discount['id'])) {
                    $d = Discount::findOrFail($discount['id']);
                    $d->update(['quantity' => $discount['quantity'], 'percent' => $discount['percent']]);
                } else {
                    Discount::create(['product_id' => $productId,
                        'quantity' => $discount['quantity'], 'percent' => $discount['percent']]);
                }
            }

            return Response::response200([$this->systemMessage->attachDiscount()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function deleteDiscount($discountId)
    {
        try {
            $discount = Discount::findOrFail($discountId);
            $discount->delete();
            return Response::response200([$this->systemMessage->deleteDiscount()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function allDiscounts($productId)
    {
        try {
            $discounts = Discount::where('product_id', $productId)->orderBy('quantity')->get();
            return Response::response200([], $discounts);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function attachMenu(Request $request, Product $product)
    {
        try {
            $product->menus()->sync($request['menus']);
            $product->update(['default_menu_id' => $request['default']]);
            $product->category()->sync($request['menus']);
            $product->update(['category' => $request['default']]);
            return Response::response200([], $this->systemMessage->update());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function getMenu(Request $request, Product $product)
    {
        try {
            $language = LanguageHelper::getAppLanguage($request);
            $menus = $product->menus()->select('id')->with(['menuT' => function ($query) use ($language) {
                $query->select('id', 'menu_id', 'title')->where('language', $language);
            }])->get();
            return Response::response200([], $menus);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function attachService(Request $request, Product $product)
    {
        try {
            ///update exclude service value table ,remove service values form excluded services
            $services = $product->services()->with('serviceValue')
                ->get()->pluck('serviceValue.*.id');
            $serviceValues = [];
            foreach ($services as $values) {
                $serviceValues = array_merge($serviceValues, $values);
            }
            $serviceValues = array_combine($serviceValues, $serviceValues);
            $excludeds = DB::table('excluded_services')
                ->select('exclude', 'from')->where('product_id', $product['id'])->get();
            $removedExcluded = [];
            foreach ($excludeds as $excluded) {
                if (!isset($serviceValues[$excluded->exclude])) {
                    $removedExcluded[$excluded->exclude] = $excluded->exclude;
                }
                if (!isset($serviceValues[$excluded->from])) {
                    $removedExcluded[$excluded->from] = $excluded->from;
                }
            }

            $product->services()->sync($request['services']);
            if (!empty($removedExcluded)) {
                DB::table('excluded_services')
                    ->where('product_id', $product['id'])->where(function ($q) use ($removedExcluded) {
                        $q->where('exclude', $removedExcluded)->orWhere('from', $removedExcluded);
                    })->delete();
            }
            return Response::response200([], $this->systemMessage->update());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function getService(Request $request, Product $product)
    {
        try {
            $language = LanguageHelper::getAppLanguage($request);

            $services = $product->services()->select('id')->with(['serviceTranlation' => function ($query) use ($language) {
                $query->select('id', 'service_id', 'title')->where('language', $language);
            }])->get();
            return Response::response200([], $services);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function excludeServices(Request $request, Product $product)
    {
        try {
            $values = [];
            foreach ($request['serviceValues'] as $option_value) {
                $values[] = ['product_id' => $product['id'],
                    'exclude' => $option_value['exclude'], 'from' => $option_value['from']];
            }
            DB::table('excluded_services')->where('product_id', $product['id'])->delete();
            DB::table('excluded_services')->insert($values);
            return Response::response200([$this->systemMessage->excludeServiceValue()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetchExcludes(Request $request, Product $product)
    {
        try {
            return Response::response200([],
                DB::table('excluded_services')
                    ->select('exclude', 'from')->where('product_id', $product['id'])->get());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function updateWorkingDay(Request $request, $productId)
    {
        try {
            Product::findOrFail($productId);
            DurationProduct::where('product_id', $productId)->delete();
            $durations = [];
            foreach ($request['durations'] as $du) {
                $durations[] = [
                    'duration_id' => $du['durationId'],
                    'product_id' => $productId,
                    'price' => isset($du['price']) ? $du['price'] : 0,
                    'default_value' => isset($du['default']) ? $du['default'] : 0
                ];
            }
            DurationProduct::insert($durations);
            return Response::response200($this->systemMessage->updateWorkingDay());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function getWorkingDay(Request $request, $productId)
    {
        try {
            Product::findOrFail($productId);
            $workingDay = DurationProduct::where('product_id', $productId)->get();
            return Response::response200([], $workingDay);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

}
