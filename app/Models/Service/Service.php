<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    public function serviceTranlation()
    {
        return $this->hasOne(ServiceTranslation::class);
    }


    public function serviceT()
    {
        return $this->hasOne(ServiceValueTranslation::class);
    }

    public function serviceValue()
    {
        return $this->hasMany(ServiceValue::class);
    }

}
