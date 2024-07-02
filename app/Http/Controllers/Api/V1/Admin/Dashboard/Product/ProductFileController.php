<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Product;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Helper\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteImageRequest;
use App\Http\Requests\DeletePdfRequest;
use App\Http\Requests\DeleteVideoRequest;
use App\Http\Requests\DeleteZipRequest;
use App\Http\Requests\UploadImageRequest;
use App\Http\Requests\UploadPdfRequest;
use App\Http\Requests\UploadVideoRequest;
use App\Http\Requests\UploadZipRequest;
use App\Models\Product\Product;
use App\Models\Product\ProductFile;

class ProductFileController extends Controller
{
    private $systemMessage;
    private $imageDirectory = 'product';
    private $pdfDirectory = 'product';

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'image', LanguageHelper::getCacheDefaultLang());
    }

    public function uploadCoverImage(UploadImageRequest $request)
    {
        try {
            $image = Uploader::uploadToStorage($request['image'], 'image', 'temp');
            return Response::response200($this->systemMessage->uploadFile(), $image);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function deleteCoverImage(DeleteImageRequest $request)
    {
        try {
            $product = Product::find($request['product_id']);

            $path = '';
            if (Uploader::fileExistInStorage($request['image'], 'image', 'temp')) {
                $path = 'temp';
            } else {
                $path = $this->imageDirectory;
            }
            Uploader::deleteFromStorage($request['image'], 'image', $path);
            if (!is_null($product)) {
                $product->update(['cover_image' => null]);
            }
            return Response::response200($this->systemMessage->deleteFile());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function uploadVideo(UploadVideoRequest $request)
    {
        try {
            $image = Uploader::uploadToStorage($request['video'], 'video', 'temp');
            $image = ProductFile::create(['path' => $image['path'], 'type' => $image['type']]);
            return Response::response200($this->systemMessage->uploadFile(), $image);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function deleteVideo(DeleteVideoRequest $request)
    {
        try {
            $productVideo = ProductFile::where('type', 'video')->findorFail($request['id']);

            $path = '';
            if (Uploader::fileExistInStorage($productVideo['path'], 'video', 'temp')) {
                $path = 'temp';
            } else {
                $path = $this->imageDirectory;
            }

            Uploader::deleteFromStorage($productVideo['path'], 'video', $path);
            $productVideo->delete();

            return Response::response200($this->systemMessage->deleteFile());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function uploadDatasheetPdf(UploadPdfRequest $request)
    {
        try {
            $image = Uploader::uploadToStorage($request['file'], 'file', 'temp');
            return Response::response200($this->systemMessage->uploadFile(), $image);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function deleteDatasheetPdf(DeletePdfRequest $request)
    {
        try {
            $path = '';
            $product = Product::find($request['product_id']);
            if (Uploader::fileExistInStorage($request['file'], 'file', 'temp')) {
                $path = 'temp';
            } else {
                $path = $this->pdfDirectory . '/datasheet';
            }
            Uploader::deleteFromStorage($request['file'], 'file', $path);
            if (!is_null($product)) {
                $product->update(['data_sheet_pdf' => null]);
            }
            return Response::response200($this->systemMessage->deleteFile());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function uploadAssemblyPdf(UploadPdfRequest $request)
    {
        try {
            $image = Uploader::uploadToStorage($request['file'], 'file', 'temp');
            return Response::response200($this->systemMessage->uploadFile(), $image);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function deleteAssemblyPdf(DeletePdfRequest $request)
    {
        try {
            $product = Product::find($request['product_id']);
            $path = '';
            if (Uploader::fileExistInStorage($request['file'], 'file', 'temp')) {
                $path = 'temp';
            } else {
                $path = $this->imageDirectory . '/assembly';
            }
            Uploader::deleteFromStorage($request['file'], 'file', $path);
            if (!is_null($product)) {
                $product->update(['assembly_pdf' => null]);
            }
            return Response::response200($this->systemMessage->deleteFile());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function uploadZip(UploadZipRequest $request)
    {
        try {
            $image = Uploader::uploadToStorage($request['file'], 'file', 'temp');
            return Response::response200($this->systemMessage->uploadFile(), $image);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function deleteZip(DeleteZipRequest $request)
    {
        try {
            $product = Product::find($request['product_id']);
            $path = '';
            if (Uploader::fileExistInStorage($request['file'], 'file', 'temp')) {
                $path = 'temp';
            } else {
                $path = $this->imageDirectory . '/zip';
            }
            Uploader::deleteFromStorage($request['file'], 'file', $path);
            if (!is_null($product)) {
                $product->update(['zip' => null]);
            }
            return Response::response200($this->systemMessage->deleteFile());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
