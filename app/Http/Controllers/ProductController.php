<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Product;
use App\Models\ProductVariant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Str;

class ProductController extends Controller
{

    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function index(Request $request)
    {
        $products = Product::query();

        $relations = [...Product::RELATIONS];

        foreach(Category::RELATIONS as  $item){
            $relations[] = 'categories.'.$item;
        };

        foreach(ProductVariant::RELATIONS as  $item){
            $relations[] = 'variants.'.$item;
        };

        $with = (is_array($request->input('with'))?$request->input('with') : [$request->input('with')]);

        if ($request->has('with') && !count(array_diff( $with, $relations))) {

            $products = $products->with($with);
        }

        if (request()->has('condition')) {
            $products = $products->where('condition' , (int)request('condition'));
        } else {
            $products = $products->where('condition' , 1);
        }

        if (request()->has('published_at')) {
            $preg = preg_match('/^([+-]?)(\d{4}-\d{1,2}-\d{1,2})$/' , request('published_at') , $match);
            if ($preg) {
                $op = $match[1] === '+' ? '>' : '<';
                $time = $match[2];
            }
        } else {
            $op = '<=';
            $time = Carbon::now()->format('Y-m-d');
        }

        $products = $products->where('published_at' , $op , $time);

        return $products->get();
    }

    public function store(ProductRequest $request)
    {
        return \response([
            'message'  => 'Create Product Successfully' ,
            'category' => $this->save($request->all()) ,
        ] , 200);
    }

    protected function save(array $data , Product $product = NULL)
    {
        //If Create Product
        if ($product == NULL) {
            $product = new Product();
        }

        $product->name = $data['name'];
        $product->slug = Str::slug($data['slug']);
        $product->sku = $data['sku'];
        $product->type = $data['type'];
        $product->condition = $data['condition'];
        $product->review = $data['review'];
        $product->short_review = $data['short_review'];
        $product->description = $data['description'] ?? NULL;
        $product->is_virtual = $data['type'] == 2;
        $product->brand_id = $data['brand_id'] ?? NULL;
        $product->published_at = $data['published_at'] ?? NULL;

        $product->save();

        $this->syncCategories($data['categories'] , $product);

        $images = $data['images'];
        $productImages = [];

        foreach ($images as $image){
            $VariantIndex= array_key_exists('variant_index' , $image)? $image['variant_index'] : false ;
            $Variant = array_key_exists ((int)$VariantIndex , $data['variants'])? $data['variants'][(int)$VariantIndex]: false;

            if($VariantIndex === null || $VariantIndex === '' || !$Variant ){
                $productImages[] = $image;
                continue;
            }

            if (!array_key_exists('images' , $Variant)){
                $data['variants'][(int)$VariantIndex]['images'] = [];
            }
            $data['variants'][(int)$VariantIndex]['images'][]= $image;
        }


        $this->syncImages($productImages , $product);
        $this->syncAttributes($data['attributes'] , $product);
        $this->syncVariants($data['variants'] , $product);

        $product->refresh();

        return $product;
    }

    protected function syncCategories(array $categories , Product $product)
    {
        $product->categories()->sync($categories);
    }

    protected function syncImages(array $imagesData , Product $product)
    {
        $product->files()->sync(collect($imagesData)->pluck('id')->toArray());
    }

    protected function syncAttributes(array $attributesData , Product $product)
    {
        $syncData = [];
        foreach ($attributesData as $i => $attribute) {
            $syncData[$attribute['id']] = [
                'group_name'   => $attribute['group_name'] ?? NULL ,
                'number'       => $i ,
            ];
        }

        $product->attributes()->sync($syncData);
    }

    protected function syncVariants(array $variantsData , Product $product)
    {
        $issetvariants = [];

        foreach ($variantsData as $index => $variantData) {
            if ($variantData['id'] ?? FALSE) {
                $issetvariants[] = $variantData['id'];
            }
        }

        $product->variants()->whereNotIn('id' , $issetvariants)->delete();

        foreach ($variantsData as $index => $variantData) {
            $syncData = [
                'attribute_id'      => $variantData['attribute_id']??null,
                'purchase_price'    => $variantData['purchase_price'] ?? NULL ,
                'selling_price'     => $variantData['selling_price'] ?? NULL ,
                'discounted_price'  => $variantData['discounted_price'] ?? NULL ,
                'wholesale_price'   => $variantData['wholesale_price'] ?? NULL ,
                'minimum_wholesale' => $variantData['minimum_wholesale'] ?? NULL ,
                'inventory'         => $variantData['inventory'] ?? NULL ,
            ];

            if ($variantData['id'] ?? FALSE) {
                $var = $product->variants()->where('id',$variantData['id'])->first();
                $var->update($syncData);
            } else {
                $var = $product->variants()->create($syncData);
            }
            $var->files()->sync(collect($variantData['images']??[])->pluck('id')->toArray());
        }

    }

    public function show(Request $request , Product $product , $slug = null)
    {
        $product = !$slug ? $product : Product::where('slug' , $slug)->firstOrFail() ;

        $relations = Product::RELATIONS;

        foreach(Category::RELATIONS as  $item){
            $relations[] = 'categories.'.$item;
        };

        foreach(Comment::RELATIONS as  $item){
            $relations[] = 'comments.'.$item;
        };

        foreach(ProductVariant::RELATIONS as  $item){
            $relations[] = 'variants.'.$item;
        };

        $with = $request->input('with');
        (is_array($with) ?: $with = [$with]);
        if ($request->has('with') && !array_diff( $with, $relations)) {
            if ($key = array_search('comments' , $with) >= 0){
                $with = array_splice($with,$key, 1);
                $with['comments'] = function($query){
                    return $query->where('confirmed' , TRUE);
                };
            };
            $product = $product->load($with);
        }
        return $product;
    }

    public function update(ProductRequest $request , Product $product)
    {
        return \response([
            'message'  => 'Update Product Successfully' ,
            'category' => $this->save($request->all() , $product) ,
        ] , 200);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return \response([
            'message'  => 'Delete Product Successfully' ,
            'category' => $product ,
        ] , 200);
    }
}
