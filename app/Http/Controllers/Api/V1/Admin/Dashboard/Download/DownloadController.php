<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Download;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\Download\Download;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'download', LanguageHelper::getCacheDefaultLang());
    }

    public function create(Request $request){
        try {
            $serviceData = $request->only(['title']);
            $service = Download::create($serviceData);
            return Response::response200([$this->systemMessage->create()], $service);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

  
    public function update(Request $request, $serviceId, $language)
    {
        try {
            // Find the Download entry
            $serviceTrans = Download::where('id', $serviceId)->firstOrFail();
    
            // Update the title
            $serviceTrans->update([
                'title' => $request['title']
            ]);
            
            return Response::response200([$this->systemMessage->update()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404([$this->systemMessage->error404()]);
            } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function search(Request $request)
    {
        try {
            $query = Download::query();
            // if (isset($request['language'])) {
            //     $query->where('language', $request['language']);
            // }
            if (isset($request['title'])) {
                $query->where('title', 'like', '%' . $request['title'] . '%');
            }

            $serviceTrans = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(10);
            return Response::response200([], $serviceTrans);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($serviceId)
    {
        try {
            Download::where('id', $serviceId)->delete();
            return Response::response200([$this->systemMessage->delete()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->delete());
        } catch (ValidationException $exception) {
            return Response::error400($exception->getMessage());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($serviceId, $language)
    {
        try {
            $serviceTrans = Download::where('id', $serviceId)->firstOrFail();
            return Response::response200([], $serviceTrans);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
