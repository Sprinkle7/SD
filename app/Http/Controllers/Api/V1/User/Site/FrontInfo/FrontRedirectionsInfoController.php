<?php

namespace App\Http\Controllers\Api\V1\User\Site\FrontInfo;

use App\Helper\Response\Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FrontRedirectionsInfoController extends Controller
{
    public function fetch()
    {
        try {
            $data = null;
            if (Storage::disk('local')->exists('front\frontRedirections.txt')) {
                $data = Storage::disk('local')->get('front\frontRedirections.txt');
            }
            return Response::response200([], ['info' => json_decode($data)]);
        } catch (\Exception $exception) {
            return Response::error500($exception->getMessage());
        }
    }
}
