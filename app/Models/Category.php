<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // THIS IS THE NEW RELATIONSHIP
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}