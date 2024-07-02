<?php

namespace App\Http\Controllers\Api\V1\User\Site\Page;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Models\Page\PageSidebar;
use App\Models\Page\PageSidebarInfo;
use App\Models\Page\PageSidebarItems;
use Illuminate\Http\Request;

class PageSidebarController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'page_sidebar', LanguageHelper::getCacheDefaultLang());
    }

    public function fetch($id)
    {
        try {
            $language = LanguageHelper::getAppLanguage(\request());
            $sidebarInfo = PageSidebarInfo::findOrFail($id);
            $sideBarItems = PageSidebarItems::with(['pages' => function ($q) use ($language) {
                $q->select(['id', 'page_id', 'title', 'language','footer_alias'])->where('language', $language);
            }])->where('sidebar_id', $id)->orderBy('arrange')->get();
            $sideBar['sidebar_info'] = $sidebarInfo;
            $sideBar['sidebar_info']['sidebar_items'] = $sideBarItems;
            $sideBar['sidebar_info']['sidebar_items']->each(function ($item) {
                $item->footer_title = $item->pages->footer_alias;
            });
            return Response::response200([], $sideBar);
        } catch (\Exception $exception) {
            return Response::error500();

        }
    }
}
