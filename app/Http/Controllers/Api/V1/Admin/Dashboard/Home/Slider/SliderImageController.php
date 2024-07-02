<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Home\Slider;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Helper\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadImageRequest;
use App\Models\Home\Slider\SliderImage;
use App\Models\Menu\MenuCoverImage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SliderImageController extends Controller
{

    private $systemMessage;
    private $imageDirectory = 'home/slider';

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'image', LanguageHelper::getCacheDefaultLang());
    }

    public function upload(UploadImageRequest $request)
    {
        try {
            $image = Uploader::uploadToStorage($request['image'], 'image', $this->imageDirectory);

            $i = null;
            if (!is_null($request['id'])) {
                $i = SliderImage::find($request['id']);
            }

            if (is_null($i)) {
                $i = SliderImage::create([
                    'path' => $image['path'], 'language' => $request['language']]);
            } else {
                $i->update(['path' => $image['path']]);
            }

            return Response::response200($this->systemMessage->uploadFile(), $i);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($id)
    {
        try {
            $image = SliderImage::findOrFail($id);
            Uploader::deleteFromStorage($image['path'], 'image', $this->imageDirectory);
            $image->update(['path' => null]);
            return Response::response200($this->systemMessage->deleteFile());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }


    public function uploadMobile(UploadImageRequest $request)
    {
        try {
            $image = Uploader::uploadToStorage($request['image'], 'image', $this->imageDirectory);

            $i = null;
            if (!is_null($request['id'])) {
                $i = SliderImage::find($request['id']);
            }

            if (is_null($i)) {
                $i = SliderImage::create([
                    'mobile_path' => $image['path'], 'language' => $request['language']]);
            } else {
                $i->update(['mobile_path' => $image['path']]);
            }

            return Response::response200($this->systemMessage->uploadFile(), $i);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function deleteMobile($id)
    {
        try {
            $image = SliderImage::findOrFail($id);
            Uploader::deleteFromStorage($image['mobile_path'], 'image', $this->imageDirectory);
            $image->update(['mobile_path' => null]);
            return Response::response200($this->systemMessage->deleteFile());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function deleteSlider($id)
    {
        try {
            $slider = SliderImage::findOrFail($id);
            Uploader::deleteFromStorage($slider['path'], 'image', $this->imageDirectory);
            Uploader::deleteFromStorage($slider['mobile_path'], 'image', $this->imageDirectory);
            $slider->delete();
            return Response::response200($this->systemMessage->deleteFile());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

}
