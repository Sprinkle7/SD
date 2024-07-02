<?php

namespace App\Http\Controllers\Api\V1\User\Dashboard\Profile;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\Dashboard\Profile\Password\UpdatePasswordRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'password', LanguageHelper::getCacheDefaultLang());
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        try {
            $user = auth()->user();
            $user->update(['password'=>bcrypt($request['password'])]);
            return Response::response200($this->systemMessage->update(), $user);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
