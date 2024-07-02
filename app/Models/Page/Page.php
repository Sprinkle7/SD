<?php

namespace App\Models\Page;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_active', 'sidebar_id'
    ];

    public function sidebar()
    {
        return $this->belongsTo(PageSidebarInfo::class);
    }

}
