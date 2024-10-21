<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Group;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Models\Friend;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            UserSeeds::class,
            PostSeeds::class,
            CommentSeeds::class,


            TagSeeds::class,
            GroupSeeds::class,
            FriendSeeds::class,
        ]);


        $users = User::all();
        $posts = Post::all();
        $comments = Comment::all();

        $tags = Tag::all();
        $groups = Group::all();


        /*foreach ($comments as $comment) {
            $comment->user()->associate($users->random());
            $comment->save();
        }*/


        /*foreach ($posts as $post) {
            $comments = Comment::factory()->count(3)->create([
                'post_id' => $post->id,
                'user_id' => $users->random()->id
            ]);
        }*/

        foreach ($groups as $group) {
            $group->members()->attach($users->random(rand(1, 5)));
        }

        /*$users = User::factory()->count(10)->create();
        $posts = Post::factory()->count(10)->create();

        foreach ($posts as $post) {
            $comments = Comment::factory()->count(3)->create([
                'post_id' => $post->id,
                'user_id' => $users->random()->id
            ]);
        }
        \Log::info("Created comments for post ID: " . $post->id);*/

    }
}
