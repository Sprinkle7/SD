<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceValueTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['service_id', 'service_value_id', 'title', 'language'];

    public static function generateServiceTransCollection($request)
    {
        $collection = ['title' => $request['title']];
        if (isset($request['language'])) {
            $collection['language'] = $request['language'];
        }
        return $collection;
    }

    public function serviceValue()
    {
        return $this->belongsTo(ServiceValue::class);
    }
}
