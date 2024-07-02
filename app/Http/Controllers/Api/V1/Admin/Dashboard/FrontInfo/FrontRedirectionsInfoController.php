<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\FrontInfo;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FrontRedirectionsInfoController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'front', LanguageHelper::getCacheDefaultLang());
    }

    public function add(Request $request)
    {
        try {
            Storage::disk('local')->put('front\frontRedirections.txt', json_encode($request['info']));
            return Response::response200($this->systemMessage->update());
        } catch (\Exception $exception) {
            return Response::error500();
        }
    }

    public function fetch()
    {
        try {
            $data = null;
            if (Storage::disk('local')->exists('front\frontRedirections.txt')) {
                $data = Storage::disk('local')->get('front\frontRedirections.txt');
            }
            return Response::response200([], ['info' => json_decode($data)]);
        } catch (\Exception $exception) {
            return Response::error500();
        }
    }
}
