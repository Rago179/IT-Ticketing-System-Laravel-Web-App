<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Like;

class Comment extends Model
{
    use HasFactory;  

    protected $fillable = ['post_id', 'user_id', 'content'];
    /**
     * Many comments can belong to ONE posts.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

     /**
     * Many comments can belong to ONE user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
        {
            return $this->morphMany(Like::class, 'likeable');
        }

    public function isLikedBy(User $user)
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}