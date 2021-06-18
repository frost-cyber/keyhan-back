<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StoreCategoryController extends Controller {

    public function index( Request $request ) {
        $query = Category::query()->whereType( Category::TYPE_STORE );
        $with  = is_array( $request->input( 'with' ) ) ? $request->input( 'with' ) : [ $request->input( 'with' ) ];
        if ( $request->has( 'with' ) && ! count( array_diff( $with, Category::RELATIONS ) ) ) {
            $query = $query->with( $with );
        }

        if ( $request->has( 'with' ) && in_array( 'parents', $with ) ) {
            $query = $query->where( 'parent_id', (int) $request->input( 'children' ) );
        }

        if ( $request->has( 'parent' ) ) {
            $query = $query->where( 'parent_id', (int) $request->input( 'parent' ) ?: NULL );
        }

        return $query->get();
    }

    public function store( CategoryRequest $request ): Response {
        return \response( [
            'message'  => 'Create Category Successfully',
            'category' => $this->save( $request->all() ),
        ], 200 );
    }

    protected function save( array $data, Category $category = NULL ): ?Category {
        //IF Create Category
        if ( ! $category ) {
            $category = new Category();
        }

        $category->name      = $data['name'];
        $category->slug      = \Str::slug( $data['slug'] );
        $category->parent_id = $data['parent_id'] ?? NULL;
        $category->type      = Category::TYPE_STORE;
        $category->save();

        return $category;
    }

    public function show( Category $category ): Category {
        return $category;
    }

    public function update( CategoryRequest $request, Category $category ): Response {
        return \response( [
            'message'  => 'Update Category Successfully',
            'category' => $this->save( $request->all(), $category ),
        ], 200 );
    }

    public function destroy( Category $category ): Response {
        if ( $category->products()->exists() ) {
            return \response( [
                'message' => 'The category cannot be removed because it has products',
            ], 400 );
        }

        $category->delete();

        return \response( [
            'message'  => 'Delete Category Successfully',
            'category' => $category,
        ], 200 );

    }

}
