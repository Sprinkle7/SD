<?php

namespace App\Models\Product\Pivot\Type2;

use App\Models\Category\Category;
use App\Models\Category\CategoryTranslation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pt2Category extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['product_id', 'category_id', 'has_no_select', 'arrange','is_default'];

    public static function generatePt2CategoryCollection($request)
    {
        return [
            'category_id' => $request['category_id'],
            'arrange' => $request['arrange'],
        ];
    }

    public function categories()
    {
        return $this->belongsTo(CategoryTranslation::class, 'category_id', 'category_id');
    }
}
