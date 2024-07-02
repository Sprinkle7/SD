<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Coupon;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Dashboard\Coupon\CreateCouponRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Coupon\UpdateCouponRequest;
use App\Models\Coupon\Coupon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),'coupon', LanguageHelper::getCacheDefaultLang());
    }

    public function create(CreateCouponRequest $request)
    {
        try {
            $coupon = Coupon::create(['code' => $request['code'],'percent' => $request['percent'],'expires_at' => $request['expires_at']]);
            return Response::response200([$this->systemMessage->create()], $coupon);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function update(Request $request)
    {
        try {
            $coupon = Coupon::findOrFail($request['id']);
            $collection = ['percent' => $request['percent'],'expires_at' => $request['expires_at']];
            if (isset($request['code'])) {
                $collection['code'] = $request['code'];
            }
            $coupon->update($collection);
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
            $query = Coupon::query();
            if (isset($request['code'])) {
                $query->where('code', 'LIKE', '%' . $request['code'] . '%');
            }
            if (isset($request['id'])) {
                $query->where('id', $request['id']);
            }

            if (isset($request['from_percent'])) {
                $query->where('percent', '>=', $request['from_percent']);
            }

            if (isset($request['to_percent'])) {
                $query->where('percent', '<=', $request['to_percent']);
            }

            if (isset($request['from_date'])) {
                $query->where('expires_at', '>=', $request['from_date']);
            }

            if (isset($request['to_date'])) {
                $query->where('expires_at', '<=', $request['to_date']);
            }

            $coupons = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(QueryHelper::perPage($request));
            return Response::response200([], $coupons);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($couponId)
    {
        try {
            $coupon = Coupon::findOrFail($couponId);
            $coupon->delete();
            return Response::response200([$this->systemMessage->delete()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($couponId)
    {
        try {
            $coupon = Coupon::findOrFail($couponId);
            return Response::response200([], $coupon);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

}
