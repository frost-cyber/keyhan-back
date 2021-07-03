<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller {

	public function index() {
		$posts = Post::query();
		if ( request()->has( 'state' ) ) {
			$posts->whereJsonContains( 'states', (int) \request('state'));
		}
		$posts = $posts->latest();

		$posts= $posts->get();
		if($posts->isEmpty()){
			$posts=Post::query()->where( 'id', 1 )->get();
		}
		return $posts;
	}

	public function store( Request $request ) {
		Post::create( $this->validateRequest( $request ) );

		return response( 'create post successfuly', 200 );
	}

	public function show( Post $post ) {
		return $post;
	}

	public function update( Request $request, Post $post ) {
		$post->update( $this->validateRequest( $request ) );

		return response( 'update post successfuly', 200 );
	}

	public function destroy( Post $post ) {
		$post->delete();

		return response( 'delete post successfuly', 200 );
	}

	private function validateRequest( Request $request ) {
		$role = [
			'name'           => 'required',
			'states'         => 'array',
			'states.*'       => 'required|integer|digits_between:1,31',
			'weight'         => 'required|array',
			'weight.*.start' => 'required|numeric',
			'weight.*.end'   => 'required|numeric',
			'weight.*.price' => 'required|numeric',
			'is_free'        => 'nullable|numeric',
		];

		$custommesage = [
			'weight.*.*.required' => 'این فیلد اجباری است',
			'weight.*.*.numeric'  => 'این فیلد باید عدد یا رشته ای از اعداد باشد',
		];

		$customatrebute = [
			'is_free' => 'فیلد رایگان',
		];

		return $request->validate( $role, $custommesage, $customatrebute );
	}

}
