<?php

namespace App\Http\Controllers\Api\V1\User\Site\Cart;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\Models\Cart\CartSystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\Site\Address\CreateAddressRequest;
use App\Models\Location\Country;
use App\Models\Location\CountryTranslation;
use App\Models\User;
use App\Models\User\Address;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function Sodium\add;

class AddressController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new CartSystemMessage(LanguageHelper::getAppLanguage(\request()),
            'address', LanguageHelper::getCacheDefaultLang());
    }

    public function fetchDefaultAddress()
    {
        try {
            $language = LanguageHelper::getAppLanguage(\request());
            $address = Address::fetchDefaultAddress();
            if (!is_null($address)) {
                $address['country'] = CountryTranslation::with('country_info')
                    ->where('language', $language)->where('country_id', $address['country_id'])->first();
            }
            return Response::response200([], $address);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function create(CreateAddressRequest $request)
    {
        try {
            $user = auth()->user();
            $userId = $user->id;

            User::ProfileIsComplete($user);

            $addressCollection = Address::generateAddressCollection($request);
            $addressCollection['user_id'] = $userId;
            $address = Address::create($addressCollection);
            return Response::response200($this->systemMessage->create(), $address);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (ValidationException $exception) {
            return Response::error400($exception->getMessage());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function update(CreateAddressRequest $request, $id)
    {
        try {
            $user = auth()->user();
            $userId = $user->id;
            $address = Address::where('user_id', $userId)->findOrFail($id);

            $addressCollection = Address::generateAddressCollection($request);
            $addressCollection['user_id'] = $userId;

            $address->update($addressCollection);
            return Response::response200($this->systemMessage->update(), $address);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function search(Request $request)
    {
        try {
            $userId = auth()->user()->id;
            $language = LanguageHelper::getAppLanguage($request);
            $addresses = Address::with(['country', 'country.translation' => function ($query) use ($language) {
                $query->where('language', $language);
            }])->where('user_id', $userId)->paginate(QueryHelper::perPage($request));
            return Response::response200([], $addresses);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($id)
    {
        try {
            $userId = auth()->user()->id;
            $addresses = Address::where('user_id', $userId)->findOrFail($id);
            $addresses->delete();
            return Response::response200([$this->systemMessage->delete()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($id)
    {
        try {
            $userId = auth()->user()->id;
            $address = Address::where('user_id', $userId)->findOrFail($id);
            return Response::response200([], $address);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }


}
