<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Like;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $table = "comments";

    protected $fillable = [
        "post_id",
        "comment"
    ];

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function like() : MorphMany {
        return $this->morphMany(Like::class, 'liked');
    }
}
