<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StoreCategoryController extends Controller
{

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $query = Category::query()->whereType(Category::TYPE_STORE);
        $with = is_array($request->input('with'))?$request->input('with'):[$request->input('with')];
        if ($request->has('with') && !count(array_diff( $with, Category::RELATIONS))) {
            $query = $query->with($with);
        }

        if ($request->has('with') && in_array('parents' , $with)){
            $query = $query->where('parent_id' ,(int) $request->input('children'));
        }

        if ($request->has('parent')){
            $query = $query->where('parent_id' ,(int) $request->input('parent') ?: null);
        }

        return $query->get();
    }

    /**
     * Store a newly created resource in storage.
     * @param CategoryRequest $request
     * @return Response
     */
    public function store(CategoryRequest $request): Response
    {
        return \response([
            'message'  => 'Create Category Successfully' ,
            'category' => $this->save($request->all()) ,
        ] , 200);
    }

    protected function save(array $data , Category $category = NULL): ?Category
    {
        //IF Create Category
        if (!$category) {
            $category = new Category();
        }

        $category->name = $data['name'];
        $category->slug = \Str::slug($data['slug']);
        $category->parent_id = $data['parent_id'] ?? NULL;
        $category->type = Category::TYPE_STORE;
        $category->save();

        return $category;
    }

    /**
     * Display the specified resource.
     * @param Category $category
     * @return Category
     */
    public function show(Category $category): Category
    {
        return $category;
    }

    /**
     * Update the specified resource in storage.
     * @param CategoryRequest $request
     * @param Category $category
     * @return Response
     */
    public function update(CategoryRequest $request , Category $category): Response
    {
        return \response([
            'message'  => 'Update Category Successfully' ,
            'category' => $this->save($request->all() , $category) ,
        ] , 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param Category $category
     * @return Response
     * @throws Exception
     */
    public function destroy(Category $category): Response
    {
        $category->delete();

        return \response([
            'message'  => 'Delete Category Successfully' ,
            'category' => $category ,
        ] , 200);
    }

}
