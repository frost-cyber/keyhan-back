<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\Cart;
use Shetabit\Multipay\Invoice;
use Shetabit\Multipay\RedirectionForm;
use Shetabit\Payment\Facade\Payment;

class OrderController extends Controller {
	public function payCart() {
		$cart = auth()->user()->currentCart();
		$this->validateCart( $cart );
		$order = auth()->user()->orders()->create( [
			'order_number' => random_int( 100000, 999999 ),
		] );

		$this->orderAttachVariants( $order, $cart );
		$shipment = new Shipment( [
			'status'     => 'درحال ارسال',
			'address_id' => 1,
		] );
		$shipment->address()->associate( 1 );
		$order->shipments()->save( $shipment );
		$pay = $this->pay( $order->total_price );
		$order->payments()->create( [
			'gateway' => 'زرین پال',
			'status'  => 'پرداخت نشده',
			'data'    => [ 'authority' => $pay['transactionId'] ],
		] );
		$cart->delete();

		return $pay['redirectForm']->toJson();
	}

	private function orderAttachVariants( Order $order, Cart $cart ) {
		$orderProductVariants = [];
		$orderTotalPrice      = 0;
		$cart->productVariants->each( function ( $variant ) use ( &$orderProductVariants, &$orderTotalPrice ) {

			$priceType = 'selling_price';
			if ( $variant->pivot->quantity >= $variant->minimum_wholesale ) {
				$priceType = 'wholesale_price';
			} elseif ( $variant->discounted_price ) {
				$priceType = 'discounted_price';
			}
			$orderProductVariants[ $variant->id ] = [
				'purchase_price' => $variant->purchase_price,
				'price'          => $variant->{$priceType},
				'price_type'     => $priceType,
				'quantity'       => $variant->pivot->quantity,
				'total_price'    => $variant->pivot->quantity * $variant->{$priceType},
				'extra'          => json_encode( [ 'variant' => $variant->getAttributes() ] ),
			];

			$orderTotalPrice += $variant->pivot->quantity * $variant->{$priceType};
		} );
		$order->total_price = $orderTotalPrice;
		$order->save();
		$order->productVariants()->sync( $orderProductVariants );
	}

	private function validateCart( Cart $cart ) {
		if ( $cart->productVariants->isEmpty() ) {
			abort( 400, 'سبد خرید خالی است' );
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

			abort( 400, $msg );
		}
		if ( ! $cart->address ) {
			abort( 400, 'ادرسی وجود ندارد' );
		}
	}

	private function pay( $amount ) {
		$invoice = ( new Invoice() )->amount( $amount );

		return [
			'redirectForm'  => Payment::purchase( $invoice )->pay(),
			'transactionId' => $invoice->getTransactionId(),
		];
	}
}
