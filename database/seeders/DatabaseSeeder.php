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



        foreach ($groups as $group) {
            $group->members()->attach($group->owner);

            // non owner users
            $randomUsers = $users->where('id', '!=', $group->owner)->random(rand(1, 5));
            $group->members()->attach($randomUsers);
        }

        foreach ($groups as $group) {
            $group->posts()->attach($posts->random(rand(1, 5)));
        }

        foreach ($posts as $post) {
            $post->tags()->attach($tags->random(rand(1,5)));
        }

        foreach ($posts as $post) {
            $num_of_likes = rand(2, 6);
            $post->like_count = $num_of_likes;
            $post->liked_by()->attach($users->random($num_of_likes)->pluck('id'));
            $post->save();
        }



    }
}
