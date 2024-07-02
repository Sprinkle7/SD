<?php

namespace App\Http\Controllers\Api\V1\User\Site\Popup;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Models\Popup\PopupTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PopupController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'popup', LanguageHelper::getCacheDefaultLang());
    }

    public function popups()
    {
        try {
            $language = LanguageHelper::getAppLanguage(\request());
            $popup = PopupTranslation::with('popupInfo')->where('language', $language)
                ->whereHas('popupInfo', function ($q) {
                    $q->where('expires_at', '>=', now())->where('is_active',1);
                })->first();

            return Response::response200([], $popup);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
