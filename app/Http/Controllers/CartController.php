<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct()
    {
        auth()->loginUsingId(5);

    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product' => 'required|integer|min:1|exists:products,id',
            'product_variant' => 'required|integer|min:1|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = auth()->user()->currentCart();

        $variant = $cart->productVariants()->where('product_variants.id', request('product_variant'))->first();
        $productVariant = ProductVariant::where('id', request('product_variant'))->first();
        if($request->has('increment')){
            $quantity = (int)$request->quantity + ($variant?->pivot?->quantity ?? 0);
        }
        else{
            $quantity = (int)$request->quantity;

        }
        if ($productVariant->selling_price === null) {
            return response('جهت خرید محصول تماس بگیرید', 400);
        }
        if ($quantity > $productVariant->inventory) {
            return response('موجودی کافی نیست', 400);
        }

        if ($variant) {
            $variant->pivot->quantity = $quantity;
            $variant->pivot->save();
            return response([
                'message' => 'IncrementedQuantity',
                'cart' => $cart->load('productVariants')->loadCount('productVariants'),
            ]);
        }
        $cart->productVariants()->attach([
            request('product_variant') => [
                'product_id' => request('product'),
                'quantity' => $quantity,
            ],
        ]);
        return response([
            'message' => 'AddedToCart',
            'cart' => $cart->load('productVariants')->loadCount('productVariants'),
        ]);
    }

//    public function removeFromCart(Request $request)
//    {
//        $request->validate([
//            'product' => 'required|integer|min:1|exists:cart_products,product_id',
//            'product_variant' => 'required|integer|min:1|exists:cart_products,product_variant_id',
//        ]);
//
//      auth()->user()->currentCart()->productVariants()->detach(request('[product_variant'));
//
//        return response([
//            'message' => 'RemovedFromCart',
//            'cart' => auth()->user()->currentCart()->load('product_variants')->loadCount('product_variants'),
//        ]);
//
//    }

    public function currentCart(Request $request)
    {
        $cart = auth()->user()->currentCart();
        if ($request->has('withCount')) {
            $cart = $cart->loadCount($request->withCount);
        }
        if($request->has('with')){
            $cart=$cart->load($request->with);
        }
        return $cart;
    }
    public function removeFromCart(ProductVariant $productvariant){
                auth()->user()->currentCart()->productVariants()->detach($productvariant->id);
                return response('Deatached');
    }

}
