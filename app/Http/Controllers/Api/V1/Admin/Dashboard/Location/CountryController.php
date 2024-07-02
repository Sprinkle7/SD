<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Location;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\Models\Location\CountrySystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Dashboard\Location\AddCountryTranslationRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Location\CreateCountryRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Location\UpdateCountryRequest;
use App\Models\Invoice\Invoice;
use App\Models\Invoice\InvoiceAddress;
use App\Models\Location\Country;
use App\Models\Location\CountryPostDuration;
use App\Models\Location\CountryTranslation;
use App\Models\Order\Order;
use App\Models\Order\OrderAddress;
use App\Models\User;
use App\Models\User\Address;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\In;

class CountryController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new CountrySystemMessage(LanguageHelper::getAppLanguage(\request()),
            'country', LanguageHelper::getCacheDefaultLang());
    }

    public function create(CreateCountryRequest $request)
    {
        try {
            $collection = Country::generateCountryCollection($request);
            $collection['is_active'] = 1;
            $country = Country::create($collection);
            $countryTrans = CountryTranslation::generateCountryTranCollection($request);
            $countryTrans['country_id'] = $country['id'];
            $trans = CountryTranslation::create($countryTrans);
            $trans['country_info'] = $country;
            return Response::response200([$this->systemMessage->create()], $trans);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function addTranslation(AddCountryTranslationRequest $request, $countryId)
    {
        try {
            $countryT = CountryTranslation::where('country_id', $countryId)->where('language', $request['language'])->first();
            if (is_null($countryT)) {
                $countryTrans = CountryTranslation::generateCountryTranCollection($request);
                $countryTrans['country_id'] = $countryId;
                CountryTranslation::create($countryTrans);
            }
            return Response::response200([$this->systemMessage->addTranslation()]);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function update(UpdateCountryRequest $request, $countryId, $language)
    {
        try {
            $countryTrans = CountryTranslation::where('country_id', $countryId)
                ->where('language', $language)->firstOrFail();
            $country = Country::findorFail($countryId);
            $country->update(Country::generateCountryCollection($request));

            $countryTrans->update(CountryTranslation::generateCountryTranCollection($request));
            return Response::response200([$this->systemMessage->update()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function search(Request $request)
    {
        try {
            $query = CountryTranslation::query()->with('country_info');
            if (isset($request['language'])) {
                $query->where('language', $request['language']);
            }

            if (isset($request['name'])) {
                $query->where('name', 'like', '%' . $request['name'] . '%');
            }

            if (isset($request['is_active'])) {
                $query->whereHas('country_info', function ($q) use ($request) {
                    $q->where('is_active', $request['is_active']);
                });
            }

            $countries = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(QueryHelper::perPage($request));

            return Response::response200([], $countries);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($countryId)
    {
        try {
            $country = Country::findOrFail($countryId);
            $address = Address::where('country_id', $countryId)->first();
            $user = User::where('country_id', $countryId)->first();
            $invoice = Invoice::where('country_id', $countryId)->first();
            $invoiceAddress = InvoiceAddress::where('country_id', $countryId)->first();
            $order = Order::where('country_id', $countryId)->first();
            $orderAddress = OrderAddress::where('country_id', $countryId)->first();
            ///check relations of country before deleting
            if (!is_null($address) || !is_null($user) || !is_null($invoice) || !is_null($invoiceAddress) ||
                !is_null($order) || !is_null($orderAddress)) {
                throw new ValidationException($this->systemMessage->unableToDelete());
            }
            CountryTranslation::where('country_id', $countryId)->delete();
            CountryPostDuration::where('country_id', $countryId)->delete();
            $country->delete();
            return Response::response200([$this->systemMessage->delete()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (ValidationException $exception) {
            return Response::error400($exception->getMessage());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($countryId, $language)
    {
        try {
            $country = CountryTranslation::with('country_info')
                ->where('country_id', $countryId)
                ->where('language', $language)->firstOrFail();
            return Response::response200([], $country);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function activate($countryId)
    {
        try {
            $country = Country::findOrFail($countryId);
            $country->update(['is_active' => 1]);
            return Response::response200([$this->systemMessage->activate()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function deactivate($countryId)
    {
        try {
            $country = Country::findOrFail($countryId);
            $country->update(['is_active' => 0]);
            return Response::response200([$this->systemMessage->deactivate()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function addPostMethod(Request $request, $countryId)
    {
        try {
            //first check the country
            Country::findOrFail($countryId);
            $oldPostMethods = CountryPostDuration::where('country_id', $countryId)->get();
            $postMethodCollection = [];
            ///create collection of new shiping info
            foreach ($request['post_methods'] as $postMethod) {
                $postMethodCollection[$postMethod['post_id']] = CountryPostDuration::generateCountryCollection($postMethod);
                $postMethodCollection[$postMethod['post_id']]['country_id'] = $countryId;
            }
            $removedPostMethods = [];
            //compare new shipping info with old ones
            if (count($oldPostMethods) > 0) {
                foreach ($oldPostMethods as $oldPostMethod) {
                    if (isset($postMethodCollection[$oldPostMethod['post_id']])) {
                        //update if shipping info exits in new ones
                        CountryPostDuration::find($oldPostMethod['id'])
                            ->update([
                                'min_price' => $postMethodCollection[$oldPostMethod['post_id']]['min_price'],
                                'price' => $postMethodCollection[$oldPostMethod['post_id']]['price']]);
                        unset($postMethodCollection[$oldPostMethod['post_id']]);
                    } else {
                        //remove if old shipping info is not exist in new ones
                        $removedPostMethods[] = $oldPostMethod['id'];
                    }
                }
                if (count($removedPostMethods)) {
                    ///delete remove ones
                    CountryPostDuration::whereIn('id', $removedPostMethods)->delete();
                }

            }
            if (count($postMethodCollection) > 0) {
                ///insert new ones that not exist yet
                CountryPostDuration::insert($postMethodCollection);
            }
            return Response::response200([$this->systemMessage->attachPostMethod()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }


    public function fetchPostMethod($countryId, $language)
    {
        try {
            //fetch shipping info join with post methods for country
            $query = 'SELECT cpd.id as post_duration_id,cpd.post_id,pmt.title as \'post_method\',cpd.min_price,cpd.price FROM country_post_duration cpd  ' .
                'JOIN post_method_translations pmt ON cpd.post_id=pmt.post_method_id and pmt.language=\'' . $language .
//                '\' JOIN duration_translations dt on cpd.duration_id=dt.duration_id and dt.language=\'' . $language .
                '\'  WHERE country_id = ' . $countryId . ' ORDER BY cpd.price';
            $postMethod = DB::select(DB::raw($query));
            return Response::response200([], $postMethod);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
