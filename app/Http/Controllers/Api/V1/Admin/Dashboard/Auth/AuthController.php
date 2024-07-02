<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Auth;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\Models\Auth\AuthSystemMessage;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new AuthSystemMessage(LanguageHelper::getAppLanguage(\request()),
            'login', LanguageHelper::getCacheDefaultLang());
    }

    public function login(Request $request)
    {
        try {
            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials))
                return Response::error401([$this->systemMessage->wrongCredentials()]);
            $user = $request->user();
            if ($user->role['id'] != 1 && $user->role['id'] != 2)
                return Response::error403();
            return $this->quickLogin($credentials, $user);
        } catch (\Exception $exception) {
            return Response::error500();
        }
    }

    private function quickLogin($credentials, $user)
    {
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        $token->save();
        return Response::response200([$this->systemMessage->signIn()], [
            'user' => $user,
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }
}
