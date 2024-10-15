<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeds extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $MatejKoscelnik_model = User::factory()->create([
            'username' => 'xkosce01',
            'email' => 'xkosco01@example.com',
            'password' => 'secret',
            'first_name' => 'Matej',
            'last_name' => 'Koscelnik'
        ]);

        $generatedUsers = User::factory()->count(9)->create();
    }
}
