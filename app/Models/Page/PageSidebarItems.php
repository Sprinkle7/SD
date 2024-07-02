<?php

namespace App\Models\Page;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageSidebarItems extends Model
{
    use HasFactory;

    protected $fillable = ['sidebar_id', 'page_id', 'type', 'arrange'];

    public function pages()
    {
        return $this->belongsTo(PageTranslation::class, 'page_id', 'page_id');
    }


}
