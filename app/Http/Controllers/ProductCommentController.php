<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductCommentController extends Controller
{
    public function index(){
        $comments = Comment::whereHasMorph('commentable' , Product::class );

        if (\request()->has('confirmed')){
            $confirmed = (boolean)\request('confirmed');
        }

        $comments->where('confirmed' , $confirmed??true);
        return $comments->latest()->get();
    }

    public function show(Comment $comment){

    }
    public function store(Request $request){

    }
    public function update(Request $request , Comment $comment){

    }
    public function destroy(Comment $comment){

    }
}
