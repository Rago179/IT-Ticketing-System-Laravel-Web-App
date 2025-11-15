<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profile extends Model
{
    use HasFactory;

    // FIX 1: Specify that the primary key is 'user_id', not 'id'
    protected $primaryKey = 'user_id';

    // FIX 2: Tell Laravel this key does not auto-increment (it comes from the User)
    public $incrementing = false;
    
    protected $fillable = [
        'user_id',
        'bio',
    ];

    /**
     * A profile belongs to ONE user.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }
}