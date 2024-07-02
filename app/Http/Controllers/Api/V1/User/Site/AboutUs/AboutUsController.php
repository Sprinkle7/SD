<?php

namespace App\Http\Controllers\Api\V1\User\Site\AboutUs;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Models\AboutUs\AboutUsTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class AboutUsController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'about_us', LanguageHelper::getCacheDefaultLang());
    }

    public function fetch()
    {
        try {
            $about = AboutUsTranslation::where('language', LanguageHelper::getAppLanguage(\request()))->firstOrFail();
            return Response::response200([], $about);

        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
