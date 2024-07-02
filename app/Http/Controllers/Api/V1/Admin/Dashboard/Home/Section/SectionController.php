<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Home\Section;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Dashboard\Home\Section\AddTranslationSectionRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Home\Section\CreateSectionRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Home\Section\UpdateSectionRequest;
use App\Models\Home\Section\HSection;
use App\Models\Home\Section\HSectionTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'section', LanguageHelper::getCacheDefaultLang());
    }

    public function create(CreateSectionRequest $request)
    {
        try {
            $section = HSection::create(['type' => $request['type']]);
            $sectionT = HSectionTranslation::create(['h_section_id' => $section['id'],
                'title' => $request['title'], 'language' => $request['language']]);
            if ($request['type'] == 'manual') {
                $section->products()->sync($request['products_id']);
            }
            $sectionT['section_info'] = $section;
            return Response::response200($this->systemMessage->create(),$sectionT);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function addTranslation(AddTranslationSectionRequest $request, $SectionId)
    {
        try {
            $section = HSectionTranslation::where('h_section_id', $SectionId)
                ->where('language', $request['language'])->first();
            if (is_null($section)) {
                HSectionTranslation::create(['h_section_id' => $SectionId,
                    'title' => $request['title'], 'language' => $request['language']]);
            }
            return Response::response200($this->systemMessage->addTranslation());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function update(UpdateSectionRequest $request, $SectionId, $language)
    {
        try {
            $section = HSection::findOrFail($SectionId);
            $section->update(['type' => $request['type']]);
            HSectionTranslation::where('h_section_id', $SectionId)
                ->where('language', $language)
                ->update(['title' => $request['title']]);
            $section->products()->sync($request['products_id']);

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
            $query = HSectionTranslation::query();
            if (isset($request['language'])) {
                $query->where('language', $request['language']);
            }
            if (isset($request['title'])) {
                $query->where('title', 'like', '%' . $request['title'] . '%');
            }


            $sliders = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(QueryHelper::perPage($request));

            return Response::response200([], $sliders);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($id)
    {
        try {
            $section = HSection::findOrFail($id);
            $section->products()->sync([]);
            HSectionTranslation::where('h_section_id', $id)->delete();
            $section->delete();
            return Response::response200([$this->systemMessage->delete()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($SectionId, $language)
    {
        try {
            $section = HSectionTranslation::with(['productT' => function ($query) use ($language) {
                $query->where('language', $language)->orderBy('arrange','ASC');
            }, 'sectionInfo'])
                ->where('h_section_id', $SectionId)
                ->where('language', $language)->firstOrFail();
            return Response::response200([], $section);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
