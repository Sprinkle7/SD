<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Setting;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\Setting\ProductionDelay;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ProductionDelayController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'production_delay', LanguageHelper::getCacheDefaultLang());
    }

    public function update(Request $request)
    {
        try {
            ProductionDelay::update($request['delay']);
            return Response::response200([$this->systemMessage->update()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch()
    {
        try {

            return Response::response200([], ProductionDelay::fetch());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
