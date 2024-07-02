<?php

namespace App\Http\Controllers\Api\V1\Admin\Dashboard\Footer;

use App\Helper\Database\QueryHelper;
use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Dashboard\Footer\UpdateFooterRequest;
use App\Models\Footer\FooterSectionTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FooterController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'footer', LanguageHelper::getCacheDefaultLang());
    }


    public function update(UpdateFooterRequest $request, $language)
    {
        try {
            foreach ($request['footer_sections'] as $section) {
                $sec = FooterSectionTranslation::updateOrCreate([
                    'footer_section_id' => $section['footer_section_id'],
                    'language' => $language
                ],
                    ['title' => $section['title']]
                );

                FooterSectionTranslation::where('footer_section_id', $section['footer_section_id'])
                    ->update(['type' => $section['type'], 'arrange' => $section['arrange']]);

                $items = [];
                if (count($section['items']) > 0) {
                    array_walk($section['items'], function (&$data, $key) use (&$items) {
                        $items[$data['id']] = ['arrange' => $data['arrange']];
                    });
                }

                if ($section['type'] == 'menu') {
                    $sec->pages()->sync([]);
                    $sec->menus()->sync($items);
                } else if ($section['type'] == 'page') {
                    $sec->pages()->sync($items);
                    $sec->menus()->sync([]);
                } else {
                    $sec->pages()->sync([]);
                    $sec->menus()->sync([]);
                }
            }
            return Response::response200($this->systemMessage->update());
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetch($language)
    {
        try {
            $pageSection = FooterSectionTranslation::with(['pages' => function ($q) use ($language) {
                $q->select(['id', 'footer_section_page.page_id', 'title', 'language'])
                    ->where('language', $language)->orderBy('arrange');
            }])->where('language', $language)->where('type', 'page')->get()->toArray();
            $menuSection = FooterSectionTranslation::with(['menus' => function ($q) use ($language) {
                $q->select(['id', 'footer_section_menu.menu_id', 'title', 'language'])
                    ->where('language', $language)->orderBy('arrange');
            }])->where('language', $language)->where('type', 'menu')->get()->toArray();
            $nullSections = FooterSectionTranslation::where('language', $language)->where('type', null)
                ->get()->toArray();
            array_push($pageSection, ...$menuSection);
            array_push($pageSection, ...$nullSections);
            usort($pageSection, function ($a, $b) {
                return $a['arrange'] > $b['arrange'];
            });
            return Response::response200([], $pageSection);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
