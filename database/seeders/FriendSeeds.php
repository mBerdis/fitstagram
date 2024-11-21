<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FriendSeeds extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $friends = $users->random(rand(1, 3))->whereNotIn('id', [$user->id]);

            foreach ($friends as $friend) {
                $friendshipExists = DB::table('friends')
                    ->where(function ($query) use ($user, $friend) {
                        $query->where('user1', $user->id)
                              ->where('user2', $friend->id);
                    })
                    ->orWhere(function ($query) use ($user, $friend) {
                        $query->where('user1', $friend->id)
                              ->where('user2', $user->id);
                    })
                    ->exists();

                if (!$friendshipExists) {
                    $user->friends()->attach($friend->id);
                    $friend->friends()->attach($user->id);
                }
            }

            $potentialRequests = $users->random(rand(1, 3))->whereNotIn('id', [$user->id]);

            foreach ($potentialRequests as $requester) {
                $reverseRequestExists = DB::table('friend_requests')
                    ->where('user1', $user->id)
                    ->where('user2', $requester->id)
                    ->exists();

                $alreadyFriends = DB::table('friends')
                    ->where(function ($query) use ($user, $requester) {
                        $query->where('user1', $user->id)
                            ->where('user2', $requester->id);
                    })
                    ->orWhere(function ($query) use ($user, $requester) {
                        $query->where('user1', $requester->id)
                            ->where('user2', $user->id);
                    })
                    ->exists();

                if (!$reverseRequestExists && !$alreadyFriends) {
                    $requester->friendRequests()->attach($user->id);
                }
            }
        }
    }
}
