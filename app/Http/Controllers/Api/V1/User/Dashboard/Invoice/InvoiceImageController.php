<?php

namespace App\Http\Controllers\Api\V1\User\Dashboard\Invoice;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Helper\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteImageRequest;
use App\Http\Requests\UploadImageRequest;
use App\Models\Invoice\Invoice;
use App\Models\Invoice\InvoiceAddressProduct;
use App\Models\Invoice\InvoiceProductImage;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class InvoiceImageController extends Controller
{
    private $systemMessage;
    private $imageDirectory = 'invoice';

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'image', LanguageHelper::getCacheDefaultLang());
    }

    public function upload(UploadImageRequest $request, $invoiceProductId, $imageId = null)
    {
        try {
            $userId = auth()->user()->id;
            $product = InvoiceAddressProduct::with('images')
                ->where('user_id', $userId)->where('invoice_address_product_id', $invoiceProductId)->firstOrFail();
            $productImageCount = count($product['images']);
            if ($productImageCount < 25) {
                $image = Uploader::uploadToStorage($request['image'], 'image', $this->imageDirectory, 'public');
                $image = InvoiceProductImage::create(['invoice_address_product_id' => $invoiceProductId, 'path' => $image['path']]);
                return Response::response200($this->systemMessage->uploadFile(), $image);
            } else {
                throw new ValidationException('not allowed');
            }
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (ValidationException $exception) {
            return Response::error400($exception->getMessage());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($invoiceProductId, $imageId)
    {
        try {
        $userId = auth()->user()->id;
        InvoiceAddressProduct::where('user_id', $userId)
            ->where('invoice_address_product_id', $invoiceProductId)->firstOrFail();

        $image = InvoiceProductImage::where('id', $imageId)->where('invoice_address_product_id', $invoiceProductId)->firstOrFail();
            Uploader::deleteFromStorage($image['path'], 'image', $this->imageDirectory, 'public');
            $image->delete();
            return Response::response200($this->systemMessage->deleteFile());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (ValidationException $exception) {
            return Response::error400($exception->getMessage());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($invoiceProductId, $imageId)
    {
        try {
            $userId = auth()->user()->id;
            InvoiceAddressProduct::where('user_id', $userId)->where('invoice_address_product_id', $invoiceProductId)->firstOrFail();
            $image = InvoiceProductImage::where('id', $imageId)->where('invoice_address_product_id', $invoiceProductId)->firstOrFail();

            $path = storage_path('app/image/' . $this->imageDirectory . '/' . $image['path']);
            return response(base64_encode(file_get_contents($path)));

        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }


}
