<?php

namespace App\Models\Product;

use App\Models\Category\Category;
use App\Models\Category\CategoryTranslation;
use App\Models\Discount\Discount;
use App\Models\Duration\Duration;
use App\Models\Menu\Menu;
use App\Models\Menu\SortCategory;
use App\Models\Portfolio\Portfolio;
use App\Models\Portfolio\PortfolioImage;
use App\Models\Product\ability\ProductQuery;
use App\Models\Product\Combination\Combination;
use App\Models\Product\Pivot\Type1\Pt1Combination;
use App\Models\Product\Pivot\Type2\Pt2Combination;
use App\Models\Service\Service;
use App\Models\TechnicalInfo\TechnicalInfo;
use App\Models\TechnicalInfo\TechnicalInfoTranslation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, ProductQuery;

    protected $fillable = [
        'code',
        'price',
        'type',
        'reorder',
        'cover_image',
        'video',
        'is_active',
        'portfolio_id',
        'default_menu_id',
        'default_combination_id',
        'data_sheet_pdf',
        'assembly_pdf',
        'zip'
    ];

    public static function generateProductCollection($request)
    {
        return [
            'price' => $request['price'],
            'reorder' => $request['reorder'] ? 1 : 0,
            'cover_image' => $request['cover_image'],
            'data_sheet_pdf' => $request['data_sheet_pdf'],
            'assembly_pdf' => $request['assembly_pdf'],
            'zip' => $request['zip'],
            'code' => $request['code']
        ];
    }

    public function workingDay()
    {
        return $this->belongsToMany(Duration::class)->withPivot('price', 'default_value');
    }

    public function product_Translations()
    {
        return $this->hasMany(ProductTranslation::class, 'product_id');
    }

    public function technicalInfos()
    {
        return $this->belongsToMany(TechnicalInfoTranslation::class, 'product_technical_info'
            , 'product_id', 'technical_info_id', 'id', 'technical_info_id')
            ->withPivot('arrange')->orderBy('arrange', 'ASC');
    }

    public function updateTechnicalInfos()
    {
        return $this->belongsToMany(TechnicalInfo::class,'product_technical_info')
            ->withPivot('arrange')->orderBy('arrange', 'ASC');
    }


    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class, 'portfolio_id', 'id');
    }

    public function portfoliosImage()
    {
        return $this->hasMany(PortfolioImage::class, 'portfolio_id', 'portfolio_id');
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class);
    }

    public function category()
    {
        return $this->belongsToMany(SortCategory::class);
    }

    public function defaultCombination()
    {
        return $this->belongsTo(Combination::class, 'default_combination_id', 'id');
    }

    public function defaultMenu()
    {
        return $this->belongsTo(Menu::class, 'default_menu_id', 'id');
    }

    public function discounts()
    {
        return $this->hasMany(Discount::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class)->withPivot('has_no_select');
    }

    public function files() {
        return $this->hasMany(ProductFile::class);
    }

}

