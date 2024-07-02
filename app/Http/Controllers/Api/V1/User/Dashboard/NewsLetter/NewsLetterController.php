<?php

namespace App\Http\Controllers\Api\V1\User\Dashboard\NewsLetter;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Models\NewsLetter\NewsLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsLetterController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'newsLetter', LanguageHelper::getCacheDefaultLang());
    }

    public function subInfo()
    {
        try {
            $news = \auth()->user()->first()->newsletter;
            return Response::response200([], $news);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function subscribe()
    {
        try {
            $user = \auth()->user()->with('newsletter')->first();
            if (is_null($user['newsletter'])) {
                NewsLetter::create(['user_id' => $user['id']]);
            }
            return Response::response200([$this->systemMessage->create()]);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function unsubscribe()
    {
        try {
             \auth()->user()->first()->newsletter()->delete();
            return Response::response200([$this->systemMessage->delete()]);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

}
