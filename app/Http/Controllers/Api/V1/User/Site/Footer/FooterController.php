<?php

namespace App\Http\Controllers\Api\V1\User\Site\Footer;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Models\Footer\FooterSectionTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class FooterController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'footer', LanguageHelper::getCacheDefaultLang());
    }

    public function fetch()
    {
        try {

            $language = LanguageHelper::getAppLanguage(\request());
            $pageSection = FooterSectionTranslation::with(['pages' => function ($q) use ($language) {
                $q->select(['id', 'footer_section_page.page_id','slug', 'title', 'language','footer_alias'])
                    ->where('language', $language)->orderBy('arrange');
            }])->where('language', $language)->where('type', 'page')->get()->toArray();
            $menuSection = FooterSectionTranslation::with([
                'menusInfo' => function ($q) {
                    $q->select(['id', 'level', 'parent_id'])->orderBy('footer_section_menu.arrange');
                },
                'menusInfo.menuT' => function ($q) use ($language) {
                    $q->select(['id', 'menu_id', 'title', 'language'])->where('language', $language);
                },
                'menusInfo.parent:id',
                'menusInfo.parent.menuT' => function ($q) use ($language) {
                    $q->select(['id', 'menu_id', 'title', 'language'])->where('language', $language);
                },
            ])->where('language', $language)->where('type', 'menu')->get()->toArray();

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
