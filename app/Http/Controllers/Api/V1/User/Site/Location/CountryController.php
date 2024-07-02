<?php

namespace App\Http\Controllers\Api\V1\User\Site\Location;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\Models\Location\CountrySystemMessage;
use App\Http\Controllers\Controller;
use App\Models\Location\Country;
use App\Models\Location\CountryTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new CountrySystemMessage(LanguageHelper::getAppLanguage(\request()),
            'country', LanguageHelper::getCacheDefaultLang());
    }

    public function search(Request $request)
    {
        try {
            $language = LanguageHelper::getAppLanguage($request);
            $query = CountryTranslation::query()->with('country_info')
                ->where('language', $language)->whereHas('country_info', function ($q) {
                $q->where('is_active', 1);
            });

            if (isset($request['name'])) {
                $query->where('name', 'like', '%' . $request['name'] . '%');
            }

            $countries = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(QueryHelper::perPage($request));

            return Response::response200([], $countries);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetchPostMethod($countryId)
    {
        try {

            ///shipping info
            $language = LanguageHelper::getAppLanguage(\request());
            $location['country'] = CountryTranslation::query()->with('country_info')
                ->where('language', $language)->where('country_id', $countryId)->first();
            $query = 'SELECT cpd.id as post_duration_id,cpd.post_id,pmt.title as \'post_method\',cpd.min_price,cpd.price FROM country_post_duration cpd  ' .
                'JOIN post_method_translations pmt ON cpd.post_id=pmt.post_method_id and pmt.language=\'' . $language .
                '\'  WHERE country_id = ' . $countryId . ' ORDER BY cpd.price';
            $location['post_method'] = DB::select(DB::raw($query));
            return Response::response200([], $location);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

}
