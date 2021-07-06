<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller {
	public function index() {
		if ( Auth::check() ) {
			$addresses = Auth::user()->addresses;

			return $addresses;
		} else {
			dd( 'no login' );
		}

	}

	public function delete( Address $address ) {
		$address->delete();

		return response( 'delete address successfully', 200 );
	}

	public function store( Request $request ) {
		$this->validateRequest();
		Auth::user()->addresses()->create( $request->all() );

		return response( 'create address successfully', '200' );
	}

	public function update( Request $request, Address $address ) {
		$this->validateRequest();
		$address->update( $request->all() );

		return response( 'update address successfully', 200 );
	}

	protected function validateRequest() {
		\request()->validate( [
			'name'        => 'required',
			'last_name'   => 'required',
			'email'       => 'required|email',
			'mobile'      => 'required|size:10',
			'phone'       => 'required|size:10',
			'description' => 'required',
			'address'     => 'required',
			'postcode'    => 'required|size:10',

		], [], [
			'postcode' => 'کد پستی',
		] );
	}
}
