<?php
namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Download;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Helper\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteImageRequest;
use App\Http\Requests\UploadPdfRequest;
use Illuminate\Http\Request;

class DownloadImageController extends Controller
{
    private $systemMessage;
    private $imageDirectory = 'service';

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'download', LanguageHelper::getCacheDefaultLang());
    }

    public function uploadImage(UploadPdfRequest $request)
    {
        try {
            $image = Uploader::uploadToStorage($request['file'], 'file', 'temp');
            return Response::response200($this->systemMessage->uploadFile(), $image);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function deleteImage(DeleteImageRequest $request)
    {
        try {
            $path = '';
            if (Uploader::fileExistInStorage($request['file'], 'image', 'temp')) {
                $path = 'temp';
            } else {
                $path = $this->imageDirectory;
            }
            Uploader::deleteFromStorage($request['file'], 'image', $path);
            return Response::response200($this->systemMessage->deleteFile());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
