<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Setting;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Models\Tax\Tax;
// use App\Helper\Setting\Tax;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaxController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'tax', LanguageHelper::getCacheDefaultLang());
    }

    public function create(Request $request)
    {
        try {
            Tax::create(['tax' => $request['tax']]);
            return Response::response200([$this->systemMessage->update()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
    
    public function update(Request $request)
    {
        try {
            $tax = Tax::where('id', '=',0)->first();
            $tax->update(['tax' => $request['tax']]);
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
            $tax = Tax::where('id', '=', 0)->first();
            return Response::response200([], ['tax' => $tax->tax]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}