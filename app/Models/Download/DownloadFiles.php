<?php

namespace App\Models\Download;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DownloadFiles extends Model
{
    use HasFactory;
    protected $fillable = ['title','image','download_id'];
    
    public function download()
    {
        return $this->belongsTo(Download::class, 'download_id');
    }
    
}
