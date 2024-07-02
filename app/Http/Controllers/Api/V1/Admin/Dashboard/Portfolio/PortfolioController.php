<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Portfolio;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Helper\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Dashboard\Portfolio\CreatePortfolioRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Portfolio\UpdatePortfolioRequest;
use App\Models\Portfolio\Portfolio;
use App\Models\Portfolio\PortfolioImage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'portfolio', LanguageHelper::getCacheDefaultLang());
    }

    public function create(CreatePortfolioRequest $request)
    {
        try {
            $portfolio = Portfolio::create(['title' => $request['title']]);
            PortfolioImage::whereIn('id', $request['images_id'])
                ->update(['portfolio_id' => $portfolio['id']]);
            return Response::response200($this->systemMessage->create());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function update(UpdatePortfolioRequest $request, $id)
    {
        try {
            $portfolio = Portfolio::findOrFail($id);
            $portfolio->update(['title' => $request['title']]);
            PortfolioImage::whereIn('id', $request['images_id'])
                ->update(['portfolio_id' => $portfolio['id']]);
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
            $query = Portfolio::query();
            if (isset($request['title'])) {
                $query->where('title', 'like', '%' . $request['title'] . '%');
            }

            $portfolios = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(QueryHelper::perPage($request));

            return Response::response200([], $portfolios);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($id)
    {
        try {
            $portfolio = Portfolio::with('images')->findOrFail($id);

            foreach ($portfolio['images'] as $image) {
                Uploader::deleteFromStorage($image['path'], 'image', 'portfolio');
            }
            $portfolio->delete();
            PortfolioImage::where('portfolio_id', $id)->delete();
            return Response::response200($this->systemMessage->delete());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($id)
    {
        try {
            $portfolio = Portfolio::with(['images' => function ($query) {
                $query->orderBy('arrange', 'DESC');
            }])->findOrFail($id);
            return Response::response200([], $portfolio);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function attachProducts(Request $request, $id)
    {
        try {
            $portfolio = Portfolio::findOrFail($id);
            $portfolio->products()->sync($request['products_id']);
            return Response::response200([$this->systemMessage->productAttached()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetchProducts($id, $language)
    {
        try {
            $port = Portfolio::findOrFail($id);
            $products = $port->productTranslation()->where('language', $language)->get(['title', 'Product_translations.product_id']);
            return Response::response200([], $products);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function updateArrange(Request $request)
    {
        try {
            foreach ($request['images'] as $image) {
                PortfolioImage::where('id', $image['id'])->update(['arrange' => $image['arrange']]);
            }
            return Response::response200([], $this->systemMessage->update());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
