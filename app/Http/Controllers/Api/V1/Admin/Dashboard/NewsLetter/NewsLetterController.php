<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\NewsLetter;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Models\NewsLetter\NewsLetter;
use Illuminate\Http\Request;

class NewsLetterController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'newsLetter', LanguageHelper::getCacheDefaultLang());
    }

    public function search(Request $request)
    {
        try {
            $query = NewsLetter::query()->with('user');
            if (isset($request['email'])) {
                $email = $request['email'];
                $query->where('email','LIKE','%'.$email.'%' )->orWhereHas('user', function ($q) use ($email) {
                    $q->where('email','LIKE','%'.$email.'%')->where('role_id',3);
                });
            }
            $news = $query->orderBy('created_at', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(QueryHelper::perPage($request));
            return Response::response200([], $news);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete(Request $request)
    {
        try {
            $email = $request['email'];
            NewsLetter::where('email', $email)->orWhereHas('user', function ($q) use ($email) {
                $q->where('email', $email);
            })->delete();
            return Response::response200([$this->systemMessage->delete()]);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
