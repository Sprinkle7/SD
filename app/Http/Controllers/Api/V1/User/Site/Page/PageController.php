<?php

namespace App\Http\Controllers\Api\V1\User\Site\Page;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use App\Http\Controllers\Controller;
use App\Models\Page\PageSidebarItems;
use App\Models\Page\PageTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new SystemMessage(LanguageHelper::getAppLanguage(\request()),
            'page', LanguageHelper::getCacheDefaultLang());
    }

    public function fetch($slug)
    {
        try {
            $language = LanguageHelper::getAppLanguage(\request());
            Log::info('Fetching page with slug: ' . $slug . ' and language: ' . $language);

            $page = PageTranslation::with('pageInfo:id,sidebar_id')
                ->where('slug', $slug)
                ->where('language', $language)
                ->firstOrFail();

            Log::info('Page found: ' . json_encode($page));

            if (!is_null($page['pageInfo']['sidebar_id'])) {
                $sidebar = PageSidebarItems::with(['pages' => function ($q) use ($language) {
                    $q->select(['id', 'page_id', 'slug', 'title', 'language', 'footer_alias'])
                      ->where('language', $language);
                }])->where('sidebar_id', $page['pageInfo']['sidebar_id'])
                  ->orderBy('arrange')
                  ->get();

                $page['sidebar'] = $sidebar;
            }

            return Response::response200([], $page);

        } catch (ModelNotFoundException $exception) {
            Log::error('Page not found with slug: ' . $slug . ' and language: ' . $language);
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            Log::error('An error occurred: ' . $exception->getMessage());
            return Response::error500($this->systemMessage->error500());
        }
    }

}
