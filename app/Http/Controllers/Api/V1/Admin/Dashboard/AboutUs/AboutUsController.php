<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\AboutUs;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Dashboard\AboutUs\UpdateAboutUsRequest;
use App\Models\AboutUs\AboutUs;
use App\Models\AboutUs\AboutUsTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class AboutUsController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'about_us', LanguageHelper::getCacheDefaultLang());
    }

    public function update(UpdateAboutUsRequest $request)
    {
        try {
            $about = AboutUs::first();
            $aboutT = [];
            if (is_null($about)) {
                $about = AboutUs::create([]);
                $aboutT = AboutUsTranslation::create(['about_us_id' => $about['id'],
                    'description' => $request['description'], 'language' => $request['language']]);
            } else {
                $aboutT = AboutUsTranslation::where('about_us_id', $about['id'])
                    ->where('language', $request['language'])->first();
                if (is_null($aboutT)) {
                    $aboutT = AboutUsTranslation::create(['about_us_id' => $about['id'],
                        'description' => $request['description'], 'language' => $request['language']]);
                } else {
                    $aboutT->update(['description' => $request['description']]);
                }
            }
            return Response::response200([$this->systemMessage->update()], $aboutT);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($language)
    {
        try {
            $about = AboutUsTranslation::where('language', $language)->firstOrFail();
            return Response::response200([], $about);

        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
