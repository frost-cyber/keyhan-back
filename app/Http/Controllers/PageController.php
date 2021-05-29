<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pages=Page::query();
        return $pages->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function show(Page $page,$slug = null)
    {
        if($slug) {
            $page=Page::where('slug',$slug)->firstOrFail();
        }
        return $page;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function destroy(Page $page)
    {
        $page->delete();
        return response('delete successfully',200);
    }
}
