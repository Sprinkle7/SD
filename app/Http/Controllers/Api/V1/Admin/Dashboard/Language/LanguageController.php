<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Language;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Dashboard\Language\CreateLanguageRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Language\UpdateLanguageRequest;
use App\Models\Language\Language;
use App\Models\Language\LanguageReference;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LanguageController extends Controller
{

    ////this is not in use+++++++++++++
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'language', LanguageHelper::getCacheDefaultLang());
    }

    public function create(CreateLanguageRequest $request)
    {
        try {
            Language::create(Language::generateLanguageCollection($request));
            Language::cacheLanguages();
            return Response::response200($this->systemMessage->create());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function update(UpdateLanguageRequest $request, $id)
    {
        try {
            $lang = Language::FindorFail($id);
            ///
            $lang->update(Language::generateLanguageCollection($request));
            Language::cacheLanguages();
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
            $query = Language::query();
            if (isset($request['name'])) {
                $query->where('name', 'like', '%' . $request['name'] . '%');
            }
            if (isset($request['code'])) {
                $query->where('code', 'like', '%' . $request['code'] . '%');
            }
            $lang = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')->paginate(10);
            return Response::response200([], $lang);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($id)
    {
        try {
            $lang = Language::FindorFail($id);
            $lang->delete();
            Language::cacheLanguages();
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
            $lang = Language::FindorFail($id);
            return Response::response200([], $lang);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function activate($id)
    {
        try {
            $lang = Language::FindorFail($id);
            $lang->update(['active' => 1]);
            Language::cacheLanguages();
            return Response::response200($this->systemMessage->activate());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function deactivate($id)
    {
        try {
            $lang = Language::FindorFail($id);
            if ($lang['default'] === 1)
                return Response::error400($this->systemMessage->isDefault());
            $lang->update(['active' => 0]);
            Language::cacheLanguages();
            return Response::response200($this->systemMessage->deactivate());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function setDefault($id)
    {
        try {
            $lang = Language::FindorFail($id);
            Language::where('default', 1)->update(['default' => 0]);
            $lang->update(['default' => 1, 'active' => 1]);

            Language::cacheDefaultLanguage($lang['code']);

            return Response::response200($this->systemMessage->setDefault());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function appLanguages()
    {
        try {
            $langs['all'] = LanguageHelper::getCacheAllLang();
            $langs['default'] = LanguageHelper::getCacheDefaultLang();
            return Response::response200([], $langs);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());

        }
    }

    public function suggestLanguage(Request $request)
    {
        try {
            $langs = LanguageReference::where('title', 'like', '%' . $request['name'] . '%')->take(5)->get();
            return Response::response200([], $langs);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());

        }
    }
}
