<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Service;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Helper\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Models\Service\Service;
use App\Models\Service\ServiceTranslation;
use App\Models\Service\ServiceValue;
use App\Models\Service\ServiceValueTranslation;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'service', LanguageHelper::getCacheDefaultLang());
    }

    public function create(Request $request)
    {
        try {
            $service = Service::create([]);
            $serviceTranslate = ServiceTranslation::generateServiceTransCollection($request);
            $serviceTranslate['service_id'] = $service['id'];
            $serviceTrans = ServiceTranslation::create($serviceTranslate);
            if ($request['image']) {
                Uploader::moveFile($request['image'], 'image', 'temp', 'service');

            }

            $serviceValTranslates = [];
            foreach ($request['values'] as $index => $value) {
                $serviceVal = ServiceValue::create([
                    'service_id' => $service['id'],
                    'price' => $value['price'],
                    'duration' => $value['duration']]);
                $serviceValTranslates[$index] = ServiceValueTranslation::generateServiceTransCollection($value);
                $serviceValTranslates[$index]['language'] = $request['language'];
                $serviceValTranslates[$index]['service_id'] = $service['id'];
                $serviceValTranslates[$index]['service_value_id'] = $serviceVal['id'];
            }

            ServiceValueTranslation::insert($serviceValTranslates);

            return Response::response200([$this->systemMessage->create()], $serviceTrans);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function addTranslation(Request $request, $serviceId)
    {
        try {
            $serviceTrans = ServiceTranslation::generateServiceTransCollection($request);
            $serviceTrans['service_id'] = $serviceId;
            ServiceTranslation::create($serviceTrans);

            if ($request['image']) {
                Uploader::moveFile($request['image'], 'image', 'temp', 'service');

            }

            $serviceValTranslates = [];
            foreach ($request['values'] as $index => $value) {
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
            $serviceTrans = ServiceTranslation::where('service_id', $serviceId)
                ->where('language', $language)->firstOrFail();

            if (isset($request['image']) && $serviceTrans['image'] != $request['image']) {
                Uploader::moveFile($request['image'], 'image', 'temp', 'service');
            }

            $serviceTrans->update([
                'title' => $request['title'],
                'image' => $request['image'],
                'height' => $request['height'],
                'width' => $request['width'],
            ]);

            $serviceValTranslates = [];
            foreach ($request['values'] as $index => $value) {
                if (isset($value['id'])) {
                    ServiceValue::where('id', $value['service_value_id'])
                        ->update(['price' => $value['price'], 'duration' => $value['duration']]);
                    $serviceVal = ServiceValueTranslation::find($value['id']);
                    $serviceVal->update(['title' => $value['title']]);
                } else {
                    $serviceVal = [];
                    if (isset($value['service_value_id'])) {
                        $serviceVal['id'] = $value['service_value_id'];
                    } else {
                        $serviceVal = ServiceValue::create(
                            ['service_id' => $serviceId, 'price' => $value['price'], 'duration' => $value['duration']]);
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
            $query = ServiceTranslation::query();

            if (isset($request['language'])) {
                $query->where('language', $request['language']);
            }

            if (isset($request['title'])) {
                $query->where('title', 'like', '%' . $request['title'] . '%');
            }

            $serviceTrans = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(10);
            return Response::response200([], $serviceTrans);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($serviceId)
    {
        try {
            $relation = DB::table('product_service')->where('service_id', $serviceId)->first();
            if (!is_null($relation)) {
                throw new ValidationException($this->systemMessage->unableToDelete());

            }
            //delete files
            Service::where('id', $serviceId)->delete();
            ServiceTranslation::where('service_id', $serviceId)->delete();
            ServiceValue::where('service_id', $serviceId)->delete();
            ServiceValueTranslation::where('service_id', $serviceId)->delete();
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
            $serviceTrans = ServiceTranslation::with(['values' => function ($query) use ($language) {
                $query->where('language', $language);
            }, 'values.serviceValue'])->where('service_id', $serviceId)
                ->where('language', $language)->firstOrFail();

            foreach ($serviceTrans['values'] as $index => $service) {
                $serviceTrans['values'][$index]['price'] = $service['serviceValue']['price'];
                $serviceTrans['values'][$index]['duration'] = $service['serviceValue']['duration'];
                unset($serviceTrans['values'][$index]['serviceValue']);
            }
            return Response::response200([], $serviceTrans);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
