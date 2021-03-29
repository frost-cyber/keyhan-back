<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $articles=Article::with(['files','categories'])->get();
        return $articles->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:5',
            'description' => 'required',
            'slug' => 'required|unique:articles',
            'tags' => 'required',
            'status' => 'required|in:active,deactive'
        ]);
        $this->save($request->all(),new Article());
        return response('create successfully','200');
    }

    public function show(Article $article){
        return $article->load('categories')->append(['thumbnail']);
    }

    public function update(Request $request, Article $article)
    {

        $request->validate([
            'title' => 'required|min:5',
            'body' => 'required',
            'description' => 'required',
            'slug' => 'required|unique:articles,slug,'.$article->id,
            'tags' => 'required',
            'status' => 'required|in:active,deactive'
        ]);
        $this->save($request->all(), $article);
        return response('update article successfully', 200);
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return response('delete successfully', 200);
    }

    protected function save($data, Article $article)
    {
        $article->title = $data['title'];
        $article->body = $data['body'];
        $article->description = $data['description'];
        $article->slug = $data['slug'];
        $article->tags = $data['tags'];
        $article->status = $data['status'];
        $article->comments_count = $data['comments_count'] ?? $article->comments_count ?? 0;
        $article->save();
        $article->categories()->sync($data['categories']['id']);
//        $article->files()->sync($data['image_id'] , ['default' => true , 'description' => 'Thumbnail' , 'number' => 0]);

        return $article;
    }

    public function tags()
    {
        $articlesTags = Article::all(['tags'])->pluck('tags')->toArray();
        $tags=[];
        foreach($articlesTags as $articleTags){
            foreach ($articleTags as $tag){
                if (!in_array($tag,$tags)){
                    $tags[]=$tag;
                }
            }
        }
        return $tags;

    }


}
