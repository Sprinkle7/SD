<?php

namespace App\Http\Controllers\Api\V1\User\Site\Comment;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\Models\Comment\CommentSystemMessage;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\Site\Comment\AddCommentReplayRequest;
use App\Http\Requests\Api\V1\User\Site\Comment\AddCommentRequest;
use App\Models\Comment\Comment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new CommentSystemMessage(LanguageHelper::getAppLanguage(\request()),
            'comment', LanguageHelper::getCacheDefaultLang());
    }

    public function comment(AddCommentRequest $request, $productId)
    {
        try {
            $comment = Comment::generateCommentCollection($request);
            $comment['product_id'] = $productId;
            $comment['user_id'] = auth()->user()->id;
            Comment::create($comment);
            return Response::response200([$this->systemMessage->create()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetchComments(Request $request, $productId)
    {
        try {
            $comments = Comment::with('user:id,first_name,last_name')->where('product_id', $productId)->whereNull('parent_id')->where('is_active', 1)
                ->paginate(QueryHelper::perPage($request));
            return Response::response200([], $comments);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function replay(AddCommentReplayRequest $request, $productId, $commentId)
    {
        try {
            Comment::whereNull('parent_id')->where('is_active', 1)->findOrFail($commentId);
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

    public function fetchReplays(Request $request, $commentId)
    {
        try {
            Comment::where('is_active', 1)->findOrFail($commentId);
            $comments = Comment::with('user:id,first_name,last_name')->where('parent_id', $commentId)->where('is_active', 1)
                ->paginate(QueryHelper::perPage($request));
            return Response::response200([], $comments);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }



}
