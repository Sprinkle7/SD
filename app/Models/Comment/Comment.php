<?php

namespace App\Models\Comment;

use App\Models\Product\ProductTranslation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['parent_id', 'product_id', 'user_id',
        'title', 'content', 'is_active'];

    public static function generateCommentCollection($request)
    {
        return [
            'title' => $request['title'],
            'content' => $request['content'],
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(ProductTranslation::class, 'product_id', 'product_id');
    }
}
