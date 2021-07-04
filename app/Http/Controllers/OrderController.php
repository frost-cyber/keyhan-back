<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Payment as PaymentModel;
use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\Cart;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Invoice;
use Shetabit\Multipay\RedirectionForm;
use Shetabit\Payment\Facade\Payment;

class OrderController extends Controller {
	public function __construct() {
		auth()->loginUsingId(1);
	}
	public function index(Request $request){
		$orders=Order::query();
		$with = ( is_array( $request->input( 'with' ) ) ? $request->input( 'with' ) : [ $request->input( 'with' ) ] );
		if ( $request->has( 'with' ) && ! count( array_diff( $with, Order::ALL_RELATIONS() ) ) ) {
			$orders->with( $with );
		}
		if($request->has('user')){
			$orders->where('user_id',(int) $request->input('user'));
		}
		if ( request()->has( 'sort' ) ) {
			$preg = preg_match( '/^([+-]?)(.*)$/', request( 'sort' ), $match );
			if ( $preg ) {
				$op   = $match[1] === '+' ? 'asc' : 'desc';
				$column = $match[2];
				$orders->orderBy($column,$op);
			}
		}
		if($request->has('paginate')){
			return $orders->paginate(5);
		}
		return $orders->get();
	}
	public function show(Order $order){
	return	$order->load(['user','payments','shipments.address','productVariants.product.files','productVariants.attribute']);
	}
	public function saveChange(Order $order,Request $request){
		$request->validate([
			'tracking_code' => 'size:24',
			'status' =>'required|in:0,1,2'
		],[],[
			'tracking_code' => 'کد رهگیری',
			'status' => 'وضعیت'
		]);
		$order->status = $request->status;
		$order->save();
		$order->shipments[0]->tracking_code = $request->tracking_code;
		$order->shipments[0]->save();
		return response('change successfully',200);
	}
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
			'amount'   => $order->total_price,
			'data'    => [ 'authority' => $pay['transactionId'] ],
		] );
//		$cart->delete();

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

	public function checkPayment(Request $request){
		$payment=PaymentModel::query()->whereJsonContains('data->authority',$request->authority)->firstOrFail();
		if($request->input('status') == 'NOK'){

		}
		if ($request->input('status') == 'OK'){
			try{
//				$responce=\Http::withHeaders([
//					'accept' => 'application/json',
//					'content-type' => 'application/json'
//				])->post('https://sandbox.zarinpal.com/pg/v4/payment/verify.json',[
//					"merchant_id"=> "753869421753869421753869421123456789",
//					'amount'=> $payment->amount,
//					'authority'=>$request->authority,
//				]);
//				dd($responce->body());
//				dd(Payment::amount((int)$payment->amount)->transactionId($request->authority));
				$receipt =Payment::amount((int)$payment->amount)->transactionId($request->authority)->verify();
				dd($receipt);
			}catch (InvalidPaymentException $exception ){
					dd($exception);
			}
		}
	}
}
