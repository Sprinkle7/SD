<?php

namespace App\Http\Controllers\Api\V1\User\Auth;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\Models\Auth\AuthSystemMessage;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\Auth\RegisterRequest;
use App\Mail\ForgetPassword;
use App\Models\User;
use App\Models\User\PasswordReset;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthController extends Controller
{

    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new AuthSystemMessage(LanguageHelper::getAppLanguage(\request()),'login', LanguageHelper::getCacheDefaultLang());
    }

    public function register(RegisterRequest $request)
    {
        try {
            $user = [
                'email' => $request['email'], 
                'password' => bcrypt($request['password']),
                'address' => $request['address'],
                'city' => $request['city'],
                'company' => $request['company'],
                'email' => $request['email'],
                'country_id' => $request['country_id'],
                'first_name' => $request['first_name'],
                'gender' => $request['gender'],
                'last_name' => $request['last_name'],
                'phone' => $request['phone'],
                'postcode' => $request['postcode'],
                'profile_completed' => 1
            ];
            $user['role_id'] = 3;
            User::create($user);
            return Response::response200($this->systemMessage->signUp());
        } catch (\Exception $exception) {
            return Response::error500($exception->errorInfo[2]);
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials))
                return Response::error401([$this->systemMessage->wrongCredentials()]);
            $user = $request->user();
            if ($user->role['id'] != 3)
                return Response::error403();
            return $this->quickLogin($credentials, $user);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
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

    public function forgetPassword(Request $request)
    {
        try {
            $language = LanguageHelper::getAppLanguage($request);
            $user = User::select('id', 'email','gender','last_name')->where('role_id', 3)->where('email', $request['email'])->first();
            $passwordReset = '';
            if (!is_null($user)) {
                $token = uniqid() . uniqid() . time();
                $passwordReset = PasswordReset::find($user['email']);
                $name = (($user['gender'] == "male") ? "Sehr geehrter Herr" : "Sehr geehrter Frau").' '.$user['last_name'];
                if (is_null($passwordReset)) {
                    $passwordReset = PasswordReset::create([
                        'email' => $user['email'],
                        'token' => $token,
                        'expires_at' => now()->addMinutes(120),
                        'request_count' => 1]);
                } else {
                    if ($passwordReset['request_count'] < 5 || (!is_null($passwordReset['ban_till']) && now() > $passwordReset['ban_till'])) {
                        $requestCount = $passwordReset['request_count'] > 5 ? 1 : $passwordReset['request_count'] + 1;
                        $passwordReset->update([
                            'token' => $token,
                            'expires_at' => now()->addMinutes(15),
                            'request_count' => $requestCount,
                            'ban_till' => null]);
                    } else {
                        if (is_null($passwordReset['ban_till'])) {
                            $passwordReset->update([
                                'ban_till' => now()->addMinutes(15)]);
                        }
                    }
                }

                if ($passwordReset['request_count'] <= 5) {
                    Mail::mailer('smtp')->to($user['email'])->queue((new ForgetPassword($token, $language, $name))->onQueue('mail'));
                }
                return Response::response200([$this->systemMessage->forgetPasswordFound()]);
            } else {
                return Response::response200([$this->systemMessage->forgetPassword()]);
            }
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function resetPassword(Request $request, $token)
    {
        try {
            $passwordReset = PasswordReset::where('token', $token)->where('expires_at', '>', now())->firstOrFail();
            $user = User::where('email', $passwordReset['email'])->firstOrFail();
            $user->update(['password' => bcrypt($request['password'])]);
            $passwordReset->delete();
            return Response::response200([$this->systemMessage->updatePassword()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

}