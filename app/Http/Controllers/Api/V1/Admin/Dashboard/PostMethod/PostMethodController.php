<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\PostMethod;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\Models\PostMethod\PostMethodSystemMessage;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Dashboard\PostMethod\AddTranslationPostMethodRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\PostMethod\CreatePostMethodRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\PostMethod\UpdatePostMethodRequest;
use App\Models\Invoice\Invoice;
use App\Models\Invoice\InvoiceAddress;
use App\Models\Location\CountryPostDuration;
use App\Models\Order\Order;
use App\Models\Order\OrderAddress;
use App\Models\PostMethod\PostMethod;
use App\Models\PostMethod\PostMethodTranslation;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PostMethodController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new PostMethodSystemMessage(LanguageHelper::getAppLanguage(\request()),
            'postMethod', LanguageHelper::getCacheDefaultLang());
    }

    public function create(CreatePostMethodRequest $request)
    {
        try {
            $post = PostMethod::create([]);
            $translation = PostMethodTranslation::generatePostMethodCollection($request);
            $translation['post_method_id'] = $post['id'];
            $trans = PostMethodTranslation::create($translation);
            return Response::response200([$this->systemMessage->create()], $trans);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function addTranslation(AddTranslationPostMethodRequest $request, $postId)
    {
        try {
            $postT = PostMethodTranslation::where('post_method_id', $postId)
                ->where('language', $request['language'])->first();

            if (is_null($postT)) {
                $postTrans = PostMethodTranslation::generatePostMethodCollection($request);
                $postTrans['post_method_id'] = $postId;
                PostMethodTranslation::create($postTrans);
            }
            return Response::response200([$this->systemMessage->addTranslation()]);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function update(UpdatePostMethodRequest $request, $postId, $language)
    {
        try {
            $post = PostMethodTranslation::where('post_method_id', $postId)
                ->where('language', $language)->firstOrFail();
            $post->update(['title' => $request['title']]);
            return Response::response200([$this->systemMessage->update()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function search(Request $request)
    {
        try {
            $query = PostMethodTranslation::query();
            if (isset($request['language'])) {
                $query->where('language', $request['language']);
            }

            if (isset($request['title'])) {
                $query->where('title', 'like', '%' . $request['title'] . '%');
            }
            $categories = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(QueryHelper::perPage($request));

            return Response::response200([], $categories);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($postId)
    {
        try {
            $post = PostMethod::findOrFail($postId);
            $invoiceAddress = InvoiceAddress::where('post_id', $postId)->first();
            $orderAddress = OrderAddress::where('post_id', $postId)->first();

            if (!is_null($invoiceAddress) || !is_null($orderAddress)) {
                throw new ValidationException($this->systemMessage->unableToDelete());
            }

            PostMethodTranslation::where('post_method_id', $postId)->delete();
            CountryPostDuration::where('post_id', $postId)->delete();
            $post->delete();
            return Response::response200([$this->systemMessage->delete()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (ValidationException $exception) {
            return Response::error400($exception->getMessage());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($postId, $language)
    {
        try {
            $post = PostMethodTranslation::where('post_method_id', $postId)
                ->where('language', $language)->firstOrFail();
            return Response::response200([], $post);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

}
