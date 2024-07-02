<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Download;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Helper\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Models\Download\DownloadFiles;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DownloadFilesController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'download', LanguageHelper::getCacheDefaultLang());
    }

    public function create(Request $request)
    {
        try {
            $serviceData = $request->only(['title','image','download_id']);
            $service = DownloadFiles::create($serviceData);
            return Response::response200([$this->systemMessage->create()], $service);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

  
    public function update(Request $request, $serviceId, $language)
    {
        try {
            // Find the Download entry
            $serviceTrans = DownloadFiles::where('id', $serviceId)->firstOrFail();
    
            // Update the title
            $serviceTrans->update([
                'title' => $request['title'],
                'image' => $request['image'],
                'download_id' => $request['download_id']
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
            $query = DownloadFiles::query();
            if (isset($request['service_id'])) {
                $query->where('download_id', $request['service_id']);
            }
            if (isset($request['title'])) {
                $query->where('title', 'like', '%' . $request['title'] . '%');
            }

            $serviceTrans = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(10);
            return Response::response200([], $serviceTrans);
        } catch (\Exception $exception) {
             // Log::error('Error creating download:', [
            //     'error' => $exception->getMessage(),
            //     'trace' => $exception->getTraceAsString()
            // ]);

            // dd($exception);
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($serviceId)
    {
        try {
            DownloadFiles::where('id', $serviceId)->delete();
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
            $serviceTrans = DownloadFiles::where('id', $serviceId)->firstOrFail();
            return Response::response200([], $serviceTrans);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
