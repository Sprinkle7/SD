<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Service;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Helper\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteImageRequest;
use App\Http\Requests\UploadImageRequest;
use App\Models\Service\ServiceTranslation;
use Illuminate\Http\Request;

class ServiceImageController extends Controller
{
    private $systemMessage;
    private $imageDirectory = 'service';

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'image', LanguageHelper::getCacheDefaultLang());
    }

    public function uploadImage(UploadImageRequest $request)
    {
        try {
            $image = Uploader::uploadToStorage($request['image'], 'image', 'temp');
            return Response::response200($this->systemMessage->uploadFile(), $image);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function deleteImage(DeleteImageRequest $request)
    {
        try {
            $service = ServiceTranslation::find($request['service_translation_id']);

            $path = '';
            if (Uploader::fileExistInStorage($request['image'], 'image', 'temp')) {
                $path = 'temp';
            } else {
                $path = $this->imageDirectory;
            }
            Uploader::deleteFromStorage($request['image'], 'image', $path);

            if (!is_null($service)) {
                $service->update(['image' => null]);
            }
            return Response::response200($this->systemMessage->deleteFile());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
