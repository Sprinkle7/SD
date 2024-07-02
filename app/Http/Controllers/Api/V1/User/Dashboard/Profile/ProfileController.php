<?php

namespace App\Http\Controllers\Api\V1\User\Dashboard\Profile;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\Dashboard\Profile\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'profile', LanguageHelper::getCacheDefaultLang());
    }

    public function fetchProfile()
    {
        try {
            $user = auth()->user();
            return Response::response200([], $user);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
       try {
            // DB::statement("alter table addresses add column is_default integer(2) default 0");
            $user = auth()->user();
            $userCollection = User::generateUserCollection($request);
            $userCollection['profile_completed'] = 1;
            unset($userCollection['password']);
            unset($userCollection['role_id']);
            $user->update($userCollection);
            return Response::response200($this->systemMessage->update(), $user);
       } catch (ModelNotFoundException $exception) {
           return Response::error404($this->systemMessage->error404());
       } catch (\Exception $exception) {
           return Response::error500($this->systemMessage->error500());
       }
    }
}
