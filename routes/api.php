<?php


use App\Models\Product\Pivot\Type1\Pt1Combination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('test', function () {
//    \Illuminate\Support\Facades\Cache::put('test', 'test');
    echo \Illuminate\Support\Facades\Cache::get('test');
});


/**
 * admin dashboard apis v1
 */
Route::group([
    'prefix' => 'v1/admin/dashboard',
    'namespace' => '\App\Http\Controllers\Api\V1\Admin\Dashboard',
], function () {

    /*
     * admin auth apis
     */
    Route::group([
        'prefix' => 'auth',
        'namespace' => 'Auth',
    ], function () {
        Route::post('/login', 'AuthController@login');
    });

    /**
     * admin dashboard apis
     */
    Route::group([
        'middleware' => ['auth:api']
    ], function () {
        Route::group([
            'prefix' => 'users',
            'namespace' => 'User',
            'middleware' => ['isAdmin:1']
        ], function () {
            Route::post('/create', 'UserController@create');
            Route::patch('/update/{id}', 'UserController@update');
            Route::get('/search', 'UserController@search');
            Route::delete('/delete/{id}', 'UserController@delete');
            Route::get('/fetch/{id}', 'UserController@fetch');
        });

        Route::group([
            'prefix' => 'roles',
            'namespace' => 'User',
            'middleware' => ['isAdmin:1']
        ], function () {
            Route::post('/create/translation/{roleId}', 'RoleController@addTranslation');
            Route::patch('/update/translation/{roleId}/{language}', 'RoleController@update');
            Route::get('/search', 'RoleController@search');
            Route::get('/fetch/translation/{roleId}/{language}', 'RoleController@fetch');
        });

        Route::group([
            'prefix' => 'languages',
            'namespace' => 'Language',
            'middleware' => ['isAdmin:1']
        ], function () {
            Route::post('/create', 'LanguageController@create');
            Route::patch('/update/{id}', 'LanguageController@update');
            Route::get('/search', 'LanguageController@search');
            Route::delete('/delete/{id}', 'LanguageController@delete');
            Route::get('/fetch/{id}', 'LanguageController@fetch');
            Route::get('/activate/{id}', 'LanguageController@activate');
            Route::get('/deactivate/{id}', 'LanguageController@deactivate');
            Route::get('/set-default/{id}', 'LanguageController@setDefault');
            Route::get('/app/languages', 'LanguageController@appLanguages');
            Route::get('/suggestion', 'LanguageController@suggestLanguage');
        });

        Route::group([
            'prefix' => 'categories',
            'namespace' => 'Category',
        ], function () {
            Route::group([
                'middleware' => ['isAdmin:1,2']
            ], function () {
                Route::post('/create', 'CategoryController@create');
                Route::post('/add/translation/{categoryId}', 'CategoryController@addTranslation');
                Route::patch('/update/{categoryId}/{language}', 'CategoryController@update');
                Route::get('/search', 'CategoryController@search');
                Route::get('/fetch/{categoryId}/{language}', 'CategoryController@fetch');
            });
            Route::group([
                'middleware' => ['isAdmin:1']
            ], function () {
                Route::delete('/delete/{categoryId}', 'CategoryController@delete');
            });
        });

        Route::group([
            'prefix' => 'contact-us',
            'namespace' => 'ContactUs',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::post('/update', 'ContactUsController@update');
            Route::get('/fetch/{language}', 'ContactUsController@fetch');
        });

        Route::group([
            'prefix' => 'about-us',
            'namespace' => 'AboutUs',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::post('/update', 'AboutUsController@update');
            Route::get('/fetch/{language}', 'AboutUsController@fetch');
        });

        Route::group([
            'prefix' => 'setting/tax',
            'namespace' => 'Setting',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::post('/add', 'TaxController@create');
            Route::post('/update', 'TaxController@update');
            Route::get('/fetch', 'TaxController@fetch');
        });

        Route::group([
            'prefix' => 'setting/production-delay',
            'namespace' => 'Setting',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::post('/update', 'ProductionDelayController@update');
            Route::get('/fetch', 'ProductionDelayController@fetch');
        });

        Route::group([
            'prefix' => 'countries',
            'namespace' => 'Location',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::post('/create', 'CountryController@create');
            Route::post('/add/translation/{countryId}', 'CountryController@addTranslation');
            Route::post('/attach/post-methods/{countryId}', 'CountryController@addPostMethod');
            Route::get('/fetch/post-methods/{countryId}/{language}', 'CountryController@fetchPostMethod');
            Route::patch('/update/{countryId}/{language}', 'CountryController@update');
            Route::get('/search', 'CountryController@search');
            Route::delete('/delete/{countryId}', 'CountryController@delete');
            Route::get('/fetch/{countryId}/{language}', 'CountryController@fetch');
            Route::get('/activate/{countryId}', 'CountryController@activate');
            Route::get('/deactivate/{countryId}', 'CountryController@deactivate');
        });

        Route::group([
            'prefix' => 'menus',
            'namespace' => 'Menu',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::post('/create', 'MenuController@create');
            Route::post('/add/translation/{menuId}', 'MenuController@addTranslation');
            Route::patch('/update/{menuId}/{language}', 'MenuController@update');
            Route::get('/get_all', 'MenuController@get_all');
            Route::get('/get_all/{menuId}', 'MenuController@getbycategory');
            Route::get('/search', 'MenuController@search');
            Route::delete('/delete/{menuId}', 'MenuController@delete');
            Route::get('/fetch/{menuId}/{language}', 'MenuController@fetch');
            Route::get('/activate/{id}', 'MenuController@activate');
            Route::get('/deactivate/{id}', 'MenuController@deactivate');
            Route::post('/attach/products/{menuId}', 'MenuController@attachProducts');
            Route::get('/fetch/products/{menuId}/{language}', 'MenuController@fetchProducts');
            Route::post('/update/arrange', 'MenuController@updateArrange');

            Route::group([
                'prefix' => 'images',
            ], function () {
                Route::post('/upload/thumbnail', 'MenuImageController@uploadThumbnailImage');
                Route::delete('/delete/thumbnail', 'MenuImageController@deleteThumbnailImage');
                Route::post('/upload/cover', 'MenuImageController@uploadCoverImage');
                Route::delete('/delete/cover/{id}', 'MenuImageController@deleteCoverImage');
                Route::post('/upload/mobile/cover', 'MenuImageController@uploadMobileCoverImage');
                Route::delete('/delete/mobile/cover/{id}', 'MenuImageController@deleteMobileCoverImage');

                Route::delete('/delete/slider/{id}', 'MenuImageController@deleteSlider');

            });
        });

        Route::group([
            'prefix' => 'products',
            'namespace' => 'Product',
        ], function () {
            Route::get('/duplicate/{productId}', 'ProductController@duplicate');
            Route::post('/create', 'ProductController@create');
            Route::post('/add/translation/{productId}', 'ProductController@addTranslation');
            Route::patch('/update/{productId}/{language}', 'ProductController@update');
            Route::delete('/delete/{productId}', 'ProductController@delete');
            Route::get('/fetch/{productId}/{language}', 'ProductController@fetch');
            Route::get('/search', 'ProductController@search');
            Route::get('activate/{productId}', 'ProductController@activate');
            Route::get('deactivate/{productId}', 'ProductController@deactivate');
            Route::post('attach/technical-info/{productId}', 'ProductController@attachTechnicalInfo');
            Route::get('get/technical-info/{productId}', 'ProductController@fetchTechnicalInfo');
            Route::post('attach/portfolio/{productId}', 'ProductController@attachPortfolio');
            Route::get('get/portfolio/{productId}', 'ProductController@fetchPortfolio');
            Route::post('attach/discount/{productId}', 'ProductController@attachDiscount');
            Route::get('all/discount/{productId}', 'ProductController@allDiscounts');
            Route::delete('delete/discount/{discountId}', 'ProductController@deleteDiscount');
            Route::post('/attach/menus/{product}', 'ProductController@attachMenu');
            Route::get('/get/menus/{product}', 'ProductController@getMenu');
            Route::post('/attach/working-days/{productId}', 'ProductController@updateWorkingDay');
            Route::get('/get/working-days/{productId}', 'ProductController@getWorkingDay');
            Route::post('/attach/services/{product}', 'ProductController@attachService');
            Route::get('/get/services/{product}', 'ProductController@getService');
            Route::post('/exclude/services/{product}', 'ProductController@excludeServices');
            Route::get('/exclude/services/{product}', 'ProductController@fetchExcludes');
            /**
             * product options
             */

            Route::group([
                'prefix' => 'option',
            ], function () {
                Route::post('/attach/{product}/{language}', 'ProductOptionController@attachOption');
                Route::post('/add/translation/{product}/{language}', 'ProductOptionController@addOptionTranslation');
                Route::get('/fetch/{product}/{language}', 'ProductOptionController@fetchOption');
                Route::get('/fetch/with-values/{product}/{language}', 'ProductOptionController@fetchOptionWithTitle');

            });

            /**
             * product option values
             */

            Route::group([
                'prefix' => 'option-values',
            ], function () {
                Route::post('/attach/{product}', 'ProductOptionController@attachOptionValue');
                Route::get('/fetch/{product}/{language}', 'ProductOptionController@fetchOptionValue');
                Route::post('/exclude/{product}', 'ProductOptionController@excludeOptionValues');
                Route::get('/exclude/{product}', 'ProductOptionController@fetchExcludes');

            });

            /**
             * product combinations
             */

            Route::group([
                'prefix' => 'combinations',
            ], function () {
                Route::get('/all/{product}/{language}', 'CombinationController@combinations');
                Route::get('/all/count/{product}/{language}', 'CombinationController@combinationsCount');
                Route::get('/fetch/info/{combinationId}/{language}', 'CombinationController@CombinationInfo');
                Route::post('/update/info/{combination}', 'CombinationController@updateCombinationInfo');
                Route::post('/change/activation/{combination}', 'CombinationController@changeCombsActivation');
                Route::post('/set/default/{product}/{combination}', 'CombinationController@setDefaultCombination');

                Route::group([
                    'prefix' => 'images',
                ], function () {
                    Route::post('/upload/{combinationId}', 'CombinationFileController@uploadCombinationImage');
                    Route::delete('/delete/{image}', 'CombinationFileController@deleteCombinationImage');
                    Route::post('/arrange/{combinationId}', 'CombinationFileController@combinationImageArrange');
                });

            });


            Route::group([
                'prefix' => 'files',
            ], function () {
                Route::post('/upload/cover-image', 'ProductFileController@uploadCoverImage');
                Route::delete('/delete/cover-image', 'ProductFileController@deleteCoverImage');
                Route::post('/upload/video', 'ProductFileController@uploadVideo');
                Route::delete('/delete/video', 'ProductFileController@deleteVideo');

                Route::post('/upload/data-sheet', 'ProductFileController@uploadDatasheetPdf');
                Route::delete('/delete/data-sheet', 'ProductFileController@deleteDatasheetPdf');
                Route::post('/upload/assembly', 'ProductFileController@uploadAssemblyPdf');
                Route::delete('/delete/assembly', 'ProductFileController@deleteAssemblyPdf');

                Route::post('/upload/zip', 'ProductFileController@uploadZip');
                Route::delete('/delete/zip', 'ProductFileController@deleteZip');
            });
        });

        Route::group([
            'prefix' => 'durations',
            'namespace' => 'Duration',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::post('/create', 'DurationController@create');
            Route::post('/add/translation/{durationId}', 'DurationController@addTranslation');
            Route::patch('/update/{durationId}/{language}', 'DurationController@update');
            Route::get('/search', 'DurationController@search');
            Route::delete('/delete/{durationId}', 'DurationController@delete');
            Route::get('/fetch/{durationId}/{language}', 'DurationController@fetch');
            Route::get('/fetch/{durationId}', 'DurationController@delete');
        });

        Route::group([
            'prefix' => 'post-methods',
            'namespace' => 'PostMethod',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::post('/create', 'PostMethodController@create');
            Route::post('/add/translation/{postId}', 'PostMethodController@addTranslation');
            Route::patch('/update/{postId}/{language}', 'PostMethodController@update');
            Route::get('/search', 'PostMethodController@search');
            Route::delete('/delete/{postId}', 'PostMethodController@delete');
            Route::get('/fetch/{postId}/{language}', 'PostMethodController@fetch');
        });

        Route::group([
            'prefix' => 'technical-info',
            'namespace' => 'TechnicalInfo',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::post('/create', 'TechnicalInfoController@create');
            Route::post('/add/translation/{techId}', 'TechnicalInfoController@addTranslation');
            Route::patch('/update/{techId}/{language}', 'TechnicalInfoController@update');
            Route::get('/search', 'TechnicalInfoController@search');
            Route::delete('/delete/{techId}', 'TechnicalInfoController@delete');
            Route::get('/fetch/{techId}/{language}', 'TechnicalInfoController@fetch');
        });

        Route::group([
            'prefix' => 'shipping-info',
            'namespace' => 'ShippingInfo',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::post('/create', 'ShippingInfoController@create');
            Route::post('/add/translation/{shippingId}', 'ShippingInfoController@addTranslation');
            Route::patch('/update/{shippingId}/{language}', 'ShippingInfoController@update');
            Route::get('/search', 'ShippingInfoController@search');
            Route::delete('/delete/{shippingId}', 'ShippingInfoController@delete');
            Route::get('/fetch/{shippingId}/{language}', 'ShippingInfoController@fetch');
        });

        Route::group([
            'prefix' => 'portfolio',
            'namespace' => 'Portfolio',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::post('/create', 'PortfolioController@create');
            Route::patch('/update/{id}', 'PortfolioController@update');
            Route::get('/search', 'PortfolioController@search');
            Route::delete('/delete/{id}', 'PortfolioController@delete');
            Route::get('/fetch/{id}', 'PortfolioController@fetch');
            Route::post('/attach/products/{id}', 'PortfolioController@attachProducts');
            Route::get('/fetch/products/{id}/{language}', 'PortfolioController@fetchProducts');
            Route::post('/update/arrange', 'PortfolioController@updateArrange');

            Route::group([
                'prefix' => 'images',
            ], function () {
                Route::post('/upload', 'PortfolioImageController@upload');
                Route::delete('/delete/{id}', 'PortfolioImageController@delete');
            });
        });

        Route::group([
            'prefix' => 'options',
            'namespace' => 'Option',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::post('/create', 'OptionController@create');
            Route::post('/add/translation/{optionId}', 'OptionController@addTranslation');
            Route::patch('/update/{optionId}/{language}', 'OptionController@update');
            Route::get('/search', 'OptionController@search');
            Route::delete('/delete/{optionId}', 'OptionController@delete');
            Route::get('/fetch/{optionId}/{language}', 'OptionController@fetch');
        });

        Route::group([
            'prefix' => 'option-values',
            'namespace' => 'Option',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::post('/create/{optionId}', 'OptionValueController@create');
            Route::post('/add/translation/{optionId}', 'OptionValueController@addTranslation');
            Route::patch('/update/{optionId}/{language}', 'OptionValueController@update');
            Route::get('/search', 'OptionValueController@search');
            Route::delete('/delete/{id}', 'OptionValueController@delete');
            Route::get('/fetch/{optionId}/{language}', 'OptionValueController@fetch');
        });
        /////services
        Route::group([
            'prefix' => 'services',
            'namespace' => 'Service',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::post('/create', 'ServiceController@create');
            Route::post('/add/translation/{serviceId}', 'ServiceController@addTranslation');
            Route::patch('/update/{serviceId}/{language}', 'ServiceController@update');
            Route::get('/search', 'ServiceController@search');
            Route::delete('/delete/{serviceId}', 'ServiceController@delete');
            Route::get('/fetch/{serviceId}/{language}', 'ServiceController@fetch');

            Route::group([
                'prefix' => 'images',
            ], function () {
                Route::post('/upload', 'ServiceImageController@uploadImage');
                Route::delete('/delete', 'ServiceImageController@deleteImage');
            });
        });

        Route::group([
            'prefix' => 'download',
            'namespace' => 'Download',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::post('/create', 'DownloadController@create');
            Route::patch('/update/{serviceId}/{language}', 'DownloadController@update');
            Route::get('/search', 'DownloadController@search');
            Route::delete('/delete/{serviceId}', 'DownloadController@delete');
            Route::get('/fetch/{serviceId}/{language}', 'DownloadController@fetch');
            Route::group([
                'prefix' => 'images',
            ], function () {
                Route::post('/upload', 'DownloadImageController@uploadImage');
                Route::delete('/delete', 'DownloadImageController@deleteImage');
            });
        });

        Route::group([
            'prefix' => 'downloadfiles',
            'namespace' => 'Download',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::post('/create', 'DownloadFilesController@create');
            Route::patch('/update/{serviceId}/{language}', 'DownloadFilesController@update');
            Route::get('/search', 'DownloadFilesController@search');
            Route::delete('/delete/{serviceId}', 'DownloadFilesController@delete');
            Route::get('/fetch/{serviceId}/{language}', 'DownloadFilesController@fetch');
        });

        Route::group([
            'prefix' => 'service-values',
            'namespace' => 'Service',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::post('/create/{serviceId}', 'ServiceValueController@create');
            Route::post('/add/translation/{serviceId}', 'ServiceValueController@addTranslation');
            Route::patch('/update/{serviceId}/{language}', 'ServiceValueController@update');
            Route::get('/search', 'ServiceValueController@search');
            Route::delete('/delete/{id}', 'ServiceValueController@delete');
            Route::get('/fetch/{serviceId}/{language}', 'ServiceValueController@fetch');
        });

//////home
        Route::group([
            'prefix' => 'homes',
            'namespace' => 'Home',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::post('/create', 'HomeController@create');
            Route::post('/add/translation/{homeId}', 'HomeController@addTranslation');
            Route::patch('/update/{id}/{language}', 'HomeController@update');
            Route::get('/search', 'HomeController@search');
            Route::delete('/delete/{id}', 'HomeController@delete');
            Route::get('/fetch/{id}/{language}', 'HomeController@fetch');
            Route::get('/activate/{id}', 'HomeController@activate');
            ///slider
            Route::group([
                'prefix' => 'sliders',
                'namespace' => 'Slider'
            ], function () {
                Route::post('/create', 'SliderController@create');
                Route::patch('/update/{id}', 'SliderController@update');
                Route::get('/search', 'SliderController@search');
                Route::delete('/delete/{id}', 'SliderController@delete');
                Route::get('/fetch/{id}/{language}', 'SliderController@fetch');

                Route::group([
                    'prefix' => 'images',
                ], function () {
                    Route::post('/upload', 'SliderImageController@upload');
                    Route::delete('/delete/{id}', 'SliderImageController@delete');

                    Route::post('/upload/mobile', 'SliderImageController@uploadMobile');
                    Route::delete('/delete/mobile/{id}', 'SliderImageController@deleteMobile');

                    Route::delete('/delete/slider/{id}', 'SliderImageController@deleteSlider');


                });
            });
            ///sections
            Route::group([
                'prefix' => 'sections',
                'namespace' => 'Section'
            ], function () {
                Route::post('/create', 'SectionController@create');
                Route::post('/add/translation/{SectionId}', 'SectionController@addTranslation');
                Route::patch('/update/{SectionId}/{language}', 'SectionController@update');
                Route::get('/search', 'SectionController@search');
                Route::delete('/delete/{id}', 'SectionController@delete');
                Route::get('/fetch/{SectionId}/{language}', 'SectionController@fetch');
            });
        });
        ////home end
        Route::group([
            'prefix' => 'comments',
            'namespace' => 'Comment',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::get('/search', 'CommentController@search');
            Route::get('/fetch/{commentId}', 'CommentController@fetch');
            Route::get('/fetch/replays/{commentId}', 'CommentController@fetchReplays');
            Route::post('/add/reply/{productId}/{commentId}', 'CommentController@replay');
            Route::get('/activate/{commentId}', 'CommentController@activate');
            Route::get('/deactivate/{commentId}', 'CommentController@deactivate');
        });

        Route::group([
            'prefix' => 'invoices',
            'namespace' => 'Invoice',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::get('/get_csv/{pid}', 'InvoiceController@get_csv');
            Route::get('/search', 'InvoiceController@search');
            Route::get('/fetch/{id}', 'InvoiceController@fetch');
            Route::get('/fetch/unseen/count', 'InvoiceController@unseenCount');
            Route::post('/change/status/{paymentIntent}', 'InvoiceController@changeInvoiceStatus');
            Route::post('/cancel/{paymentIntent}', 'InvoiceController@cancelInvoice');
            Route::post('/products/number-of-image/{incAddPId}', 'InvoiceController@setNumberOFNeededImage');
            Route::get('/email/logs/{paymentIntent}', 'InvoiceController@invoiceSentEmails');
            Route::get('/addresses/{paymentIntent}', 'InvoiceController@invoiceAddresses');
            Route::post('/addresses/change/status/{paymentIntent}', 'InvoiceController@changeInvoiceAddressStatus');
            Route::get('/addresses/products/{incAddId}', 'InvoiceController@invoiceAddressProducts');
            Route::get('/addresses/products/images/fetch/{invoiceProductId}/{imageId}', 'InvoiceController@fetchImage');
            Route::delete('/addresses/products/images/delete/{imageId}', 'InvoiceController@deleteImage');
            Route::get('/addresses/products/images/download/{invoiceProductId}/{imageId}', 'InvoiceController@downloadImage');
            Route::get('/addresses/products/xml/download/{xmlName}', 'InvoiceController@downloadXml');
        });

        Route::group([
            'prefix' => 'coupon',
            'namespace' => 'Coupon',
            'middleware' => ['isAdmin:1']
        ], function () {
            Route::post('/create', 'CouponController@create');
            Route::post('/update', 'CouponController@update');
            Route::get('/search', 'CouponController@search');
            Route::delete('/delete/{couponId}', 'CouponController@delete');
            Route::get('/fetch/{couponId}', 'CouponController@fetch');
        });
        Route::group([
            'prefix' => 'holiday',
            'namespace' => 'Holiday',
            'middleware' => ['isAdmin:1']
        ], function () {
            Route::post('/create', 'HolidayController@create');
            Route::patch('/update/{holidayId}', 'HolidayController@update');
            Route::get('/search', 'HolidayController@search');
            Route::delete('/delete/{holidayId}', 'HolidayController@delete');
            Route::get('/fetch/{holidayId}', 'HolidayController@fetch');
        });

        Route::group([
            'prefix' => 'pages',
            'namespace' => 'Page',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::post('/create', 'PageController@create');
            Route::post('/add/translation/{pageId}', 'PageController@addTranslation');
            Route::patch('/update/{id}/{language}', 'PageController@update');
            Route::get('/search', 'PageController@search');
            Route::delete('/delete/{id}', 'PageController@delete');
            Route::get('/fetch/{id}/{language}', 'PageController@fetch');
            Route::get('/is/unique', 'PageController@isUnique');
        });
///
        Route::group([
            'prefix' => 'page/sidebar',
            'namespace' => 'Page',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::post('/create', 'PageSidebarController@create');
            Route::patch('/update/{sidebarId}', 'PageSidebarController@update');
            Route::get('/search', 'PageSidebarController@search');
            Route::get('/fetch/{sidebarId}/{language}', 'PageSidebarController@fetch');
            Route::delete('/delete/{sidebarId}', 'PageSidebarController@delete');
        });

        Route::group([
            'prefix' => 'footer',
            'namespace' => 'Footer',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::post('/update/{language}', 'FooterController@update');
            Route::get('/fetch/{language}', 'FooterController@fetch');
        });

        Route::group([
            'prefix' => 'popups',
            'namespace' => 'Popup',
            'middleware' => ['isAdmin:1,2']
        ], function () {
            Route::post('/create', 'PopupController@create');
            Route::post('/add/translation/{popupId}', 'PopupController@addTranslation');
            Route::patch('/update/{id}/{language}', 'PopupController@update');
            Route::get('/search', 'PopupController@search');
            Route::delete('/delete/{id}', 'PopupController@delete');
            Route::get('/fetch/{id}/{language}', 'PopupController@fetch');
            Route::get('/activate/{id}', 'PopupController@activate');
            Route::get('/deactivate/{id}', 'PopupController@deactivate');
        });


        Route::group([
            'prefix' => 'newsletter',
            'namespace' => 'NewsLetter',
        ], function () {
            Route::get('search', 'NewsLetterController@search');
            Route::delete('search', 'NewsLetterController@delete');
        });


        Route::group([
            'prefix' => 'front-info',
            'namespace' => 'FrontInfo',
        ], function () {
            Route::post('redirections/add', 'FrontRedirectionsInfoController@add');
            Route::get('redirections/fetch', 'FrontRedirectionsInfoController@fetch');
        });

///////
///
    });
    ////end of admin
});

Route::get('fetch/image', function (Request $request) {
    $path = storage_path('app/public/' . $request['path'] . '/' . $request['image']);
    return response()->file($path);
});

Route::get('fetch/file', function (Request $request) {
    $path = storage_path('app/public/' . $request['path'] . '/' . $request['file']);
    return response(base64_encode(file_get_contents($path)));
});

/**==================================================
 * clients apis
 * ---------------------------------------------------*/

Route::group([
    'prefix' => 'v1/user',
    'namespace' => '\App\Http\Controllers\Api\V1\User',
], function () {

    Route::group([
        'prefix' => 'auth',
        'namespace' => 'Auth',
    ], function () {
        Route::post('/login', 'AuthController@login');
        Route::post('/register', 'AuthController@register');
        Route::post('/forget-password', 'AuthController@forgetPassword');
        Route::post('/reset-password/{token}', 'AuthController@resetPassword');
    });
    /**==================================
     * site apis
     * ======================================*/
    Route::group([
        'prefix' => 'site',
        'namespace' => 'Site',
    ], function () {
        Route::group([
            'prefix' => 'menus',
            'namespace' => 'Menu'
        ], function () {
            Route::get('/mega', 'MenuController@menus');
            Route::get('/fetch/{menuSlug}', 'MenuController@menuInfo');
            Route::get('/fetch/children/{menuSlug}', 'MenuController@fetchMenuChildren');
            Route::get('/fetch/menu/breadcrumb', 'MenuController@menuPath');
        });

        Route::group([
            'prefix' => 'search',
            'namespace' => 'Search'
        ], function () {
            Route::get('/products', 'SearchController@searchProducts');
        });

        Route::group([
            'prefix' => 'middle-page',
            'namespace' => 'MiddlePage'
        ], function () {
            Route::get('/products/{menu}', 'MiddlePageController@products');
            Route::get('/slugs/{menu}', 'MiddlePageController@slugs');
        });

        Route::group([
            'prefix' => 'detail-page/product',
            'namespace' => 'DetailPage'
        ], function () {

            Route::group([], function () {
                Route::get('/fetch/{productSlug}', 'DetailPageController@fetchProduct');
                Route::get('/fetch/options/{productSlug}', 'DetailPageController@fetchProductOptions');
                Route::get('/fetch/optionsid/{productId}', 'DetailPageController@fetchProductOptionsID');
                Route::get('/fetch/infos/{productSlug}', 'DetailPageController@fetchProductInfos');
                Route::get('/fetch/combination-info/{productSlug}', 'DetailPageController@fetchCombinationInfo');
            });
        });

        Route::group([
            'prefix' => 'contact-us',
            'namespace' => 'ContactUs'
        ], function () {
            Route::get('/fetch', 'ContactUsController@fetch');
        });

        Route::group([
            'prefix' => 'about-us',
            'namespace' => 'AboutUs'
        ], function () {
            Route::get('/fetch', 'AboutUsController@fetch');
        });

        Route::group([
            'prefix' => 'comments',
            'namespace' => 'Comment',
        ], function () {
            Route::get('/{productId}', 'CommentController@fetchComments');
            Route::get('/replays/{commentId}', 'CommentController@fetchReplays');

            Route::group([
                'middleware' => ['auth:api', 'isUser']
            ], function () {
                Route::post('/add/{productId}', 'CommentController@comment');
                Route::post('/add/replay/{productId}/{commentId}', 'CommentController@replay');
            });
        });

        Route::group([
            'prefix' => 'home',
            'namespace' => 'Home'
        ], function () {
            Route::get('fetch', 'HomeController@homeInfo');
            Route::get('downloads', 'HomeController@download');
            Route::get('fetch/tax', 'HomeController@fetchTax');
            Route::get('fetch/production/delay', 'HomeController@fetchWorkingDelay');
        });

        Route::group([
            'prefix' => 'bookmark',
            'namespace' => 'Bookmark',
            'middleware' => ['auth:api', 'isUser']
        ], function () {
            Route::get('add/{productId}', 'BookmarkController@addToBookmark');
            Route::get('remove/{productId}', 'BookmarkController@removeFromBookmark');
        });

        Route::group([
            'prefix' => 'cart',
            'namespace' => 'Cart',
            'middleware' => ['auth:api', 'isUser']
        ], function () {
            Route::post('add/{productId}', 'CartController@addToCart');
            Route::delete('remove/{productId}/{combinationId}/{durationsId}', 'CartController@removeFromCart');
            Route::get('fetch', 'CartController@fetchCartItems');
            Route::get('fetch/discount/{productId}', 'CartController@fetchDiscount');
            Route::get('items/count', 'CartController@cartItemCount');
            Route::post('check/stock/{productId}', 'CartController@checkStockCount')->withoutMiddleware(['auth:api', 'isUser']);
            Route::get('price/calculator', 'CartController@calculatePrice');
            Route::post('check/coupon', 'CartController@checkCoupon')->withoutMiddleware(['auth:api', 'isUser']);
            Route::post('check/ust-id', 'CartController@checkUstId')->withoutMiddleware(['auth:api', 'isUser']);


            Route::group([
                'prefix' => 'addresses',
            ], function () {
                Route::post('add', 'AddressController@create');
                Route::Patch('update/{id}', 'AddressController@update');
                Route::get('fetch-all', 'AddressController@search');
                Route::delete('delete/{id}', 'AddressController@delete');
                Route::get('fetch/{id}', 'AddressController@fetch');
                Route::get('fetch/default/address', 'AddressController@fetchDefaultAddress');
            });

            Route::group([
                'prefix' => 'orders',
            ], function () {
                Route::post('create', 'OrderController@createOrder');
                Route::post('cancel/stripe', 'OrderController@cancelStripeOrder');
                Route::post('capture/paypal/{token}', 'OrderController@capturePayPalOrder');
                Route::post('cancel/paypal/{token}', 'OrderController@cancelPayPalOrder');
            });
        });

        Route::group([
            'prefix' => 'orders',
            'namespace' => 'Cart',
        ], function () {
            Route::post('webhook', 'OrderController@webhook');
        });

        Route::group([
            'prefix' => 'countries',
            'namespace' => 'Location',
        ], function () {
            Route::get('search', 'CountryController@search');
            Route::get('post-methods/{countryId}', 'CountryController@fetchPostMethod');
        });

        Route::group([
            'prefix' => 'pages',
            'namespace' => 'Page',
        ], function () {
            Route::get('fetch/{slug}', 'PageController@fetch');
        });

        Route::group([
            'prefix' => 'page/sidebar',
            'namespace' => 'Page',
        ], function () {
            Route::get('/fetch/{id}', 'PageSidebarController@fetch');
        });

        Route::group([
            'prefix' => 'footer',
            'namespace' => 'Footer',
        ], function () {
            Route::get('/fetch', 'FooterController@fetch');
        });


        Route::group([
            'prefix' => 'popups',
            'namespace' => 'Popup',
        ], function () {
            Route::get('/', 'PopupController@popups');
        });

        Route::group([
            'prefix' => 'newsletter',
            'namespace' => 'NewsLetter',
        ], function () {
            Route::post('subscribe', 'NewsLetterController@subscribe');
        });

        Route::group([
            'prefix' => 'holiday',
            'namespace' => 'Holiday',
        ], function () {
            Route::get('/get/period/{day}', 'HolidayController@checkHoliday');
        });

        Route::group([
            'prefix' => 'front-info',
            'namespace' => 'FrontInfo',
        ], function () {
            Route::get('/redirections/fetch', 'FrontRedirectionsInfoController@fetch');
        });

    });
    /**========================================
     * site apis end
     * =========================================*/
    /**========================================
     * user dashboard apis
     * =========================================*/
    Route::group([
        'prefix' => 'dashboard',
        'namespace' => 'Dashboard',
        'middleware' => ['auth:api', 'isUser']
    ], function () {
        Route::group([
            'prefix' => 'profile',
            'namespace' => 'Profile',
        ], function () {
            Route::get('fetch', 'ProfileController@fetchProfile');
            Route::patch('update', 'ProfileController@updateProfile');
            Route::group([
                'prefix' => 'password',
            ], function () {
                Route::patch('update', 'PasswordController@updatePassword');

            });
        });

        Route::group([
            'prefix' => 'bookmarks',
            'namespace' => 'Bookmark',
        ], function () {
            Route::get('all', 'BookmarkController@fetchBookMarks');
            Route::get('count', 'BookmarkController@bookmarkCount');
        });

        Route::group([
            'prefix' => 'invoices',
            'namespace' => 'Invoice'
        ], function () {
            Route::get('/search', 'InvoiceController@search');
            Route::get('/fetch/{id}', 'InvoiceController@fetch');
            Route::get('/addresses/{paymentIntent}', 'InvoiceController@invoiceAddresses');
            Route::get('/addresses/products/{incAddId}', 'InvoiceController@invoiceAddressProducts');
            Route::post('/ready/{paymentIntent}', 'InvoiceController@readyInvoice');
            Route::group([
                'prefix' => 'images',
            ], function () {
                Route::post('/upload/{invoiceProductId}/{imageId?}', 'InvoiceImageController@upload');
                Route::delete('/delete/{invoiceProductId}/{imageId}', 'InvoiceImageController@delete');
                Route::get('/fetch/{invoiceProductId}/{imageId}', 'InvoiceImageController@fetch');
            });
        });

        Route::group([
            'prefix' => 'newsletter',
            'namespace' => 'NewsLetter',
        ], function () {
            Route::get('get', 'NewsLetterController@subInfo');
            Route::post('subscribe', 'NewsLetterController@subscribe');
            Route::post('unsubscribe', 'NewsLetterController@unsubscribe');
        });

    });
    /*******************
     * end of User dashboard
     *******************/
});
Route::get('/test', function (Request $request) {

    return 0;
//    $arr = [['23423/234234', '81/2','810/2'],['2/3', '81/2','1/2']];
//    foreach($arr as &$a) {
//        \App\Helper\Sort\BubbleSort::sort($a);
//    }
//    return $arr;
    $products = \App\Models\Product\Product::where('type', 2)->pluck('id', 'id')->toArray();
    $menus = \App\Models\Menu\Menu::where('level', 2)->pluck('id')->toArray();
    foreach ($menus as $menu) {
        $rndproductsIndex = array_rand($products, 15);
        if (!is_array($rndproductsIndex)) {
            $rndproductsIndex = [$rndproductsIndex];
        }
        \App\Models\Menu\Menu::find($menu)->products()->sync($rndproductsIndex);
    }
    return $menus;
//return \App\Models\Product\Product::fetchPt2Combinations(37,'en');
    /**
     * product type 2 seederrr
     */

    $faker = \Faker\Factory::create();
    $categories = \App\Models\Category\Category::with('productType1')->get()
        ->pluck('productType1.*.id', 'id')->toArray(); //[1:[1,2,3,],...,n:[]]
    for ($i = 0; $i < 20; $i++) {
        // create product
        $productCol = ['type' => 2, 'reorder' => 1,
            'cover_image' => '', 'video' => ''];
        $createdProduct = \App\Models\Product\Product::create($productCol);
        ///insert translations
        $title = $faker->name;
        $Desc = $faker->text(250);
        $productT = [['title' => $title, 'benefit_desc' => $Desc,
            'item_desc' => $Desc, 'language' => 'en', 'product_id' => $createdProduct['id']],
            ['title' => $title . 'german', 'benefit_desc' => $Desc,
                'item_desc' => $Desc, 'language' => 'de', 'product_id' => $createdProduct['id']]];
        \App\Models\Product\ProductTranslation::insert($productT);
        /// attach pt1 and pt1 combs to pt2
        $pt2Pt1Combs = [];
        $catRandIndexes = array_rand($categories, rand(1, 5));
        if (!is_array($catRandIndexes)) {
            $catRandIndexes = [$catRandIndexes];
        }
        //insert category
        $pt2Cat = [];
        foreach ($catRandIndexes as $index => $j) {
            if (count($categories[$j]) > 0) {
                $pt2Cat[] = ['category_id' => $j, 'product_id' => $createdProduct['id'],
                    'arrange' => rand(1, 5), 'has_no_select' => rand(0, 1)];
            }
        }
        \App\Models\Product\Pivot\Type2\Pt2Category::insert($pt2Cat);
        //inset pt1 combinations
        foreach ($catRandIndexes as $catRandIndex) {
            $pt1s = $categories[$catRandIndex];
            if (count($pt1s) > 0) {
                $pt1sCount = count($pt1s);
                $pt1sRandIndexes = array_rand($pt1s, rand(1, $pt1sCount));
                if (!is_array($pt1sRandIndexes)) {
                    $pt1sRandIndexes = [$pt1sRandIndexes];
                }
                foreach ($pt1sRandIndexes as $pt1sRandIndex) {
                    $pt1combs = Pt1Combination::where('product_id', $pt1s[$pt1sRandIndex])->pluck('id')->toArray();
                    $pt1CombsCount = count($pt1combs);
                    $pt1CombRandIndexes = array_rand($pt1combs, rand(1, $pt1CombsCount));
                    if (!is_array($pt1CombRandIndexes)) {
                        $pt1CombRandIndexes = [$pt1CombRandIndexes];
                    }
                    foreach ($pt1CombRandIndexes as $pt1CombRandIndex) {
                        $pt2Pt1Combs[] = ['pt2_id' => $createdProduct['id'],
                            'category_id' => $catRandIndex, 'pt1_id' => $pt1s[$pt1sRandIndex],
                            'pt1_combination_id' => $pt1combs[$pt1CombRandIndex]];
                    }
                }

            }
        }
        \App\Models\Product\Pivot\Type2\Pt2Pt1Combination::insert($pt2Pt1Combs);
        dispatch(new \App\Jobs\Product\Type2\Pt2CombinationCheckerJob($createdProduct['id']));
    }
//
//    return $result;
//    $query = 'SELECT cpd.post_id,pmt.title as \'post_method\',JSON_ARRAYAGG(JSON_OBJECT(\'duration_id\',cpd.duration_id,\'duration\',dt.title,\'price\',cpd.price)) FROM country_post_duration cpd JOIN post_method_translations pmt ON cpd.post_id=pmt.post_method_id and pmt.language=\'en\' JOIN duration_translations dt on cpd.duration_id=dt.duration_id and dt.language=\'en\' WHERE country_id = 1 GROUP BY cpd.post_id';

//    return dispatch_sync(new \App\Jobs\ProductT1CombinationsJob(1));
//    $value = \App\Models\Product\Pivot\Pt1OptionValue::fetchOptionValueGroup(1)[0]['ov'];
//
//    return \App\Helper\Response\Response::response200([],
//        array_map('trim', explode(',', $value)));
//    $s = new \App\Helper\SystemMessage\SystemMessage('de', 'image', 'en');
//    return $s->delete();
//    return app_path();
//    $language = 'de';
//    return \App\Helper\Response\Response::response200(
//    return \App\Helper\Response\Response::response200([],$request->header(LanguageHelper::getHeaderKey()));
//    return \App\Helper\Response\Response::response200([],
//    return \App\Helper\Language\LanguageHelper::getAppLanguage($request);
//    $message = new \App\Helper\SystemMessage\SystemMessage('ez', 'user', 'en');
//    return $message->update();
//    \App\Models\User\Role::Create([]);
//    \App\Models\User\RoleTranslation::create(['role_id' => 1, 'title' => 'admin', 'language_code' => 'en']);
//    $langes = \App\Models\Language\Language::where('active', 1)->pluck('code', 'code');
//    return $langes;
//    $cache = \Illuminate\Support\Facades\Cache::put('langs', $langes);
//    return \App\Helper\Response\Response::response200(',', \Illuminate\Support\Facades\Cache::get('languages'));
//    return \App\Helper\Response\Response::response200(',',$request->hasHeader('application-language'));
//    return \App\Helper\Response\Response::response200(',', $request->header('application-language'));
//    return \App\Helper\Response\Response::response200(',',\App\Models\Language\Language::where('default',1)->first()['code']);
});

Route::post('/test2', function (Request $request) {
    \App\Helper\Uploader\Uploader::uploadToStorage($request['image'], 'image', 'menu');
});

Route::get('image', function () {
    $path = storage_path('app/public/image/menu/612dc74a447e7612dc74a447e91630390090.jpg');
    return response()->file($path);
});




