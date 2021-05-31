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
            $comments->where('confirmed' , (boolean)request('confirmed'));
        }
        return $comments->latest()->get();
    }

    public function show(Comment $comment)
    {
        if (!str_ends_with($comment->commentable_type , 'Product')) {
            abort(404);
        }
        return $comment->load([
            'user' , 'commentable' , 'parent',
        ]);
    }

    public function store(Request $request)
    {
        $roles = [
            'body'  => 'required' ,
            'name'  => 'required|min:3' ,
            'email' => 'required|email',
            'product_id' => 'required|exists:products,id',
            'parent_id' => 'nullable|exists:comments,id'
        ];

        if (Auth()->check()) {
            unset($roles['name'] , $roles['email']);
        }

        $request->validate($roles , [] , ['body' => 'نظر']);

        $comment = $request->all();
        $comment['user_id'] = auth()->id();

        Product::findOrFail($request->product_id)->comments()->create($comment);

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
