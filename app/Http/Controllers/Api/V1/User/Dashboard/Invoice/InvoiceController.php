<?php

namespace App\Http\Controllers\Api\V1\User\Dashboard\Invoice;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Models\Invoice\Invoice;
use App\Models\Order\CustomOrder;
use App\Models\Invoice\InvoiceAddressProduct;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'invoice', LanguageHelper::getCacheDefaultLang());
    }

    public function search(Request $request)
    {
        try {
            $userId = auth()->user()->id;
            $language = LanguageHelper::getAppLanguage($request);
            $query = Invoice::query()->with(['user:id,first_name,last_name,email,phone',
                'billing_country' => function ($query) use ($language) {
                    $query->where('language', $language);
                }]);
            $query->where('user_id', $userId);

            if (isset($request['is_complete'])) {
                $query->where('is_complete', $request['is_complete']);
            }

            $invoices = $query->orderBy('created_at', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(QueryHelper::perPage($request));
            
            foreach ($invoices as $invoice) {
                // Fetch invoice details with addresses and country
                $invoiceDetails = Invoice::where('user_id', $userId)
                    ->where('payment_intent', $invoice->payment_intent)
                    ->firstOrFail()
                    ->addresses()
                    ->with([
                        'country' => function ($q) use ($language) {
                            $q->select('id', 'country_id', 'name')->where('language', $language);
                        }
                    ])->paginate(QueryHelper::perPage($request));
                
                $invoice['otherdetails'] = $invoiceDetails;
            
                // Fetch the custom order if it exists
                $custom = CustomOrder::where('payment_intent', $invoice->payment_intent)->first();
                if ($custom) {
                    $invoice['custom'] = $custom;
                }
            }
                

            return Response::response200([], $invoices);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($id)
    {
        try {
            $userId = auth()->user()->id;
            $language = LanguageHelper::getAppLanguage(\request());
            $invoice = Invoice::with(['user:id,first_name,last_name,email,phone,gender,company','billing_country' => function ($query) use ($language) {
                $query->where('language', $language);
            }])->where('user_id', $userId)->findOrFail($id);
            return Response::response200([], $invoice);
        } catch (ModelNotFoundException $exception) {
            return Response::error404();
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function invoiceAddresses(Request $request, $paymentIntent)
    {
        try {
            $language = LanguageHelper::getAppLanguage($request);
            $userId = auth()->user()->id;
            $invoiceDetails = Invoice::where('user_id', $userId)->where('payment_intent', $paymentIntent)->firstOrFail()->addresses()->with([
                'country' => function ($q) use ($language) {
                    $q->select('id', 'country_id', 'name')->where('language', $language);
                },
                'post' => function ($q) use ($language) {
                    $q->select('id', 'post_method_id', 'title')->where('language', $language);
                }])->paginate(QueryHelper::perPage($request));
            return Response::response200([], $invoiceDetails);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function invoiceAddressProducts(Request $request, $incAddId)
    {
        try {
            $userId = auth()->user()->id;
            $language = LanguageHelper::getAppLanguage($request);
            $products = InvoiceAddressProduct::with([
                'product:id,cover_image',
                'info' => function ($query) use ($language) {
                    $query->where('language', $language);
                },
                'duration_info' => function ($query) use ($language) {
                    $query->where('language', $language);
                }, 'images'])->where('user_id', $userId)->where('order_address_id', $incAddId)
                ->paginate(QueryHelper::perPage($request));
            return Response::response200([], $products);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function readyInvoice($paymentIntent)
    {
        try {
            $invoice = Invoice::where('payment_intent', $paymentIntent)->firstOrFail();
            $invoice->update(['state' => 'ready']);
            return Response::response200($this->systemMessage->update());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

}
