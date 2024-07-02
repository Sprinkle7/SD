<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\User;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Dashboard\User\CreateUserRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'user', LanguageHelper::getCacheDefaultLang());
    }

    public function create(CreateUserRequest $request)
    {
        try {
            $userCollection = User::generateUserCollection($request);
            $userCollection['profile_completed'] = 1;
            User::create($userCollection);
            return Response::response200($this->systemMessage->create());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = User::findorFail($id);
            $userCollection = User::generateUserCollection($request);

            if (isset($userCollection['email']) && $user['email'] === $userCollection['email']) {
                unset($userCollection['email']);
            }
            if (isset($userCollection['phone']) && $user['phone'] === $userCollection['phone']) {
                unset($userCollection['phone']);
            }

            $user->update(User::generateUserCollection($request));
            return Response::response200($this->systemMessage->update());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function search(Request $request)
    {
        try {
            $query = User::query()->with('RoleTranslation');
            if (isset($request['first_name'])) {
                $query->where('first_name', 'like', '%' . $request['first_name'] . '%');
            }
            if (isset($request['last_name'])) {
                $query->where('last_name', 'like', '%' . $request['last_name'] . '%');
            }
            if (isset($request['role_id'])) {
                $query->where('role_id',  $request['role_id']);
            }
            if (isset($request['email'])) {
                $query->where('email', 'like', '%' . $request['email'] . '%');
            }
            if (isset($request['phone'])) {
                $query->where('phone', 'like', '%' . $request['phone'] . '%');
            }
            if (isset($request['id'])) {
                $query->where('id', $request['id']);
            }
            $users = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(QueryHelper::perPage($request));

            return Response::response200([], $users);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($id)
    {
        try {
            $user = User::FindorFail($id);
            $user->delete();
            return Response::response200($this->systemMessage->delete());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($id)
    {
        try {
            $user = User::FindorFail($id);
            return Response::response200([], $user);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
