<?php

namespace App\Models\Download;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    use HasFactory;
    protected $fillable = ['title'];
    

    public function downloadFiles()
    {
        return $this->hasMany(DownloadFiles::class, 'download_id');
    }
    
}
