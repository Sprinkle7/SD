<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\User;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Dashboard\User\CreateRoleRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\User\UpdateRoleRequest;
use App\Models\User\RoleTranslation;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'role', LanguageHelper::getCacheDefaultLang());
    }

    public function addTranslation(CreateRoleRequest $request, $roleId)
    {
        try {
            $roleT = RoleTranslation::where('role_id', $roleId)
                ->where('language', $request['language'])->first();
            if (is_null($roleT)) {
                $role = RoleTranslation::generateRoleCollection($request);
                $role['role_id'] = $roleId;
                RoleTranslation::create($role);
            }
            return Response::response200([$this->systemMessage->create()]);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    /// update translation
    public function update(UpdateRoleRequest $request, $roleId, $language)
    {
        try {
            $roleT = RoleTranslation::where('role_id', $roleId)->where('language', $language)->firstOrFail();
            $roleT->update(['title' => $request['title']]);
            return Response::response200([$this->systemMessage->update()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function search(Request $request)
    {
        try {
            $query = RoleTranslation::query();
            if (isset($request['language'])) {
                $query->where('language', $request['language']);
            }
            if (isset($request['title'])) {
                $query->where('title', 'like', '%' . $request['title'] . '%');
            }
            $roles = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(QueryHelper::perPage($request));

            return Response::response200([], $roles);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($roleId, $language)
    {
        try {

            $roleT = RoleTranslation::where('role_id', $roleId)->where('language', $language)->firstOrFail();
            return Response::response200([], $roleT);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
