<?php

namespace App\Http\Controllers\Api\V1\User\Site\Holiday;

use App\Helper\Response\Response;
use App\Http\Controllers\Controller;
use App\Models\Holiday\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function checkHoliday(Request $request,$day)
    {
        try {
            $to = now()->startOfDay()->addDays($day);
            $holiday = Holiday::select('date')->where('date', '>=', now()->startOfDay())->where('date', '<=', $to)->orderBy('date')->pluck('date');
            return Response::response200([], ['holidays' => $holiday]);
        } catch (\Exception $exception) {
            return Response::error500();
        }
    }
}
