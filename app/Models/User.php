<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'profile_picture',
        'role' ,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function groupsOwned(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function friends(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friends', 'user1', 'user2');
    }

    public function friendRequests(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friend_requests', 'user1', 'user2');
    }

    public function receivedFriendRequests(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friend_requests', 'user2', 'user1');
    }


    public function groupsMember(): BelongsToMany
    {
        return $this->belongsToMany(Group::class);
    }

    public function liked(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }
}
