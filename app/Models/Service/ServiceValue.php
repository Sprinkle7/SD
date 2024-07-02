<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceValue extends Model
{
    use HasFactory;

    protected $fillable = ['service_id', 'price','duration'];

    public function serviceValueTranslation() {
        return $this->hasOne(ServiceValueTranslation::class);
    }
}
