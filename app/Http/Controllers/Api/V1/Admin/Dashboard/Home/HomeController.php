<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Home;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Dashboard\Home\AddHomeTranslationRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Home\CreateHomeRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Home\Section\AddTranslationSectionRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Home\Section\CreateSectionRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Home\Section\UpdateSectionRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Home\UpdateHomeRequest;
use App\Models\Home\Home;
use App\Models\Home\HomeTranslation;
use App\Models\Home\Section\HSection;
use App\Models\Home\Section\HSectionTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'home', LanguageHelper::getCacheDefaultLang());
    }

    public function create(CreateHomeRequest $request)
    {
        try {
            $home = Home::create(['title' => $request['title'], 'slider_id' => $request['slider_id']]);
            $homeT = HomeTranslation::create(
                ['home_id' => $home['id'], 'description' => $request['description'], 'language' => $request['language']]);
            $home->sections()->sync($request['sections_id']);
            $homeT['home_info'] = $home;
            return Response::response200($this->systemMessage->create(), $homeT);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function addTranslation(AddHomeTranslationRequest $request, $homeId)
    {
        try {
            $homeT = HomeTranslation::where('home_id', $homeId)->where('language', $request['language'])->first();
            if (is_null($homeT)) {
                HomeTranslation::create(
                    ['home_id' => $homeId, 'description' => $request['description'], 'language' => $request['language']]);
            }
            return Response::response200($this->systemMessage->addTranslation());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function update(UpdateHomeRequest $request, $id, $language)
    {
        try {
            $home = Home::findOrFail($id);
            $homeT = HomeTranslation::where('home_id', $id)->where('language', $language)->firstOrFail();
            $home->update(['title' => $request['title'], 'slider_id' => $request['slider_id']]);
            $homeT->update(['description' => $request['description']]);
            $home->sections()->sync($request['sections_id']);
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
            $query = Home::query();

            if (isset($request['title'])) {
                $query->where('title', 'like', '%' . $request['title'] . '%');
            }


            $homes = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(QueryHelper::perPage($request));

            return Response::response200([], $homes);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($id)
    {
        try {
            $home = Home::findOrFail($id);
            $home->sections()->sync([]);
            HomeTranslation::where('home_id', $id)->delete();
            $home->delete();
            return Response::response200([$this->systemMessage->delete()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($id, $language)
    {
        try {

            $home = HomeTranslation::with([
                'home_info', 'home_info.slider' , 'home_info.sectionsT' => function ($query) use ($language) {
                    $query->where('language', $language);
                }])->where('home_id',$id)->where('language',$language)->firstorFail();
            return Response::response200([], $home);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function activate($id)
    {
        try {
            $home = Home::findOrFail($id);
            Home::where('is_active', 1)->update(['is_active' => 0]);
            $home->update(['is_active' => 1]);
            return Response::response200([$this->systemMessage->activate()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
