<?php

namespace App\Models\Location;

use App\Helper\Response\Response;
use App\Models\Duration\DurationTranslation;
use App\Models\PostMethod\PostMethodTranslation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryPostDuration extends Model
{
    use HasFactory;

    protected $table = 'country_post_duration';
    protected $fillable = ['country_id', 'post_id', 'min_price','price'];
    public $timestamps = false;

    public static function generateCountryCollection($request)
    {
        return [
            'post_id' => $request['post_id'],
            'min_price' => $request['min_price'],
            'price' => $request['price'],
        ];
    }

    public function postMethod()
    {
        return $this->belongsTo(PostMethodTranslation::class, 'post_id', 'post_method_id');
    }
}
