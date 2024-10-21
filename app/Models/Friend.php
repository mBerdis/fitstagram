<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Friend extends Model
{
    use HasFactory, Notifiable;

    public function user1()
    {
        return $this->belongsTo(User::class, 'user1');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user2');
    }
}
