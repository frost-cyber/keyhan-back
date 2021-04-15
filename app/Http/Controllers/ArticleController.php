<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
class ArticleController extends Controller
{

    public function index()
    {
        $articles = Article::with(['files','categories']);
        if (\request()->has('category')){
            $articles = $articles->whereHas('categories',function (Builder $query){
                $query->where('id',\request('category'));
            } );
        }
        $articles=$articles->get();
        return $articles;
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:5',
            'description' => 'required',
            'slug' => 'required|unique:articles',
            'tags' => 'required',
            'status' => 'required|in:active,deactive',
            'thumbnail'=> 'required|array',
            'thumbnail.id'=>'required|int|exists:files,id'
        ]);
        $this->save($request->all(),new Article());
        return response('create successfully','200');
    }

    public function show(Article $article){



        return $article->load([ 'comments' => function($query){
            if (is_numeric(\request('commentsConfirmed'))){
                // اینجا در آخر باز هم همون آبجکت رو بر میگردونه واسه همون باز هم ما به همه متود ها و اون آبجکت دسترسی داریم
                // پس اگر میخوایم یک رست فول اپی آی داشته باشیم باید یک کوءری واسه اون درخواست درست کنیم و برا اساس هر کدوم از شرط ها مون به همین شکل خط پایین کوءری هامون رو تغییر میدیم هر شرط رو که میزارم بر اساس هر منطقی چون در آخر باز همون آبجکت رو برمیگردونه دوباره من داخل یک مقدار ذخیره میکنیم وکه میشه همون کورءری
                $query = $query->where('confirmed' , (int) \request('commentsConfirmed'));
            }

//            $query = $query->where()
//            $query = $query->where()
//            $query = $query->where()
//            $query = $query->where()
//            $query = $query->where()
            return $query->without('commentable');
        }] )->load( 'categories')->append(['thumbnail']);
    }

    public function update(Request $request, Article $article)
    {

        $request->validate([
            'title' => 'required|min:5',
            'body' => 'required',
            'description' => 'required',
            'slug' => 'required|unique:articles,slug,'.$article->id,
            'tags' => 'required',
            'status' => 'required|in:active,deactive',
            'thumbnail'=> 'required|array',
            'thumbnail.id'=>'required|int|exists:files,id'
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
        if ($data['thumbnail']){
            $article->files()->sync($data['thumbnail'] ['id'], ['default' => true , 'description' => 'Thumbnail' , 'number' => 0]);
        }
        else{
            $article->files()->delete();

        }

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
