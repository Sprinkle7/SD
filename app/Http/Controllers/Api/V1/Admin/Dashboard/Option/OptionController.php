<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Option;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Dashboard\Option\AddTranslationOptionRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Option\CreateOptionRequest;
use App\Http\Requests\Api\V1\Admin\Dashboard\Option\UpdateOptionRequest;
use App\Models\Option\Option;
use App\Models\Option\OptionTranslation;
use App\Models\Option\OptionValue;
use App\Models\Option\OptionValueTranslation;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OptionController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'option', LanguageHelper::getCacheDefaultLang());
    }

    public function create(CreateOptionRequest $request)
    {
        try {
            //option and its values are created together
            $option = Option::create([]);
            $optTranslate = OptionTranslation::generateOptionTransCollection($request);
            $optTranslate['option_id'] = $option['id'];
            $optionTrans = OptionTranslation::create($optTranslate);

            $optValTranslates = [];
            foreach ($request['option_values'] as $index => $value) {
                $optVal = OptionValue::create(['option_id' => $option['id']]);
                $optValTranslates[$index] = OptionValueTranslation::generateOptionTransCollection($value);
                $optValTranslates[$index]['language'] = $request['language'];
                $optValTranslates[$index]['option_id'] = $option['id'];
                $optValTranslates[$index]['option_value_id'] = $optVal['id'];
            }

            OptionValueTranslation::insert($optValTranslates);

            return Response::response200([$this->systemMessage->create()], $optionTrans);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function addTranslation(AddTranslationOptionRequest $request, $optionId)
    {
        try {
            $optionTrans = OptionTranslation::generateOptionTransCollection($request);
            $optionTrans['option_id'] = $optionId;
            OptionTranslation::create($optionTrans);

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

    public function update(UpdateOptionRequest $request, $optionId, $language)
    {
        try {
            $optionTrans = OptionTranslation::where('option_id', $optionId)->where('language', $language)->firstOrFail();
            $optionTrans->update(['title' => $request['title']]);

            $optValTranslates = [];
            /**
             * in this loop check if the value is new one or the old on
             * if this new it's just update the title
             * else the values id created with its translation
             */
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
            $query = OptionTranslation::query();
            if (isset($request['language'])) {
                $query->where('language', $request['language']);
            }
            if (isset($request['title'])) {
                $query->where('title', 'like', '%' . $request['title'] . '%');
            }
            if (isset($request['id'])) {
                $query->where('id', $request['id']);
            }

            $optionTrans = $query->orderBy('id', isset($request['orderBy']) ? $request['orderBy'] : 'DESC')
                ->paginate(QueryHelper::perPage($request));
            return Response::response200([], $optionTrans);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function delete($optionId)
    {
        try {

            // the delete action is possible if there is no usage the option in products
            $relation = DB::table('option_value_product')->where('option_id',$optionId)->first();

            if (!is_null($relation)) {
                throw new ValidationException($this->systemMessage->unableToDelete());

            }
            Option::where('id', $optionId)->firstOrFail()->delete();
            OptionTranslation::where('option_id', $optionId)->delete();
            OptionValue::where('option_id', $optionId)->delete();
            OptionValueTranslation::where('option_id', $optionId)->delete();

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
            $optionTrans = OptionTranslation::with(['optionValues' => function ($query) use ($language) {
                $query->where('language', $language);

            }])->where('option_id', $optionId)
                ->where('language', $language)
                ->firstOrFail();
            return Response::response200([], $optionTrans);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
