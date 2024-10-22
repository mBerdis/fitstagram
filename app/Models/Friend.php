<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Friend extends Model
{
    use HasFactory, Notifiable;

    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1');
    }

    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2');
    }
}
