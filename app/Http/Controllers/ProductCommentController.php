<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductCommentController extends Controller
{
    public function index()
    {
        $comments = Comment::whereHasMorph('commentable' , Product::class)->with('commentable');

        if (\request()->has('confirmed') && \request('confirmed') >= 0) {
            $comments->where('confirmed' , (boolean) request('confirmed'));
        }
        return $comments->latest()->get();
    }

    public function show(Comment $comment)
    {
        if (!str_ends_with($comment->commentable_type , 'Product')){
            abort(404);
        }
        return $comment->load([
            'user', 'commentable' , 'parent'
        ]);
    }

    public function store(Request $request)
    {
        $comment = new Comment();

        if (Auth()->check()) {
            $comment->user_id = auth()->id();
        } else {
            $comment->name = $data['name'] ?? NULL;
            $comment->email = $data['email'] ?? NULL;
        }

        if ($request->has('parent_id')) {
            $comment->parent_id = (int)$request->parent_id ?: NULL;
        }

        $comment->body = $request->body;
        Product::findOrFail($request->product_id)->comments()->save($comment);

        return response('Create comment successfully');
    }

    public function update(Request $request , Comment $comment)
    {
        $comment->body = $request->body;
        $comment->save();
        return response('Update comment Successfully');
    }

    public function toggleConfirm(Comment $comment)
    {
        $comment->confirmed = !$comment->confirmed;
        $comment->save();
        return $comment;
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response('Delete comment successfully');
    }
}
