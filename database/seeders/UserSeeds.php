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

        $MarosBerdis_model = User::factory()->create([
            'username' => 'xberdi01',
            'email' => 'marosberdis@gmail.com',
            'password' => 'ciscocisco',
            'first_name' => 'Maroš',
            'last_name' => 'Berdis'
        ]);

        $generatedUsers = User::factory()->count(8)->create();
    }
}
