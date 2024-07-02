<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['service_id', 'title', 'image', 'height', 'width', 'language'];

    public static function generateServiceTransCollection($request)
    {
        $collection = [
            'title' => $request['title'],
            'language' => $request['language'],
            'height' => $request['height'],
            'width' => $request['width']];

        if (isset($request['image'])) {
            $collection['image'] = $request['image'];
        }
        return $collection;
    }

    public function values()
    {
        return $this->hasMany(ServiceValueTranslation::class, 'service_id', 'service_id');
    }
}
