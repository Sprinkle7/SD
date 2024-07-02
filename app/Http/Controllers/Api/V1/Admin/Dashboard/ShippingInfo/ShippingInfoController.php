<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\ShippingInfo;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Dashboard\ShippingInfo\AddTranslationShippingInfoRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\ShippingInfo\CreateShippingInfoRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\ShippingInfo\UpdateShippingInfoRequest;
use App\Models\ShipingInfo\ShippingInfo;
use App\Models\ShipingInfo\ShippingInfoTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ShippingInfoController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'shipping_info', LanguageHelper::getCacheDefaultLang());
    }

    public function create(CreateShippingInfoRequest $request)
    {
        try {
            $Shipping = ShippingInfo::create([]);
            $ShippingTrans = ShippingInfoTranslation::generateCategoryCollection($request);
            $ShippingTrans['shipping_info_id'] = $Shipping['id'];
            $trans = ShippingInfoTranslation::create($ShippingTrans);
            return Response::response200([$this->systemMessage->create()], $trans);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function addTranslation(AddTranslationShippingInfoRequest $request, $shippingId)
    {
        try {
            $shippingT = ShippingInfoTranslation::where('shipping_info_id', $shippingId)
                ->where('language', $request['language'])->first();
            if (is_null($shippingT)) {
                $shippingTrans = ShippingInfoTranslation::generateCategoryCollection($request);
                $shippingTrans['shipping_info_id'] = $shippingId;
                ShippingInfoTranslation::create($shippingTrans);
            }
            return Response::response200([$this->systemMessage->addTranslation()]);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function update(UpdateShippingInfoRequest $request, $shippingId, $language)
    {
        try {
            $shippingTrans = ShippingInfoTranslation::where('shipping_info_id', $shippingId)
                ->where('language', $language)->firstOrFail();
            $shippingTrans->update(['title' => $request['title'], 'description' => $request['description']]);
            return Response::response200([$this->systemMessage->update()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function search(Request $request)
    {
        try {
            $query = ShippingInfoTranslation::query()
                ->select('shipping_info_id', 'title', 'language');
            if (isset($request['language'])) {
                $query->where('language', $request['language']);
            }

            if (isset($request['title'])) {
                $query->where('title', 'like', '%' . $request['title'] . '%');
            }

            $shippingTranslations = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(QueryHelper::perPage($request));

            return Response::response200([], $shippingTranslations);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($shippingId)
    {
        try {
            ShippingInfoTranslation::where('shipping_info_id', $shippingId)->delete();
            ShippingInfo::where('id', $shippingId)->delete();
            return Response::response200([$this->systemMessage->delete()]);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($shippingId, $language)
    {
        try {
            $shippingTrans = ShippingInfoTranslation::where('shipping_info_id', $shippingId)
                ->where('language', $language)->firstOrFail();
            return Response::response200([], $shippingTrans);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
