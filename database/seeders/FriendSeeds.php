<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Friend;

class FriendSeeds extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $friends = Friend::factory()->count(10)->create();

        foreach ($friends as $friend) {
            $reverseExists = Friend::where('user1', $friend->user2)
                ->where('user2', $friend->user1)
                ->exists();

            if (!$reverseExists) {
                Friend::create([
                    'user1' => $friend->user2,
                    'user2' => $friend->user1,
                ]);
            }
        }
    }
}
