<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller {

	public function index() {
		return Post::latest()->get();
	}

	public function store( Request $request ) {
		Post::create($this->validateRequest($request));
		return response('create post successfuly',200);
	}

	public function show( Post $post ) {
		//
	}

	public function update( Request $request, Post $post ) {
		$post::update($this->validateRequest($request));
		return response('update post successfuly',200);
	}


	public function destroy( Post $post ) {
		$post->delete();
		return response('delete post successfuly',200);
	}
	private function validateRequest(Request $request){
		return $request->validate([
			'name'=>'required',
			'states'=>'required|array',
			'states.*'=>'required|integer|digits_between:1,31',
			'weight'=>'required|array',
			'is_free'=>'nullable|numeric'
		]);
	}

}
