<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Product;
use App\Models\ProductVariant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Str;

class ProductController extends Controller {

    protected $product;

    public function __construct( Product $product ) {
        $this->product = $product;
    }

    public function index( Request $request ) {
        $products = Product::query();

        if ($request->has('type')){
            $products = $products->where('type' , (int) request('type'));
        }

        $with = ( is_array( $request->input( 'with' ) ) ? $request->input( 'with' ) : [ $request->input( 'with' ) ] );

        if ( $request->has( 'with' ) && ! count( array_diff( $with, Product::ALL_RELATIONS() ) ) ) {

            $products = $products->with( $with );
        }

        if ( request()->has( 'condition' ) ) {
            $conditions=array_map(fn($val )=>(boolean) $val,request( 'condition' ));
            $products = $products->whereIn( 'condition', $conditions);
        } else {
            $products = $products->where( 'condition', 1 );
        }

        if ( request()->has( 'published_at' ) ) {
            $preg = preg_match( '/^([+-]?)(\d{4}-\d{1,2}-\d{1,2})$/', request( 'published_at' ), $match );
            if ( $preg ) {
                $op   = $match[1] === '+' ? '>' : '<';
                $time = $match[2];
            }
        } else {
            $op   = '<=';
            $time = Carbon::now()->format( 'Y-m-d' );
        }

        $products = $products->where( 'published_at', $op, $time );

        if ( $request->has( 'category' ) ) {
            $category = Category::where( 'slug', request( 'category' ) )->first();
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

            if ( $category ) {
                $products = $products->whereHas( 'categories', function ( Builder $query ) use ( $category ) {
                    return $query->whereIn( 'id', getIds( $category, 'children' ) );
                } );
            }
        }

        if ( $request->has( 'categories' ) ) {
            $categories = Category::whereIn( 'slug', request( 'categories' ) )->get('id');
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
            if ( $categories ) {
                $categoriesID = [];
                foreach ($categories as $cat ){
                    array_push($categoriesID , ...getIds($cat , 'children'));
                }
                $products = $products->whereHas( 'categories', function ( Builder $query ) use ( $categoriesID ) {
                    return $query->whereIn( 'id', $categoriesID );
                } );
            }
        }

        if ($request->has( 'products' )) {
            $productsSlug = is_array($request->input('products'))? $request->input('products'):[$request->input('products')];
            $products = $products->orWhereIn('slug' , $productsSlug);
        }

        if ( $request->has( 'brands' ) ) {
            $brands   = ( is_array( $request->input( 'brands' ) ) ? $request->input( 'brands' ) : [ $request->input( 'brands' ) ] );
            $products = $products->whereHas( 'brand', function ( Builder $query ) use ( $brands ) {
                return $query->whereIn( 'slug', $brands );
            } );
        }

        if ( $request->has( 'search' ) ) {
            $products = $products->where( 'name', 'like', "%{$request->input('search')}%" );
        }

        $products = $products->orderByDesc(
            ( new \App\Models\ProductVariant )->select( 'selling_price' )
                                              ->whereColumn( 'product_id', 'products.id' )
                                              ->orderBy( 'selling_price' )->limit( 1 )
        );

        if ( $request->has( 'pagination' ) ) {
            $products = $products->paginate( '15' );
        } else {
            $products = $products->get();
        }

        return $products;
    }

    public function store( ProductRequest $request ) {
        return \response( [
            'message'  => 'Create Product Successfully',
            'category' => $this->save( $request->all() ),
        ], 200 );
    }

    public function show( Request $request, Product $product, $slug = NULL ) {
        $product = ! $slug ? $product : Product::where( 'slug', $slug )->firstOrFail();

        $with = $request->input( 'with' );
        ( is_array( $with ) ?: $with = [ $with ] );
        if ( $request->has( 'with' ) && ! array_diff( $with, Product::ALL_RELATIONS() ) ) {
            $key = array_search( 'comments', $with );
            if ( $key && $key >= 0 ) {
                array_splice( $with, $key, 1 );
                $with['comments'] = function ( $query ) {
                    return $query->where( 'confirmed', TRUE );
                };
            }
            $product = $product->load( $with );
        }

        return $product;
    }

    public function update( ProductRequest $request, Product $product ) {
        return \response( [
            'message'  => 'Update Product Successfully',
            'category' => $this->save( $request->all(), $product ),
        ], 200 );
    }

    public function destroy( Product $product ) {
        $product->delete();

        return \response( [
            'message'  => 'Delete Product Successfully',
            'category' => $product,
        ], 200 );
    }

    public function toggleWishlist(Product $product){
        auth()->loginUsingId(5);
        try{
            $wish=auth()->user()->productsWishlist()->where('id',$product->id)->first();
            if($wish){
                auth()->user()->productsWishlist()->detach($product->id);
                return response('Deatached');
            }
            auth()->user()->productsWishlist()->attach($product->id);
            return response('Attached');
        }catch ( \Exception $e){
            return $e;
        }
    }

    protected function save( array $data, Product $product = NULL ) {
        //If Create Product
        if ( $product == NULL ) {
            $product = new Product();
        }

        $product->name         = $data['name'];
        $product->slug         = Str::slug( $data['slug'] );
        $product->sku          = $data['sku'];
        $product->type         = $data['type'];
        $product->condition    = $data['condition'];
        $product->review       = $data['review'];
        $product->short_review = $data['short_review'];
        $product->description  = $data['description'] ?? NULL;
        $product->is_virtual   = $data['type'] == 2;
        $product->brand_id     = $data['brand_id'] ?? NULL;
        $product->published_at = $data['published_at'] ?? NULL;

        $product->save();

        $this->syncCategories( $data['categories'], $product );

        $images        = $data['images'];
        $productImages = [];

        foreach ( $images as $image ) {
            $VariantIndex = array_key_exists( 'variant_index', $image ) ? $image['variant_index'] : FALSE;
            $Variant      = array_key_exists( (int) $VariantIndex, $data['variants'] ) ? $data['variants'][ (int) $VariantIndex ] : FALSE;

            if ( $VariantIndex === NULL || $VariantIndex === '' || ! $Variant || (int) $data['type'] !== 2 ) {
                $productImages[] = $image;
                continue;
            }

            if ( ! array_key_exists( 'images', $Variant ) ) {
                $data['variants'][ (int) $VariantIndex ]['images'] = [];
            }
            $data['variants'][ (int) $VariantIndex ]['images'][] = $image;
        }


        $this->syncImages( $productImages, $product );
        $this->syncAttributes( $data['attributes'], $product );
        $this->syncVariants( $data['variants'], $product );
        if ((int)$data['type'] === 3){
            $this->syncLinks($data['links'] , $product);
        }
        $product->refresh();

        return $product;
    }

    protected function syncCategories( array $categories, Product $product ) {
        $product->categories()->sync( $categories );
    }

    protected function syncImages( array $imagesData, Product $product ) {
        $product->files()->sync( collect( $imagesData )->pluck( 'id' )->toArray() );
    }

    protected function syncAttributes( array $attributesData, Product $product ) {
        $syncData = [];
        foreach ( $attributesData as $i => $attribute ) {
            $syncData[ $attribute['id'] ] = [
                'group_name' => $attribute['group_name'] ?? NULL,
                'number'     => $i,
            ];
        }

        $product->attributes()->sync( $syncData );
    }

    protected function syncVariants( array $variantsData, Product $product ) {
        $issetvariants = [];

        foreach ( $variantsData as $index => $variantData ) {
            if ( $variantData['id'] ?? FALSE ) {
                $issetvariants[] = $variantData['id'];
            }
        }

        $product->variants()->whereNotIn( 'id', $issetvariants )->delete();

        foreach ( $variantsData as $index => $variantData ) {
            $syncData = [
                'attribute_id'      => $variantData['attribute_id'] ?? NULL,
                'purchase_price'    => $variantData['purchase_price'] ?? NULL,
                'selling_price'     => $variantData['selling_price'] ?? NULL,
                'discounted_price'  => $variantData['discounted_price'] ?? NULL,
                'wholesale_price'   => $variantData['wholesale_price'] ?? NULL,
                'minimum_wholesale' => $variantData['minimum_wholesale'] ?? NULL,
                'inventory'         => $variantData['inventory'] ?? NULL,
            ];

            if ( $variantData['id'] ?? FALSE ) {
                $var = $product->variants()->where( 'id', $variantData['id'] )->first();
                $var->update( $syncData );
            } else {
                $var = $product->variants()->create( $syncData );
            }
            $var->files()->sync( collect( $variantData['images'] ?? [] )->pluck( 'id' )->toArray() );
        }

    }

    protected function syncLinks( array $linksData, Product $product ) {
        $linksData = collect( $linksData );
        $product->links()->whereNotIn( 'id', $linksData->pluck( 'id' )->toArray() )->delete();
        foreach ( $linksData as $index => $linkData ) {
            $link = $product->links()->where( 'id', $linkData['id']??0 )->firstOrNew();
            $link->title       = $linkData['title'];
            $link->link        = $linkData['link'];
            $link->description = $linkData['description'];
            $link->number      = $index;
            $link->save();
        }
    }
}
