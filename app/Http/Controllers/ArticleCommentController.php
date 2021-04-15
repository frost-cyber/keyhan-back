<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Auth\Authenticatable;
use Illuminate\Http\Request;

class ArticleCommentController extends Controller
{

    public function index()
    {
        $comments = Comment::whereHasMorph('commentable', Article::class);
//                if(\request()->has('confirmed') && (int) request('confirmed') >= 0){
//                    $comments = $comments->where('confirmed' , (int) request('confirmed'));
//
//                }else
        if (\request()->has('confirmed')) {
         $comments=Comment::where('user_id','!=',0)->where('confirmed',1)->with(['user']);
        }

        return $comments->get();
    }

    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $rules = [
            'body' => 'required',
            'article_id' => 'required|exists:articles,id',
            'parent_id' => 'nullable|exists:comments,id'
        ];
        if (!auth()->check()) {
            $rules['name'] = 'required';
            $rules['email'] = 'required|email';
        }
        $this->validate($request, $rules);

        $article = Article::find($request->article_id);

        $data = $request->all();
        $data['user_id'] = auth()->id();

        $article->comments()->create($data);

        return response('create comment successfully', 200);
    }

    public function show($id)
    {
        $comment = Comment::find($id);
        return $comment;
    }

    public function edit($id)
    {
        //
    }


    public function update(Request $request, Comment $comment)
    {
        $this->validate($request, [
            'body' => 'required'
        ]);
        $data = $request->only('body');
        $comment->update($data);

        return response('update comment successfully', 200);
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
