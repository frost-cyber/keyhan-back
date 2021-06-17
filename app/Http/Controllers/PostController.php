<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller {

	public function index() {
		return Post::latest()->get();
	}

	public function store( Request $request ) {
		$this->validateRequest($request);
		Post::create($request->validated());
		return response('create post successfuly',200);
	}

	public function show( Post $post ) {
		//
	}

	public function update( Request $request, Post $post ) {
		$this->validateRequest($request);
		$post::update($request->validated());
		return response('update post successfuly',200);
	}


	public function destroy( Post $post ) {
		$post->delete();
		return response('delete post successfuly',200);
	}
	private function validateRequest(Request $request){
		$request->validate([
			'name'=>'required',
			'states'=>'required|array',
			'states.*'=>'required|integer|digit_bitween:1,31',
			'weight'=>'required|array',
			'is_free'=>'nullable|numeric'
		]);
	}

}
