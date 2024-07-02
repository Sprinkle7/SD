<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\TechnicalInfo;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;

use App\Http\Requests\Api\V1\Admin\Dashboard\ShippingInfo\CreateShippingInfoRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\TechnicalInfo\AddTranslationTechnicalInfoRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\TechnicalInfo\CreateTechnicalInfoRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\TechnicalInfo\UpdateTechnicalInfoRequest;
use App\Models\TechnicalInfo\TechnicalInfo;
use App\Models\TechnicalInfo\TechnicalInfoTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class TechnicalInfoController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'technical_info', LanguageHelper::getCacheDefaultLang());
    }

    public function create(CreateTechnicalInfoRequest $request)
    {
        try {
            $tech = TechnicalInfo::create([]);
            $techTrans = TechnicalInfoTranslation::generateCategoryCollection($request);
            $techTrans['technical_info_id'] = $tech['id'];
            $trans = TechnicalInfoTranslation::create($techTrans);
            return Response::response200([$this->systemMessage->create()], $trans);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function addTranslation(AddTranslationTechnicalInfoRequest $request, $techId)
    {
        try {
            $techT = TechnicalInfoTranslation::where('technical_info_id', $techId)
                ->where('language', $request['language'])->first();
            if (is_null($techT)) {
                $techTrans = TechnicalInfoTranslation::generateCategoryCollection($request);
                $techTrans['technical_info_id'] = $techId;
                TechnicalInfoTranslation::create($techTrans);
            }
            return Response::response200([$this->systemMessage->addTranslation()]);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function update(UpdateTechnicalInfoRequest $request, $techId, $language)
    {
        try {
            $techTrans = TechnicalInfoTranslation::where('technical_info_id', $techId)
                ->where('language', $language)->firstOrFail();
            $techTrans->update(['title' => $request['title'], 'description' => $request['description']]);
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
            $query = TechnicalInfoTranslation::query()
                ->select('technical_info_id', 'title', 'language');
            if (isset($request['language'])) {
                $query->where('language', $request['language']);
            }

            if (isset($request['title'])) {
                $query->where('title', 'like', '%' . $request['title'] . '%');
            }

            $techTranslations = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(QueryHelper::perPage($request));

            return Response::response200([], $techTranslations);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete(Request $request, $techId)
    {
        try {
            TechnicalInfoTranslation::where('technical_info_id', $techId)->delete();
            TechnicalInfo::where('id', $techId)->delete();
            return Response::response200([$this->systemMessage->delete()]);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch(Request $request, $techId, $language)
    {
        try {
            $techTrans = TechnicalInfoTranslation::where('technical_info_id', $techId)
                ->where('language', $language)->firstOrFail();
            return Response::response200([], $techTrans);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

}
