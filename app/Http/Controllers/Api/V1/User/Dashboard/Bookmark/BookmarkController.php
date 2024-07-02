<?php

namespace App\Http\Controllers\Api\V1\User\Dashboard\Bookmark;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\Models\Bookmark\BookmarkSystemMessage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new BookmarkSystemMessage(LanguageHelper::getAppLanguage(\request()),
            'bookmark', LanguageHelper::getCacheDefaultLang());
    }

    public function fetchBookMarks()
    {
        try {
            $language = LanguageHelper::getAppLanguage(\request());
            $user = auth()->user();
            $bookmarks = $user->bookmarks()->
            with(['product_info',
                'product_info.workingDay' => function ($query) {
                    $query->orderBy('price', 'DESC');
                },
                'product_info.workingDay.durationTranslation' => function ($query) use ($language) {
                    $query->where('language', $language);
                },
                'product_info.defaultCombination:id,price,additional_price',
                'product_info.defaultMenu' => function ($query) {
                    $query->select('id', 'level', 'parent_id')->where('is_active', 1);
                },
                'product_info.defaultMenu.menuT' => function ($query) use ($language) {
                    $query->select('id', 'menu_id', 'title', 'language')->where('language', $language);
                },
                'product_info.defaultMenu.parent:id,level,parent_id',
                'product_info.defaultMenu.parent.menuT' => function ($query) use ($language) {
                    $query->select('id', 'menu_id', 'title', 'language')->where('language', $language);
                },
            ])->
            where('language', $language)->
            whereHas('product_info', function ($query) {
                $query->where('is_active', 1);
            })->
            paginate(QueryHelper::perPage(\request()))->
            map(function ($p) {
                $p->product_info->working_day = $p->product_info->workingDay->take(1);
                unset($p->product_info->workingDay);
                return $p;
            });
            return Response::response200([], $bookmarks);

        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function bookmarkCount()
    {
        try {
            $user = auth()->user();
            $bookmarksCount = $user->bookmarkP()->count();
            return Response::response200([], $bookmarksCount);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
