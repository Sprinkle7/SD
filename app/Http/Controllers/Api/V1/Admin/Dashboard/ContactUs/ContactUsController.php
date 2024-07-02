<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\ContactUs;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Dashboard\ContactUs\UpdateContactUsRequest;
use App\Models\ContactUs\ContactUs;
use App\Models\ContactUs\ContactUsTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'contact_us', LanguageHelper::getCacheDefaultLang());
    }

    public function update(UpdateContactUsRequest $request)
    {
        try {
            $contact = ContactUs::first();
            $contactT = [];
            if (is_null($contact)) {
                $contact = ContactUs::create([]);
                $contactT = ContactUsTranslation::create(['contact_us_id' => $contact['id'],
                    'description' => $request['description'], 'language' => $request['language']]);
            } else {
                $contactT = ContactUsTranslation::where('contact_us_id', $contact['id'])
                    ->where('language', $request['language'])->first();
                if (is_null($contactT)) {
                    $contactT = ContactUsTranslation::create(['contact_us_id' => $contact['id'],
                        'description' => $request['description'], 'language' => $request['language']]);
                } else {
                    $contactT->update(['description' => $request['description']]);
                }
            }
            return Response::response200([$this->systemMessage->update()], $contactT);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($language)
    {
        try {
            $contact = ContactUsTranslation::where('language', $language)->firstOrFail();
            return Response::response200([], $contact);

        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
