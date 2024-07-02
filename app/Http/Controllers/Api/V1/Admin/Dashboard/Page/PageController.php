<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Page;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Dashboard\Page\AddTranslationPageRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Page\CreatePageRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Page\UpdatePageRequest;
use App\Models\Page\Page;
use App\Models\Page\PageTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'page', LanguageHelper::getCacheDefaultLang());
    }

    public function create(CreatePageRequest $request)
    {
        try {
            $page = Page::create(['sidebar_id' => $request['sidebar_id']]);
            $collection = PageTranslation::generateCollection($request);
            $collection['page_id'] = $page['id'];
            $pageTranslation = PageTranslation::create($collection);
            return Response::response200($this->systemMessage->create(), $pageTranslation);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function addTranslation(AddTranslationPageRequest $request, $pageId)
    {
        try {
            $pageT = PageTranslation::where('page_id', $pageId)
                ->where('language', $request['language'])->first();
            if (is_null($pageT)) {
                $collection = PageTranslation::generateCollection($request);
                $collection['page_id'] = $pageId;
                PageTranslation::create($collection);
            }
            return Response::response200([$this->systemMessage->addTranslation()]);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function update(UpdatePageRequest $request, $id, $language)
    {
        try {
            $page = Page::findorFail($id);
            $pageT = PageTranslation::where('page_id', $id)
                ->where('language', $language)->firstOrFail();
            $collection = ['title' => $request['title'], 'content' => $request['content'],'section'=>$request['section'],'footer_alias'=>$request['footer_alias']];
            if (isset($request['slug']) && $pageT['slug'] != $request['slug']) {
                $collection['slug'] = $request['slug'];
            }
            $page->update(['sidebar_id' => $request['sidebar_id']]);
            $pageT->update($collection);
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
            $query = PageTranslation::query()
                ->select('page_id', 'title', 'slug', 'language', 'footer_alias');
            if (isset($request['language'])) {
                $query->where('language', $request['language']);
            }
            if (isset($request['title'])) {
                $query->where('title', 'like', '%' . $request['title'] . '%');
            }
            $pageTranslations = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(QueryHelper::perPage($request));
            return Response::response200([], $pageTranslations);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($id)
    {
        try {
            PageTranslation::where('page_id', $id)->delete();
            Page::where('id', $id)->delete();
            ///delete page from footer and sidebars
            DB::table('footer_section_page')->where('page_id', $id)->delete();
            DB::table('page_sidebar_items')->where('type', 'page')
                ->where('page_id', $id)->delete();
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
            $pageTranslation = PageTranslation::with(
                [
                    'pageInfo:id,sidebar_id',
                    'pageInfo.sidebar:id,title',

                ])->where('page_id', $id)
                ->where('language', $language)->firstOrFail();
            return Response::response200([], $pageTranslation);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function isUnique(Request $request)
    {
        try {
            $pageTranslation = PageTranslation::where('slug', $request['slug'])->get();
            $isUnique = true;
            if (count($pageTranslation) > 0) {
                $isUnique = false;
            }
            return Response::response200([], ['is_unique' => $isUnique]);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
