<?php

namespace Database\Seeders;


use App\Models\Comment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeds extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //


        $comments = Comment::factory()->count(20)->create();


    }
}
