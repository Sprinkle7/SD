<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Option;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Dashboard\Option\AddTranslationOptionValueRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Option\CreateOptionValueRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Option\UpdateOptionValueRequest;
use App\Models\Option\OptionValue;
use App\Models\Option\OptionValueTranslation;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OptionValueController extends Controller
{
    //this in not in user
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'option_value', LanguageHelper::getCacheDefaultLang());
    }

    public function create(CreateOptionValueRequest $request, $optionId)
    {
        try {
            $optValTranslates = [];
            foreach ($request['option_values'] as $index => $value) {
                $optVal = OptionValue::create(['option_id' => $optionId]);
                $optValTranslates[$index] = OptionValueTranslation::generateOptionTransCollection($value);
                $optValTranslates[$index]['language'] = $request['language'];
                $optValTranslates[$index]['option_id'] = $optionId;
                $optValTranslates[$index]['option_value_id'] = $optVal['id'];
            }

            OptionValueTranslation::insert($optValTranslates);

            return Response::response200([$this->systemMessage->create()]);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function addTranslation(AddTranslationOptionValueRequest $request, $optionId)
    {
        try {
            $optValTranslates = [];
            foreach ($request['option_values'] as $index => $value) {
                $optValTranslates[$index] = OptionValueTranslation::generateOptionTransCollection($value);
                $optValTranslates[$index]['language'] = $request['language'];
                $optValTranslates[$index]['option_id'] = $optionId;
                $optValTranslates[$index]['option_value_id'] = $value['option_value_id'];
            }

            OptionValueTranslation::insert($optValTranslates);
            return Response::response200([$this->systemMessage->addTranslation()]);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function update(UpdateOptionValueRequest $request, $optionId, $language)
    {
        try {
            $optValTranslates = [];
            foreach ($request['option_values'] as $index => $value) {
                if (isset($value['id'])) {
                    $optVal = OptionValueTranslation::find($value['id']);
                    $optVal->update(['title' => $value['title']]);
                } else {
                    $optVal = [];
                    if (isset($value['option_value_id'])) {
                        $optVal['id'] = $value['option_value_id'];
                    } else {
                        $optVal = OptionValue::create(['option_id' => $optionId]);
                    }
                    $optValTranslates[$index] = OptionValueTranslation::generateOptionTransCollection($value);
                    $optValTranslates[$index]['language'] = $language;
                    $optValTranslates[$index]['option_id'] = $optionId;
                    $optValTranslates[$index]['option_value_id'] = $optVal['id'];
                }
            }
            OptionValueTranslation::insert($optValTranslates);

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

            return Response::response200([]);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($optionValueId)
    {
        try {
            $relation = DB::table('option_value_product')->where('option_value_id',$optionValueId)->first();

            if (!is_null($relation)) {
                throw new ValidationException($this->systemMessage->unableToDelete());

            }
            OptionValue::where('id', $optionValueId)->delete();
            OptionValueTranslation::where('option_value_id', $optionValueId)->delete();
            return Response::response200([$this->systemMessage->delete()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->delete());
        } catch (ValidationException $exception) {
            return Response::error400($exception->getMessage());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($optionId, $language)
    {
        try {
            $option = OptionValueTranslation::where('option_id', $optionId)->where('language', $language)->get();

            return Response::response200([], $option);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
