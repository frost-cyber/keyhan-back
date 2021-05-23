<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ArticleController extends Controller {

    public function index( Request $request ) {
        $articles = Article::with( [ 'files', 'categories' ] );
        if ( \request()->has( 'category' ) ) {
            function getIds( $model, $relation ) {
                $ids = [ $model->id ];
                if ( ! $model->$relation ) {
                    return $ids;
                }
                foreach ( $model->$relation as $value ) {
                    array_push( $ids, ...getIds( $value, $relation ) );
                }

                return $ids;
            }

            $category = Category::where( 'slug', request( 'category' ) )->first();
            if ( $category ) {
                $articles = $articles->whereHas( 'categories', function ( Builder $query ) use ( $category ) {
                    return $query->whereIn( 'id', getIds( $category, 'children' ) );
                } );
            }
        }
        if ( $request->has( 'pagination' ) ) {
            $articles = $articles->paginate( '4' );
        } else {
            $articles = $articles->get();
        }

        return $articles;
    }


    public function store( Request $request ) {
        $request->validate( [
            'title'        => 'required|min:5',
            'description'  => 'required',
            'slug'         => 'required|unique:articles',
            'tags'         => 'required',
            'status'       => 'required|in:active,deactive',
            'thumbnail'    => 'required|array',
            'thumbnail.id' => 'required|int|exists:files,id'
        ] );
        $this->save( $request->all(), new Article() );

        return response( 'create successfully', '200' );
    }

    public function show( Article $article, $slug = null ) {
        $article = ! $slug ? $article : Article::where( 'slug', $slug )->where( 'status', 'active' )->first();

        return $article->load( [
            'comments' => function ( $query ) {

                $query = $query->where( 'confirmed', true );

                return $query->without( 'commentable' );
            }
        ] )->load( 'categories', 'categories.parent' )->append( [ 'thumbnail' ] );
    }

    public function update( Request $request, Article $article ) {

        $request->validate( [
            'title'        => 'required|min:5',
            'body'         => 'required',
            'description'  => 'required',
            'slug'         => 'required|unique:articles,slug,' . $article->id,
            'tags'         => 'required',
            'status'       => 'required|in:active,deactive',
            'thumbnail'    => 'required|array',
            'thumbnail.id' => 'required|int|exists:files,id'
        ] );
        $this->save( $request->all(), $article );

        return response( 'update article successfully', 200 );
    }

    public function destroy( Article $article ) {
        $article->delete();

        return response( 'delete successfully', 200 );
    }

    protected function save( $data, Article $article ) {
        $article->title          = $data['title'];
        $article->body           = $data['body'];
        $article->description    = $data['description'];
        $article->slug           = $data['slug'];
        $article->tags           = $data['tags'];
        $article->status         = $data['status'];
        $article->comments_count = $data['comments_count'] ?? $article->comments_count ?? 0;
        $article->save();
        $article->categories()->sync( $data['categories']['id'] );
        if ( $data['thumbnail'] ) {
            $article->files()->sync( $data['thumbnail'] ['id'], [ 'default' => true, 'description' => 'Thumbnail', 'number' => 0 ] );
        } else {
            $article->files()->delete();

        }

        return $article;
    }

    public function tags() {
        $articlesTags = Article::all( [ 'tags' ] )->pluck( 'tags' )->toArray();
        $tags         = [];
        foreach ( $articlesTags as $articleTags ) {
            foreach ( $articleTags as $tag ) {
                if ( ! in_array( $tag, $tags ) ) {
                    $tags[] = $tag;
                }
            }
        }

        return $tags;

    }


}
