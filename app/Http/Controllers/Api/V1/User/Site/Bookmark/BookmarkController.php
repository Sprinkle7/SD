<?php

namespace App\Http\Controllers\Api\V1\User\Site\Bookmark;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\Models\Bookmark\BookmarkSystemMessage;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Models\Bookmark\Bookmark;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new BookmarkSystemMessage(LanguageHelper::getAppLanguage(\request()),
            'bookmark', LanguageHelper::getCacheDefaultLang());
    }

    public function addToBookmark($productId)
    {
        try {
            $userId = auth()->user()->id;
            $bookmark = Bookmark::where('product_id', $productId)
                ->where('user_id', $userId)->first();
            if (is_null($bookmark)) {
                Bookmark::create(['product_id' => $productId, 'user_id' => $userId]);
            }
            return Response::response200($this->systemMessage->bookmarked());
        }  catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function removeFromBookmark($productId)
    {
        try {
            $userId = auth()->user()->id;
            Bookmark::where('product_id', $productId)->where('user_id', $userId)->delete();
            return Response::response200($this->systemMessage->bookmarkRemoved());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
