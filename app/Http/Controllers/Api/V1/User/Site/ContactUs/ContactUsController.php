<?php

namespace App\Http\Controllers\Api\V1\User\Site\ContactUs;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Models\ContactUs\ContactUsTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{

    ///not in use
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'contact_us', LanguageHelper::getCacheDefaultLang());
    }

    public function fetch()
    {
        try {
            $contact = ContactUsTranslation::where('language', LanguageHelper::getAppLanguage(\request()))->firstOrFail();
            return Response::response200([], $contact);

        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
