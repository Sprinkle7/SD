<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Service;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Models\Cart\Cart;
use App\Models\Service\Service;
use App\Models\Service\ServiceTranslation;
use App\Models\Service\ServiceValue;
use App\Models\Service\ServiceValueTranslation;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceValueController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'service_value', LanguageHelper::getCacheDefaultLang());
    }

    public function create(Request $request, $serviceId)
    {
        try {
            $serviceValTranslates = [];
            foreach ($request['service_values'] as $index => $value) {
                $serviceVal = ServiceValue::create(
                    [
                        'service_id' => $serviceId,
                        'price' => $value['price'],
                        'duration' => $value['duration']]);
                $serviceValTranslates[$index] = ServiceValueTranslation::generateServiceTransCollection($value);
                $serviceValTranslates[$index]['language'] = $request['language'];
                $serviceValTranslates[$index]['service_id'] = $serviceId;
                $serviceValTranslates[$index]['service_value_id'] = $serviceVal['id'];
            }

            ServiceValueTranslation::insert($serviceValTranslates);

            return Response::response200([$this->systemMessage->create()]);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function addTranslation(Request $request, $serviceId)
    {
        try {
            $serviceValTranslates = [];
            foreach ($request['service_values'] as $index => $value) {
                $serviceValTranslates[$index] = ServiceValueTranslation::generateServiceTransCollection($value);
                $serviceValTranslates[$index]['language'] = $request['language'];
                $serviceValTranslates[$index]['service_id'] = $serviceId;
                $serviceValTranslates[$index]['service_value_id'] = $value['service_value_id'];
            }

            ServiceValueTranslation::insert($serviceValTranslates);
            return Response::response200([$this->systemMessage->addTranslation()]);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function update(Request $request, $serviceId, $language)
    {
        try {
            $serviceValTranslates = [];
            foreach ($request['service_values'] as $index => $value) {
                if (isset($value['id'])) {
                    ServiceValue::where('id', $value['service_value_id'])
                        ->update(['price' => $value['price'],
                            'duration' => $value['duration']]);
                    $serviceVal = ServiceValueTranslation::find($value['id']);
                    $serviceVal->update(['title' => $value['title']]);
                } else {
                    $serviceVal = [];
                    if (isset($value['service_value_id'])) {
                        $serviceVal['id'] = $value['service_value_id'];
                    } else {
                        $serviceVal = ServiceValue::create(['service_id' => $serviceId, 'price' => $value['price']]);
                    }
                    ServiceValue::where('id', $serviceVal['id'])->update(['price' => $value['price']]);

                    $serviceValTranslates[$index] = ServiceValueTranslation::generateServiceTransCollection($value);
                    $serviceValTranslates[$index]['language'] = $language;
                    $serviceValTranslates[$index]['service_id'] = $serviceId;
                    $serviceValTranslates[$index]['service_value_id'] = $serviceVal['id'];
                }
            }
            ServiceValueTranslation::insert($serviceValTranslates);

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

            return Response::response200([]);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($serviceValueId)
    {
        try {

            ///todo:bad practice
            $serviceValue = ServiceValue::findOrFail($serviceValueId);
            $relation = Cart::where('services','like','%'.$serviceValueId.'%')->first();

//            $relation = DB::table('product_service')->where('service_id', $serviceId)->first();
            if (!is_null($relation)) {
                throw new ValidationException('This value is available in carts now');
            }
            $serviceValue->delete();
            ServiceValueTranslation::where('service_value_id', $serviceValueId)->delete();
            return Response::response200([$this->systemMessage->delete()]);

        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->delete());
        } catch (ValidationException $exception) {
            return Response::error400($exception->getMessage());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($serviceId, $language)
    {
        try {
            $service = ServiceValueTranslation::with('serviceValue')
                ->where('service_id', $serviceId)->where('language', $language)->get();

            return Response::response200([], $service);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
