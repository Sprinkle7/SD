<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Page;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Dashboard\Page\CreatePageSidebarRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Page\UpdatePageSidebarRequest;
use App\Models\Page\Page;
use App\Models\Page\PageSidebarInfo;
use App\Models\Page\PageSidebarItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageSidebarController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'page_sidebar', LanguageHelper::getCacheDefaultLang());
    }

    public function create(CreatePageSidebarRequest $request)
    {
        try {
            $sidebarInfo = PageSidebarInfo::create(['title' => $request['title']]);
            $sideBarItems = $request['sidebar'];
            array_walk($sideBarItems, function (&$data, $key) use ($sidebarInfo) {
                $data['sidebar_id'] = $sidebarInfo['id'];
            });
            PageSidebarItems::insert($sideBarItems);
            return Response::response200([$this->systemMessage->create()]);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function update(UpdatePageSidebarRequest $request, $sidebarId)
    {
        try {
            $sidebarInfo = PageSidebarInfo::findOrFail($sidebarId);
            DB::table('page_sidebar_items')->where('sidebar_id', $sidebarId)->delete();
            $sideBarItems = $request['sidebar'];
            array_walk($sideBarItems, function (&$data, $key) use ($sidebarInfo) {
                $data['sidebar_id'] = $sidebarInfo['id'];
            });
            $sidebarInfo->update(['title' => $request['title']]);
            PageSidebarItems::insert($sideBarItems);
            return Response::response200([$this->systemMessage->update()]);
        } catch (\Exception $exception) {
            return Response::error500();
        }
    }

    public function search(Request $request)
    {
        try {
            $query = PageSidebarInfo::query();
            if ($request['title']) {
                $query->where('title', 'like', '%' . $request['title'] . '%');
            }
            $sidebars = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(QueryHelper::perPage($request));
            return Response::response200([], $sidebars);
        } catch (\Exception $exception) {
            return Response::error500();
        }
    }

    public function fetch($sidebarId, $language)
    {
        try {
            $sidebarInfo = PageSidebarInfo::findOrFail($sidebarId);
            $sideBarItems = PageSidebarItems::with(['pages' => function ($q) use ($language) {
                $q->select(['id', 'page_id', 'title', 'language'])->where('language', $language);
            }])->where('sidebar_id', $sidebarId)->orderBy('arrange')->get();
            $sideBar['sidebar_info'] = $sidebarInfo;
            $sideBar['sidebar_info']['sidebar_items'] = $sideBarItems;

            return Response::response200([], $sideBar);
        } catch (\Exception $exception) {
            return Response::error500();
        }
    }

    public function delete($sidebarId)
    {
        try {
            $sidebarInfo = PageSidebarInfo::findOrFail($sidebarId);
            Page::where('sidebar_id', $sidebarId)->update(['sidebar_id' => null]);
            PageSidebarItems::where('sidebar_id', $sidebarId)->delete();
            $sidebarInfo->delete();
            return Response::response200([$this->systemMessage->delete()]);
        } catch (\Exception $exception) {
            return Response::error500();
        }
    }
}
