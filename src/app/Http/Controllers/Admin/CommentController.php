<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::with(['user', 'post'])
            ->latest()
            ->paginate(20);

        return view('admin.comments.index', compact('comments'));
    }

    public function toggleVisibility(Comment $comment)
    {
        $comment->update([
            'status' => $comment->status === 'visible' ? 'hidden' : 'visible',
        ]);

        return back()->with('success', '评论状态已更新');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return back()->with('success', '评论已删除');
    }
}
