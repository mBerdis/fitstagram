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
            'last_name' => 'Koscelnik',
            'role' => 4
        ]);

        $MarosBerdis_model = User::factory()->create([
            'username' => 'xberdi01',
            'email' => 'marosberdis@gmail.com',
            'password' => 'ciscocisco',
            'first_name' => 'Maroš',
            'last_name' => 'Berdis'
        ]);

        $admin_model = User::factory()->create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => 'admin',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'role' => 4
        ]);

        $mod_model = User::factory()->create([
            'username' => 'moderator',
            'email' => 'moderator@example.com',
            'password' => 'moderator',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'role' => 3
        ]);

        $user_model = User::factory()->create([
            'username' => 'user',
            'email' => 'user@example.com',
            'password' => 'user',
            'first_name' => 'John',
            'last_name' => 'Smith',
        ]);

        $generatedUsers = User::factory()->count(8)->create();
    }
}
