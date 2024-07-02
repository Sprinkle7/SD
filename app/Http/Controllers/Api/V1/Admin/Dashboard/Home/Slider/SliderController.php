<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Home\Slider;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Helper\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Dashboard\Home\Slider\CreateSliderRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Home\Slider\UpdateSliderRequest;
use App\Models\Home\Slider\Slider;
use App\Models\Home\Slider\SliderImage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'slider', LanguageHelper::getCacheDefaultLang());
    }

    public function create(CreateSliderRequest $request)
    {
        try {
            $slider = Slider::create(['title' => $request['title']]);
            $x =0; 
            foreach ($request['images_id'] as $image) {
                SliderImage::where('id', $image['id'])
                    ->update(['slider_id' => $slider['id'], 'link' => $image['link'],'sorting' => $x++]);
            }
            return Response::response200($this->systemMessage->create(), $slider);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }


    // public function update(UpdateSliderRequest $request, $id)
    // {
    //     try {
    //         $slider = Slider::findOrFail($id);
    //         $slider->update(['title' => $request['title']]);
    //         $existingImages = $slider->images()->get()->keyBy('id'); 
    //         $slider->images()->delete();
    //         foreach ($request['images_id'] as $index => $image) {
    //             $existingImage = $existingImages->get($image['id']);
    //             if ($existingImage) {
    //                 SliderImage::create([
    //                     'slider_id' => $slider['id'],
    //                     'path' => $existingImage['path'],
    //                     'mobile_path' => $existingImage['mobile_path'],
    //                     'link' => $existingImage['link'],
    //                     'language' => 'de' 
    //                 ]);
    //             }
    //         }

    //         return Response::response200($this->systemMessage->update());
    //     } catch (ModelNotFoundException $exception) {
    //         return Response::error404($this->systemMessage->error404());
    //     } catch (\Exception $exception) {
    //         return Response::error500($this->systemMessage->error500());
    //     }
    // }

    // public function update(UpdateSliderRequest $request, $id)
    // {
    //     try {
    //         $slider = Slider::findOrFail($id);
    //         $slider->update(['title' => $request['title']]);
    //         $existingImages = $slider->images()->get()->toArray();
    
    //         $slider->images()->delete();
    //         foreach ($request['images_id'] as $index => $image) {
    //             $existingImage = collect($existingImages)->firstWhere('id', $image['id']);
    //             if ($existingImage) {
    //                 SliderImage::create([
    //                     'slider_id' => $slider['id'],
    //                     'path' => $existingImage['path'],
    //                     'mobile_path' => $existingImage['mobile_path'],
    //                     'link' => $existingImage['link'],
    //                     'language' => 'de' 
    //                 ]);
    //             }
    //         }
    
    //         return Response::response200($this->systemMessage->update());
    //     } catch (ModelNotFoundException $exception) {
    //         return Response::error404($this->systemMessage->error404());
    //     } catch (\Exception $exception) {
    //         return Response::error500($this->systemMessage->error500());
    //     }
    // }

    public function update(UpdateSliderRequest $request, $id)
    {
        try {
            $slider = Slider::findOrFail($id);
            $slider->update(['title' => $request['title']]);
            foreach ($request['images_id'] as $image) {
                SliderImage::where('id', $image['id'])
                    ->update(['slider_id' => $slider['id'], 'link' => $image['link'], 'sorting' => $image['sorting']]);
            }
            return Response::response200($this->systemMessage->update());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function search(Request $request)
    {
        try {
            $query = Slider::query();
            if (isset($request['title'])) {
                $query->where('title', 'like', '%' . $request['title'] . '%');
            }

            $sliders = $query->orderBy('id', 'ASC')->paginate(QueryHelper::perPage($request));

            return Response::response200([], $sliders);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($id)
    {
        try {
            $slider = Slider::with('images')->findOrFail($id);

            foreach ($slider['images'] as $image) {
                Uploader::deleteFromStorage($image['path'], 'image', 'home/slider');
                Uploader::deleteFromStorage($image['mobile_path'], 'image', 'home/slider');
            }
            SliderImage::where('slider_id', $id)->delete();
            $slider->delete();
            return Response::response200($this->systemMessage->delete());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($id, $language)
    {
        try {
            $slider = Slider::with(['images' => function ($query) use ($language) {
                $query->where('language', $language);
            }])->findOrFail($id);
            return Response::response200([], $slider);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
