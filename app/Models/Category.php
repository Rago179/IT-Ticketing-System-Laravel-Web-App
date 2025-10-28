?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = ['name'];

    /**
     * A category can have many posts (tickets).
     */
    public function posts()
    {
        // This links to the posts table via the 'category_post' pivot table
        return $this->belongsToMany(Post::class);
    }
}