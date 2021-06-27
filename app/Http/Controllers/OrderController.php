<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\Cart;

class OrderController extends Controller {
    public function payCart() {
        $cart = auth()->user()->currentCart();

        $this->validateCart($cart);

        $order = auth()->user()->orders()->create( [
            'order_number' => random_int( 100000, 999999 ),
        ] );

        $orderProductVariants = [];
        foreach ( $cart->productVariants as $variant ) {

            $priceType = 'selling_price';
            if ( $variant->pivot->quantity >= $variant->minimum_wholesale ) {
                $priceType = 'wholesale_price';
            } elseif ( $variant->discounted_price ) {
                $priceType = 'discounted_price';
            }

            $orderProductVariant[ $variant->id ] = [
                'purchase_price' => $variant->purchase_price,
                'price' => $variant->{$priceType},
                'price_type' => $priceType,
                'quality' => $variant->pivot->quantity,
            ];
        }

        $order->productVariants()->attach( $orderProductVariants );

        $shipment = $order->shipments()->newModelInstance( [
            'status' => 'درحال ارسال',
        ] );

        $shipment->address()->associate( 1 );

        $order->payments()->create([
            'gateway' => '',
            'status' => 'پرداخت نشده'
        ]);

        return $shipment->save();
    }

    private function validateCart(Cart $cart){
        if ( $cart->productVariants->isEmpty() ) {
            return response( 'سبد خرید خالی است', 400 );
        }
        $productVariantsNotEnoughInventory = [];
        foreach ( $cart->productVariants as $productVariant ) {
            if ( $productVariant->inventory < $productVariant->pivot['quantity'] ) {
                $productVariantsNotEnoughInventory[] = $productVariant;
            }
        }
        if ( $productVariantsNotEnoughInventory ) {
            $msg = 'محصولات زیر موجودی آن کافی نیست';
            foreach ( $productVariantsNotEnoughInventory as $productVariant ) {
                $msg .= " </br>" . $productVariant->product->name;
            }

            return response( $msg, 400 );
        }
        if ( ! $cart->address ) {
            return response( 'ادرسی وجود ندارد', 400 );
        }
    }
}
