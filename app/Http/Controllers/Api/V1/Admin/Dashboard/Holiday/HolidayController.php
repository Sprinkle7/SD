<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Holiday;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Models\Holiday\Holiday;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'holiday', LanguageHelper::getCacheDefaultLang());
    }
    // 
    // ,'title' => $request['title']
    public function create(Request $request)
    {
        try {
            $holiday = Holiday::create(['date' => $request['date'],'title' => $request['title']]);
            return Response::response200([$this->systemMessage->create()], $holiday);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500(), $exception->getMessage());
        }
    }

    public function update(Request $request, $holidayId)
    {
        try {
            $holiday = Holiday::findOrFail($holidayId);
            $holiday->update(['date' => $request['date'],'title' => $request['title']]);
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
            $query = Holiday::query();

            if (isset($request['date'])) {
                $query->where('date', $request['date']);
            }
            if (isset($request['id'])) {
                $query->where('id', $request['id']);
            }
            if (isset($request['from'])) {
                $query->where('date', '>=', $request['from']);
            }
            if (isset($request['to'])) {
                $query->where('date', '<=', $request['to']);
            }
            $holidays = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(QueryHelper::perPage($request));
            return Response::response200([], $holidays);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($holidayId)
    {
        try {
            $holiday = Holiday::findOrFail($holidayId);
            $holiday->delete();
            return Response::response200([$this->systemMessage->delete()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($holidayId)
    {
        try {
            $holiday = Holiday::findOrFail($holidayId);
            return Response::response200([], $holiday);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
