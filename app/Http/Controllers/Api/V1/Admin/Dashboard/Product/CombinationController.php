<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Product;

use App\Helper\json\JsonD;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\Models\Product\Pt2SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Dashboard\Product\Typ2\UpdateCombsInfoPt2Request;
use App\Jobs\Product\RemoveDeactivatedCombinationFromCartJob;
use App\Models\Product\Combination\Combination;
use App\Models\Product\Pivot\Type2\Pt2Category;
use App\Models\Product\Pivot\Type2\Pt2Combination;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CombinationController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new Pt2SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'product', LanguageHelper::getCacheDefaultLang());
    }

    public function combinations(Request $request, Product $product, $language)
    {
        try {
            $relationCount = 0;
            $combinations = Product::fetchCombinations($request, $product, $language);
//            foreach ($combinations as $index => $combination) {
//                $combinations[$index]['option_values'] = JsonD::json_decode($combination['option_values']);
//            }
            return Response::response200([], $combinations);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($exception->getMessage());
        }
    }

    public function combinationsCount(Request $request, Product $product, $language)
    {
        try {
            $relationCount = 0;
            $count = Product::fetchCombinationCount($request, $product, $language);
            return Response::response200([], count($count));
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($exception->getMessage());
        }
    }

    public function CombinationInfo($combinationId)
    {
        try {
            $comb = Combination::with('images')->findOrFail($combinationId);
            return Response::response200([], $comb);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function updateCombinationInfo(UpdateCombsInfoPt2Request $request, Combination $combination)
    {
        try {

            $combination->update(['additional_price' => $request['additional_price']]);
            return Response::response200(['done']);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function changeCombsActivation(Combination $combination)
    {
        try {
            ///check is default
            if ($combination['is_default'] == 1 && $combination['is_active'] == 1) {
                throw new BadRequestException('This is default it not possible to deactivate it');
            }
            $combination->update(['is_active' => !$combination['is_active']]);
            if (!$combination['is_active']) {
                ///remove comb from cards
                dispatch(new RemoveDeactivatedCombinationFromCartJob($combination['id']));
            }
            return Response::response200([$this->systemMessage->updateCombActivation()]);
        } catch (BadRequestException $exception) {
            return Response::error400($exception->getMessage());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function setDefaultCombination(Product $product, Combination $combination)
    {
        try {

            if ($combination['is_default'] != 1) {
                //select default
                $product->update(['default_combination_id' => $combination['id']]);
                $oldDefault = Combination::where('product_id', $product['id'])->where('is_default', 1)->first();
                if ($oldDefault) {
                    $oldDefault->update(['is_default' => 0, 'real_price' => $oldDefault['price']]);
                }
                $combination->update(['is_default' => 1, 'real_price' => 0]);
            } else {
                //remove
                $product->update(['is_active' => 0, 'default_combination_id' => null]);
                $combination->update(['is_default' => 0, 'real_price' => $combination['price']]);
            }
            return Response::response200($this->systemMessage->setDefaultCombination());
        } catch (BadRequestException $exception) {
            return Response::error404($exception->getMessage());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

}
