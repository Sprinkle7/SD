<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Product;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Helper\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadImageRequest;
use App\Models\Product\Combination\CombinationImage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CombinationFileController extends Controller
{
    private $systemMessage;
    private $imageDirectory = 'product/combination';

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'image', LanguageHelper::getCacheDefaultLang());
    }


    public function uploadCombinationImage(UploadImageRequest $request, $combinationId)
    {
        try {
            $image = Uploader::uploadToStorage($request['image'], 'image', $this->imageDirectory);
            $image['combination_id'] = $combinationId;
            $image = CombinationImage::create($image);
            return Response::response200($this->systemMessage->uploadFile(), $image);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function deleteCombinationImage(CombinationImage $image)
    {
        try {

            Uploader::deleteFromStorage($image['path'], 'image', $this->imageDirectory);
            $image->delete();
            return Response::response200($this->systemMessage->deleteFile());
        } catch (ModelNotFoundException $exception) {
            return Response::error400($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function combinationImageArrange(Request $request)
    {
        try {
            foreach ($request['images'] as $image) {
                $p = CombinationImage::find($image['id']);
                $p->update(['arrange' => $image['arrange']]);
            }
            return Response::response200([$this->systemMessage->update()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
