<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Invoice;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\Models\Invoice\InvoiceSystemMessage;
use App\Helper\SystemMessage\SystemMessage;
use App\Helper\Uploader\Uploader;
use App\Models\Product\Product;   
use App\Http\Controllers\Controller;
use App\Models\Duration\DurationTranslation;
use App\Mail\ForgetPassword;
use App\Models\User;
use App\Mail\InvoiceStatus;
use App\Models\Invoice\Invoice;
use App\Models\Invoice\InvoiceAddress;
use App\Models\Invoice\InvoiceAddressProduct;
use App\Models\Invoice\InvoiceEmailLog;
use App\Models\Invoice\InvoiceProductImage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new InvoiceSystemMessage(LanguageHelper::getAppLanguage(\request()),
            'invoice', LanguageHelper::getCacheDefaultLang());
    }

    public function search(Request $request)
    {
        try {
            $language = LanguageHelper::getAppLanguage($request);

            $query = Invoice::query()->with(['user:id,first_name,last_name,email,phone',
                'billing_country' => function ($query) use ($language) {
                    $query->where('language', $language);
                }]);

            if (isset($request['first_name']) || isset($request['last_name'])
                || isset($request['email']) || isset($request['phone'])) {
                $query->whereHas('user', function ($q) use ($request) {
                    if (isset($request['first_name'])) {
                        $q->where('first_name', 'LIKE', '%' . $request['first_name'] . '%');
                    }
                    if (isset($request['last_name'])) {
                        $q->where('last_name', 'LIKE', '%' . $request['last_name'] . '%');
                    }
                    if (isset($request['email'])) {
                        $q->where('email', 'LIKE', '%' . $request['email'] . '%');
                    }
                    if (isset($request['phone'])) {
                        $q->where('phone', 'LIKE', '%' . $request['phone'] . '%');
                    }
                });
            }

            if (isset($request['user_id'])) {
                $query->where('user_id', $request['user_id']);
            }
            if (isset($request['invoice_id'])) {
                $query->where('invoice_id', $request['invoice_id']);
            }
            if (isset($request['payment_type'])) {
                $query->where('payment_type', $request['payment_type']);
            }
            if (isset($request['payment_intent'])) {
                $query->where('payment_intent', $request['payment_intent']);
            }
            if (isset($request['country_id'])) {
                $query->where('country_id', $request['country_id']);
            }

            if (isset($request['is_complete'])) {
                $query->where('is_complete', $request['is_complete']);
            }

            $invoices = $query->orderBy('created_at', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(QueryHelper::perPage($request));
            return Response::response200([], $invoices);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function get_csv(Request $request, $pid) {

        $invoice = Invoice::where('payment_intent', $pid)->first();
        $user = User::find($invoice['user_id'])->toArray();
        $addressesInvoice = InvoiceAddress::with(['post' => function ($query) {
            $query->where('language', 'de');
        }])->where('payment_intent', $pid)->get();

        $productsInvoice = InvoiceAddressProduct::where('payment_intent', $pid)->get()->toArray();
        $products = [];
        $x = 0;
        foreach ($productsInvoice as $productIn) {
            foreach ($productIn['services_data'] as $servive) {
                $productIn['product_title'] = $productIn['product_title'] . ' || ' . $servive['service_value_translation']['title'];
            }
            $duration = DurationTranslation::where('duration_id', $productIn['duration_id'])->where('language', 'de')->first();
            $productIn['product_title'] = $productIn['product_title'] . ' || ' . $duration['title'];
            $products[$x] = $productIn;
            $products[$x]['product'] = Product::find($productIn['product_id']);
            $x++;
        }

        foreach ($addressesInvoice as $addressInc) {
            $address = User\Address::find($addressInc['address_id']);
            if ($address) {
                $address['country_name'] = $addressInc['country_name'];
            } else {
                $address = $user;
            }

            $query = 'SELECT cpd.id as post_duration_id,cpd.post_id,pmt.title as \'post_method\',cpd.min_price,cpd.price,pmt.post_method_id FROM country_post_duration cpd  ' . 'JOIN post_method_translations pmt ON cpd.post_id=pmt.post_method_id WHERE cpd.post_id = ' . $addressInc['post_id'] . ' ORDER BY cpd.price';
            $postMethod = DB::select(DB::raw($query));
            $addressInc['address'] = $address;
            $addressInc['shippings'] = $postMethod;
        }
        return Response::response200([], ['product' => $productsInvoice,'invoice' => $invoice,'products' => $products, 'user' => $user, 'address' => $addressesInvoice]);
    }


    public function fetch($id)
    {
        try {
            $language = LanguageHelper::getAppLanguage(\request());

            $invoice = Invoice::with(['user',
                'billing_country' => function ($query) use ($language) {
                    $query->where('language', $language);
                }])->findOrFail($id);
            if ($invoice['seen'] == 0) {
                $invoice->update(['seen' => 1]);
            }
            return Response::response200([], $invoice);
        } catch (ModelNotFoundException $exception) {
            return Response::error404();
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function unseenCount()
    {
        try {
            ///number of invoices that not be checked yes
            $unseen['count'] = Invoice::where('state', 'new_order')->orWhere('state', 'ready')->count();
            return Response::response200([], $unseen);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function invoiceAddresses(Request $request, $paymentIntent)
    {
        try {
            $language = LanguageHelper::getAppLanguage($request);
            $invoiceDetails = Invoice::where('payment_intent', $paymentIntent)->firstOrFail()->addresses()->with([
            'country' => function ($q) use ($language) {
                $q->select('id', 'country_id', 'name')->where('language', $language);
            },
            'duration' => function ($q) use ($language) {
                $q->select('id', 'duration_id', 'title')->where('language', $language);
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
            $language = LanguageHelper::getAppLanguage($request);
            $products = InvoiceAddressProduct::with([
            'product:id,cover_image',
            'info' => function ($query) use ($language) {
                $query->where('language', $language);
            }, 'duration_info' => function ($query) use ($language) {
                $query->where('language', $language);
            }, 'images'])->where('order_address_id', $incAddId)
            ->paginate(QueryHelper::perPage($request));
            return Response::response200([], $products);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function setNumberOFNeededImage(Request $request, $paymentIntent)
    {
        try {
            $invoice = Invoice::with(['user:id,first_name,last_name,email'])
                ->select('invoice_id', 'payment_intent', 'seen', 'state', 'amount_total', 'user_id')
                ->findOrFail($paymentIntent);
            $user = $invoice['user'];
            $message = null;
            $orderState = [];
            $imageCount = array_sum($request['products']);
            if ($imageCount == 0) {
                $orderState['state'] = 'ready';
                $message = 'Your invoice is confirmed';
            } else if ($invoice['state'] == 'new_order') {
                $orderState['state'] = 'confirmed';
                $orderState['confirmed_email_has_sent'] = 1;
                $orderState['confirmed_email_sent_at'] = now();
                $message = 'Your invoice is confirmed, Please check and upload image if needed';
            } else {
                $orderState['state'] = 'updating';
                $message = 'Please Update your invoice image';
            }

            $invoice->update($orderState);

            if ($imageCount > 0) {
                foreach ($request['products'] as $key => $value) {
                    InvoiceAddressProduct::where('invoice_address_product_id', $key)
                        ->update(['number_of_images' => $value]);
                }
            }
            Mail::mailer('secondary')->to($user['email'])
                ->queue((new InvoiceStatus($user, $invoice['invoice_id'], $message))
                    ->onQueue('mail'));
            InvoiceEmailLog::create([
                'payment_intent' => $invoice['payment_intent'],
                'type' => $orderState['state'],
                'sent_at' => now()]);
            return Response::response200($this->systemMessage->update(), $invoice);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function changeInvoiceStatus($paymentIntent)
    {
        try {
            $invoice = Invoice::where('payment_intent', $paymentIntent)->firstOrFail();
            $state = !$invoice['is_complete'] ? 'completed' : 'ready';
            $invoice->update(['is_complete' => !$invoice['is_complete'], 'state' => $state]);
            return Response::response200([$this->systemMessage->update()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function cancelInvoice($paymentIntent)
    {
        try {
            $invoice = Invoice::where('payment_intent', $paymentIntent)->firstOrFail();
            $invoice->update(['state' => 'canceled']);
            return Response::response200($this->systemMessage->update());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function changeInvoiceAddressStatus(Request $request, $orderAddressId)
    {
        try {
            $invoiceAddress = InvoiceAddress::where('order_address_id', $orderAddressId)->firstOrFail();
            $invoiceAddress->update(['status' => $request['status']]);
            return Response::response200([$this->systemMessage->update()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetchImage($invoiceProductId, $imageId)
    {
        try {
            $image = InvoiceProductImage::where('id', $imageId)->where('invoice_address_product_id', $invoiceProductId)->firstOrFail();

            $path = storage_path('app/image/invoice/' . $image['path']);
            return response(base64_encode(file_get_contents($path)));

        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
    public function deleteImage($imageId)
    {
        try {
            $image = InvoiceProductImage::where('id', $imageId)->firstOrFail();
            Uploader::deleteFromStorage($image['path'], 'image', 'invoice', 'private');
            $image->delete();
            return Response::response200($this->systemMessage->deleteFile());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function downloadImage($invoiceProductId, $imageId)
    {
        try {
            $image = InvoiceProductImage::where('id', $imageId)->where('invoice_address_product_id', $invoiceProductId)->firstOrFail();

            $path = storage_path('app/image/invoice/' . $image['path']);
            return response()->download($path);

        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function downloadXml($xmlName)
    {
        try {
            $path = storage_path('app/OrderCsv/' . $xmlName);
            return response()->download($path);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function invoiceSentEmails($paymentIntent)
    {
        try {
            return Response::response200([], InvoiceEmailLog::where('payment_intent', $paymentIntent)->paginate(50));
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
