<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'message'
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
