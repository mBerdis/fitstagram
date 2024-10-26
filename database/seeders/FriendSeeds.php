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
        // Získame všetkých používateľov
        $users = User::all();

        // Pre každý pár používateľov vytvoríme priateľstvo alebo žiadosť o priateľstvo
        foreach ($users as $user) {
            // Vyberieme náhodných priateľov pre každého používateľa
            $friends = $users->random(rand(1, 3))->whereNotIn('id', [$user->id]);

            foreach ($friends as $friend) {
                // Skontrolujeme, či už priateľstvo existuje
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
                    // Vytvoríme obojsmerné priateľstvo
                    $user->friends()->attach($friend->id);
                    $friend->friends()->attach($user->id);
                }
            }

            // Pridáme niekoľko žiadostí o priateľstvo
            $potentialRequests = $users->random(rand(1, 2))->whereNotIn('id', [$user->id]);
            
            foreach ($potentialRequests as $requester) {
                // Kontrola na existenciu opačnej žiadosti
                $reverseRequestExists = DB::table('friend_requests')
                    ->where('user1', $user->id)
                    ->where('user2', $requester->id)
                    ->exists();

                if (!$reverseRequestExists) {
                    // Pridáme jednostrannú žiadosť o priateľstvo, ak opačná žiadosť neexistuje
                    $requester->friendRequests()->attach($user->id);
                }
            }
        }
    }
}
