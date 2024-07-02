<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Portfolio;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Helper\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadImageRequest;
use App\Models\Portfolio\PortfolioImage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PortfolioImageController extends Controller
{
    private $systemMessage;

    private $imageDirectory = 'portfolio';

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'image', LanguageHelper::getCacheDefaultLang());
    }

    public function upload(UploadImageRequest $request)
    {
        try {
            $image = Uploader::uploadToStorage($request['image'], 'image', $this->imageDirectory);
            $i = PortfolioImage::create(['path' => $image['path']]);
            $image['id'] = $i['id'];
            return Response::response200($this->systemMessage->uploadFile(), $image);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($id)
    {
        try {
            $image = PortfolioImage::findOrFail($id);
            Uploader::deleteFromStorage($image['path'], 'image', $this->imageDirectory);
            $image->delete();
            return Response::response200($this->systemMessage->deleteFile());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
