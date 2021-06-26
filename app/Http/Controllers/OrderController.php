<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller {
	public function payCart() {
		$cart = auth()->user()->currentCart();
		if ( $cart->productVariants->isEmpty() ) {
				return response('سبد خرید خالی است',400);
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
				$msg.= " </br>". $productVariant->product->name;
			}
	    	return response( $msg, 400 );
	    }
		if ( ! $cart->address ) {
			return response('ادرسی وجود ندارد',400);
		}

	}
}
