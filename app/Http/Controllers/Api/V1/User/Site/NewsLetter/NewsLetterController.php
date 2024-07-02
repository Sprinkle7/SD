<?php

namespace App\Http\Controllers\Api\V1\User\Site\NewsLetter;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\Site\NewLetter\NewsletterSubribeRequest;
use App\Models\NewsLetter\NewsLetter;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NewsLetterController extends Controller
{

    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'newsLetter', LanguageHelper::getCacheDefaultLang());
    }

    public function subscribe(NewsletterSubribeRequest $request)
    {
        try {

            $subscribe = [
                'first_name' => $request['first_name'],
                'last_name' => $request['last_name'],
                'email' => $request['email']];
            NewsLetter::create($subscribe);

            return Response::response200([], $this->systemMessage->create());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());

        }
    }
}
