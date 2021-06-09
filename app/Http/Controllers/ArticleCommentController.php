<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;

class ArticleCommentController extends Controller {

	public function index() {
		$comments = Comment::whereHasMorph( 'commentable', Article::class )->with( [ 'commentable', 'user' ] );
		if ( request()->has( 'limit' ) ) {
			$comments = $comments->limit( request( 'limit' ) );
		}
		if ( \request()->has( 'confirmed' ) && \request( 'confirmed' ) >= 0 ) {
			$comments = $comments->where( 'confirmed', (boolean) request( 'confirmed' ) );
		} else {
			$comments = $comments->where( 'confirmed', true );
		}

		return $comments->latest()->get();
	}

	public function store( Request $request ) {
		$rules = [
			'body'       => 'required',
			'article_id' => 'required|exists:articles,id',
			'parent_id'  => 'nullable|exists:comments,id',
		];
		if ( ! auth()->check() ) {
			$rules['name']  = 'required';
			$rules['email'] = 'required|email';
		}
		$this->validate( $request, $rules, [], [ 'body' => 'نظر' ] );

		$article = Article::findOrFail( $request->article_id );

		$data            = $request->all();
		$data['user_id'] = auth()->id();

		$article->comments()->create( $data );

		return response( 'create comment successfully', 200 );
	}

	public function show( $id ) {
		$comment = Comment::find( $id );

		return $comment;
	}

	public function edit( $id ) {
		//
	}


	public function update( Request $request, Comment $comment ) {
		$this->validate( $request, [
			'body' => 'required',
		] );
		$data = $request->only( 'body' );
		$comment->update( $data );

		return response( 'update comment successfully', 200 );
	}


	public function destroy( Comment $comment ) {
		$comment->delete();

		return response( 'delete successfully', 200 );
	}

	public function toggleConfirm( Comment $comment ) {
		$comment->confirmed = ! $comment->confirmed;
		$comment->save();

		return $comment;
	}
}

