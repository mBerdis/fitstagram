<?php

namespace App\Http\Controllers\Post;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Services\UserAuthenticationService;

class CommentController extends Controller
{
    public function store(Request $request,UserAuthenticationService $authService)
    {
        if (!$authService->role_access(UserRole::USER)) {
            return back();
        }

        $request->validate([
            'content' => 'required|string|max:255',
            'post_id' => 'required|exists:posts,id',
            'user' => 'required|exists:users,id',
        ]);


        $comment = Comment::create([
            'message' => $request->content,
            'post_id' => $request->post_id,
            'user_id' => $request->user, // Associate comment with the authenticated user
        ]);

        return back();
    }

    public function delete_comment(Request $request, UserAuthenticationService $authService)
    {
        $user = auth()->user();

        $request->validate([
            'comment_id' => 'required|exists:comments,id',
        ]);

        $comment = Comment::findOrFail($request->comment_id);

        // verify user has rights
        if ( !($comment->user->id === $user->id)                  // is not author and
             && !$authService->role_access(UserRole::MODERATOR)) // is not atleast mod
        {
            return back()->with('error', 'User has unsufficient edit rights.');
        }

        $comment->delete();

        return back()->with('success', 'Comment removed.');
    }

}

