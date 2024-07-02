<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Popup;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Dashboard\Popup\AddTranlationPopupRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Popup\CreatePopupRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Popup\UpdatePopupRequest;
use App\Models\Popup\Popup;
use App\Models\Popup\PopupTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PopupController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'popup', LanguageHelper::getCacheDefaultLang());
    }

    public function create(CreatePopupRequest $request)
    {
        try {
            $popup = Popup::create(['expires_at' => $request['expires_at']]);
            $collection = PopupTranslation::generateCollection($request);
            $collection['popup_id'] = $popup['id'];
            $popupTranslation = PopupTranslation::create($collection);
            return Response::response200($this->systemMessage->create(), $popupTranslation);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function addTranslation(AddTranlationPopupRequest $request, $popupId)
    {
        try {
            $popT = PopupTranslation::where('popup_id', $popupId)
                ->where('language', $request['language'])->first();
            if (is_null($popT)) {
                $collection = PopupTranslation::generateCollection($request);
                $collection['popup_id'] = $popupId;
                PopupTranslation::create($collection);
            }
            return Response::response200([$this->systemMessage->addTranslation()]);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function update(UpdatePopupRequest $request, $id, $language)
    {
        try {
            $popup = Popup::findorFail($id);
            $popupT = PopupTranslation::where('popup_id', $id)
                ->where('language', $language)->firstOrFail();
            $collection = PopupTranslation::generateCollection($request);
            unset($collection['language']);
            $popup->update(['expires_at' => $request['expires_at']]);
            $popupT->update($collection);
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
            $query = PopupTranslation::query()
                ->select('popup_id', 'title', 'language')
                ->with('popupInfo:id,expires_at,is_active');

            if (isset($request['language'])) {
                $query->where('language', $request['language']);
            }

            if (isset($request['title'])) {
                $query->where('title', 'like', '%' . $request['title'] . '%');
            }

            $popupTranslations = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(QueryHelper::perPage($request));


            return Response::response200([], $popupTranslations);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($id)
    {
        try {
            $popup = Popup::findOrFail($id);
            PopupTranslation::where('popup_id', $id)->delete();
            $popup->delete();
            return Response::response200([$this->systemMessage->delete()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($id, $language)
    {
        try {
            $popupTranslation = PopupTranslation::with('popupInfo')->where('popup_id', $id)
                ->where('language', $language)->firstOrFail();
            return Response::response200([], $popupTranslation);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function activate($id)
    {
        try {
            $popup = Popup::findOrFail($id);
            if ($popup['is_active'] == 0) {
                Popup::where('is_active', 1)->update(['is_active' => 0]);
                $popup->update(['is_active' => 1]);
            }
            return Response::response200([], $this->systemMessage->activate());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function deactivate($id)
    {
        try {
            $popup = Popup::findOrFail($id);
            if ($popup['is_active'] == 1) {
                $popup->update(['is_active' => 0]);
            }
            return Response::response200([], $this->systemMessage->deactivate());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
