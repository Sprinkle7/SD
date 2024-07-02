<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Duration;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\Models\Duration\DurationSystemMessage;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Dashboard\Duration\AddTranslationDurationRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Duration\CreateDurationRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Duration\UpdateDurationRequest;
use App\Models\Duration\Duration;
use App\Models\Duration\DurationTranslation;
use App\Models\Invoice\Invoice;
use App\Models\Invoice\InvoiceAddress;
use App\Models\Invoice\InvoiceAddressProduct;
use App\Models\Location\CountryPostDuration;
use App\Models\Order\Order;
use App\Models\Order\OrderAddress;
use App\Models\Order\OrderAddressProduct;
use App\Models\Product\Pivot\DurationProduct;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class DurationController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new DurationSystemMessage(LanguageHelper::getAppLanguage(\request()),
            'duration', LanguageHelper::getCacheDefaultLang());
    }

    public function create(CreateDurationRequest $request)
    {
        try {
            $duration = Duration::create(Duration::generateDurationCollection($request));
            $duTransCollection = DurationTranslation::generateDurationCollection($request);
            $duTransCollection['duration_id'] = $duration['id'];
            $dutrans = DurationTranslation::create($duTransCollection);
            $dutrans['duration_info'] = $duration;
            return Response::response200([$this->systemMessage->create()], $dutrans);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function addTranslation(AddTranslationDurationRequest $request, $durationId)
    {
        try {
            $durationT = DurationTranslation::where('duration_id', $durationId)
                ->where('language', $request['language'])->first();
            if (is_null($durationT)) {
                $duTransCollection = DurationTranslation::generateDurationCollection($request);
                $duTransCollection['duration_id'] = $durationId;
                DurationTranslation::create($duTransCollection);
            }

            return Response::response200([$this->systemMessage->addTranslation()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function update(UpdateDurationRequest $request, $durationId, $language)
    {
        try {
            $duration = Duration::findOrFail($durationId);
            $durationTrans = DurationTranslation::where('duration_id', $durationId)
                ->where('language', $language)->firstOrFail();
            $duration->update(Duration::generateDurationCollection($request));
            $durationTrans->update(['title' => $request['title']]);

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
            $query = DurationTranslation::query()->with('duration_info');
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

    public function delete($durationId)
    {
        try {
            $duration = Duration::findOrFail($durationId);
            $product = DurationProduct::where('duration_id', $durationId)->first();
            $invoiceAddress = InvoiceAddress::where('duration_id', $durationId)->first();
            $invoiceAddressProduct = InvoiceAddressProduct::where('duration_id', $durationId)->first();
            $orderAddress = OrderAddress::where('duration_id', $durationId)->first();
            $orderAddressProduct = OrderAddressProduct::where('duration_id', $durationId)->first();

            if (!is_null($product) ||
                !is_null($invoiceAddress) || !is_null($invoiceAddressProduct) || !is_null($orderAddress) || !is_null($orderAddressProduct)) {
                throw new ValidationException($this->systemMessage->unableToDelete());
            }
            DurationTranslation::where('duration_id', $durationId)->delete();
            CountryPostDuration::where('duration_id', $durationId)->delete();
            $duration->delete();

            return Response::response200([$this->systemMessage->delete()], $invoiceAddress);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (ValidationException $exception) {
            return Response::error400($exception->getMessage());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($durationId, $language)
    {
        try {
            $duration = DurationTranslation::with('duration_info')->where('duration_id', $durationId)
                ->where('language', $language)->firstOrFail();
            return Response::response200([], $duration);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
