<?php

namespace App\Models\Home;

use App\Models\Home\Section\HSection;
use App\Models\Home\Section\HSectionTranslation;
use App\Models\Home\Slider\Slider;
use App\Models\Home\Slider\SliderImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Home extends Model
{
    use HasFactory;

    protected $fillable = ['slider_id', 'title', 'is_active'];

    public function sliderImage()
    {
        return $this->hasMany(SliderImage::class,'slider_id','slider_id');
    }

    public function slider() {
        return $this->belongsTo(Slider::class,'slider_id','id');
    }

    public function sections()
    {
        return $this->belongsToMany(HSection::class);
    }

    public function sectionsT() {
        return $this->belongsToMany(HSectionTranslation::class, 'h_section_home','home_id' ,'h_section_id' , 'id', 'h_section_id');
    }

    public function home_info() {
        return $this->hasOne(HomeTranslation::class);
    }
}
