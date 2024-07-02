<?php

namespace App\Models\Footer;

use App\Models\Menu\Menu;
use App\Models\Menu\MenuTranslation;
use App\Models\Page\PageTranslation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterSectionTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['footer_section_id', 'type', 'title', 'language', 'arrange'];

    public function pages()
    {
        return $this->belongsToMany(PageTranslation::class, 'footer_section_page', 'footer_section_id', 'page_id',
            'footer_section_id', 'page_id')->withPivot('arrange');
    }

    public function menusInfo() {
        return $this->belongsToMany(Menu::class, 'footer_section_menu', 'footer_section_id', 'menu_id',
            'footer_section_id', 'id')->withPivot('arrange');
    }

    public function menus()
    {
        return $this->belongsToMany(MenuTranslation::class, 'footer_section_menu', 'footer_section_id', 'menu_id',
            'footer_section_id', 'menu_id')->withPivot('arrange');
    }
}
