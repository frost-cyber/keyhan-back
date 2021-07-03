<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $pages=Page::query();
        if(request()->has("status")){
        	$pages->where('status',\request('status'));
        }
        return $pages->get();
    }

    public function store(Request $request)
    {
           $request->validate([
               'title'=>'required',
               'body'=>'required',
               'status'=>'required|in:active,deactive',
               'slug'=>'required|unique:pages',
           ]);
           Page::create($request->all());
           return  response('create page successfully',200);
    }


    public function show(Page $page,$slug = null)
    {
        if($slug) {
            $page=Page::where('slug',$slug)->firstOrFail();
        }
        return $page;
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title'        => 'required',
            'body'         => 'required',
            'slug'         => 'required|unique:pages,slug,' . $page->id,
            'status'       => 'required|in:active,deactive',
        ]);
        $page->update($request->all());
        return response('update successfully',200);
    }


    public function destroy(Page $page)
    {
        $page->delete();
        return response('delete successfully',200);
    }
}
