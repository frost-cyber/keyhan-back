<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller {

    public function addToCart( Request $request ) {
        $request->validate( [
            'product'         => 'required|integer|min:1|exists:products,id',
            'product_variant' => 'required|integer|min:1|exists:product_variants,id',
            'quantity'        => 'required|integer|min:1',
        ] );

        $cart = auth()->user()->currentCart();

        if ( $variant = $cart->productVariants()->where( 'id', request( 'product_variant' ) )->first() ) {
            $cart->productVariants()->sync( [
                request( 'product_variant' ) => [
                    'product_id' => request( 'product' ),
                    'quantity'   => (int) $variant->pivot->quantity + (int) request( 'quantity' ),
                ],
            ] );

            return response( [
                'message' => 'IncrementedQuantity',
                'cart' => $cart->load('product_variants')->loadCount( 'product_variants' ),
            ] );
        }

        $cart->productVariants()->attach( [
            request( 'product_variant' ) => [
                'product_id' => request( 'product' ),
                'quantity'   => request( 'quantity' ),
            ],
        ] );

        return response( [
            'message' => 'AddedToCart',
            'cart' => $cart->load('product_variants')->loadCount( 'product_variants' ),
        ] );
    }

    public function removeFromCart( Request $request ) {
        $request->validate( [
            'product'         => 'required|integer|min:1|exists:cart_products,product_id',
            'product_variant' => 'required|integer|min:1|exists:cart_products,product_variant_id',
        ] );

        auth()->user()->currentCart()->productVariants()->detach( request( '[product_variant' ) );

        return response( [
            'message' => 'RemovedFromCart',
            'cart' => auth()->user()->currentCart()->load('product_variants')->loadCount( 'product_variants' ),
        ] );
    }
}
