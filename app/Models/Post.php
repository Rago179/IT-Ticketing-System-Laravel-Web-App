<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;  

    protected $fillable = ['user_id', 'title', 'description', 'status','priority'];
    /**
     * Many posts can belong to ONE user.
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
 
    /**
     *  Many post can belong to many categories.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }
}