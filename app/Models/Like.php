<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Like extends Model
{
    use HasFactory;

    protected $table = "likes";

    protected $fillable = [
        "user_id", "liked_type", "liked_id"
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function liked() : MorphTo {
        return $this->morphTo('liked');
    }
}
