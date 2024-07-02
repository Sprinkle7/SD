<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Menu;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Helper\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteImageRequest;
use App\Http\Requests\UploadImageRequest;
use App\Models\Menu\Menu;
use App\Models\Menu\MenuCoverImage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MenuImageController extends Controller
{
    private $systemMessage;
    private $imageDirectory = 'menu';

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'image', LanguageHelper::getCacheDefaultLang());
    }

    public function uploadThumbnailImage(UploadImageRequest $request)
    {
        try {
            $image = Uploader::uploadToStorage($request['image'], 'image', 'temp');
            return Response::response200($this->systemMessage->uploadFile(), $image);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function deleteThumbnailImage(DeleteImageRequest $request)
    {
        try {
            $menu = Menu::find($request['menu_id']);
            $path = '';
            if (Uploader::fileExistInStorage($request['image'], 'image', 'temp')) {
                $path = 'temp';
            } else {
                $path = $this->imageDirectory;
            }
            Uploader::deleteFromStorage($request['image'], 'image', $path);
            if (!is_null($menu)) {
                $menu->update(['thumbnail_image' => null]);
            }
            return Response::response200($this->systemMessage->deleteFile());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function uploadCoverImage(UploadImageRequest $request)
    {
        try {
            $image = Uploader::uploadToStorage($request['image'], 'image', $this->imageDirectory);

            $i = null;
            if (!is_null($request['id'])) {
                $i = MenuCoverImage::find($request['id']);
            }

            if (is_null($i)) {
                $i = MenuCoverImage::create([
                    'path' => $image['path'], 'language' => $request['language']]);
            } else {
                $i->update(['path' => $image['path']]);
            }

            return Response::response200($this->systemMessage->uploadFile(), $i);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function deleteCoverImage($id)
    {
        try {
            $image = MenuCoverImage::findOrFail($id);
            Uploader::deleteFromStorage($image['path'], 'image', $this->imageDirectory);
            $image->update(['path' => null]);
            return Response::response200($this->systemMessage->deleteFile());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function uploadMobileCoverImage(UploadImageRequest $request)
    {
        try {
            $image = Uploader::uploadToStorage($request['image'], 'image', $this->imageDirectory);
            $i = null;
            if (!is_null($request['id'])) {
                $i = MenuCoverImage::find($request['id']);
            }

            if (is_null($i)) {
                $i = MenuCoverImage::create([
                    'mobile_path' => $image['path'], 'language' => $request['language']]);
            } else {
                $i->update(['mobile_path' => $image['path']]);
            }

            return Response::response200($this->systemMessage->uploadFile(), $i);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function deleteMobileCoverImage($id)
    {
        try {
            $image = MenuCoverImage::findOrFail($id);
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
            $slider = MenuCoverImage::findOrFail($id);
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
