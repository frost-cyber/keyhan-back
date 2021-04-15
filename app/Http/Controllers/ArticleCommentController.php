<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class ArticleCommentController extends Controller
{

    public function index()
    {
        $comments = Comment::where('commentable_type', 'App\Models\Article')->get();
        return $comments->toJson();
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        $comment = Comment::find($id);
        return $comment->toJson();
    }

    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy(Comment $comment)
    {
        $comment->delete();
        return response('delete successfully', 200);
    }

    public function toggleConfirm(Comment $comment)
    {
        $comment->confirmed = !$comment->confirmed;
        $comment->save();
        return $comment;
    }
}
