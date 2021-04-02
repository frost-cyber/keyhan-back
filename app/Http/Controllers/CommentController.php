<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    public function index()
    {
            $comments = Comment::all()->where('commentable_type','article');
             return  $comments->toJson();
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
}
