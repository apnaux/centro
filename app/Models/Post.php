<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    use HasFactory;

    protected $table = "posts";

    protected $fillable = [
        'post',
        'visibility',
    ];

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }
    
    public function comments() : HasMany {
        return $this->hasMany(Comment::class);
    }

    public function like() : MorphMany {
        return $this->morphMany(Like::class, 'liked');
    }
}
