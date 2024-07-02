<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Comment;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\Models\Comment\CommentSystemMessage;
use App\Helper\SystemMessage\Models\Menu\MenuSystemMessage;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Models\Comment\Comment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    ///this is not in use yet
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new CommentSystemMessage(LanguageHelper::getAppLanguage(\request()),
            'comment', LanguageHelper::getCacheDefaultLang());
    }

    public function search(Request $request)
    {
        try {
            $language = LanguageHelper::getAppLanguage(\request());
            $query = Comment::query();
            $query->with(['user:id,first_name,last_name',
                'product' => function ($query) use ($language) {
                    $query->select('id', 'product_id', 'title')->where('language', $language);
                }]);
            if (isset($request['title'])) {
                $query->where('title', 'like', '%' . $request['title'] . '%');
            }
            if (isset($request['content'])) {
                $query->where('content', 'like', '%' . $request['content'] . '%');
            }
            if (isset($request['user_id'])) {
                $query->where('user_id', $request['user_id']);
            }

            $comments = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(QueryHelper::perPage($request));
            return Response::response200([], $comments);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($commentId)
    {
        try {
            $language = LanguageHelper::getAppLanguage(\request());
            $comment = Comment::with(['user:id,first_name,last_name',
                'product' => function ($query) use ($language) {
                    $query->select('id', 'product_id', 'title')->where('language', $language);
                }])->findOrFail($commentId);
            return Response::response200([], $comment);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetchReplays(Request $request, $commentId)
    {
        try {
            $language = LanguageHelper::getAppLanguage(\request());
            $comments = Comment::with(['user:id,first_name,last_name',
                'product' => function ($query) use ($language) {
                    $query->select('id', 'product_id', 'title')->where('language', $language);
                }])->where('parent_id', $commentId)
                ->paginate(QueryHelper::perPage($request));
            return Response::response200([], $comments);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function replay(Request $request, $productId, $commentId)
    {
        try {
            Comment::whereNull('parent_id')->findOrFail($commentId);
            $comment = Comment::generateCommentCollection($request);
            $comment['product_id'] = $productId;
            $comment['parent_id'] = $commentId;
            $comment['user_id'] = auth()->user()->id;
            Comment::create($comment);
            return Response::response200([$this->systemMessage->addReply()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function activate($commentId)
    {
        try {
            $comment = Comment::findOrFail($commentId);
            $comment->update(['is_active' => 1]);
            return Response::response200($this->systemMessage->activate());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function deactivate($commentId)
    {
        try {
            $comment = Comment::findOrFail($commentId);
            $comment->update(['is_active' => 0]);
            return Response::response200($this->systemMessage->deactivate());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
