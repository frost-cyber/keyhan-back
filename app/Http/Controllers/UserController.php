<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\In;
use Spatie\Permission\Models\Permission;
use Symfony\Component\Console\Input\Input;

class UserController extends Controller {
	public function currentUser() {
		$user= auth()->user()->load( [
			'files',
			'productsWishlist' => function ( $query ) {
				return $query->select( 'id' );
			},
		] );
		$id=$user->productsWishlist->pluck('id');
		$user=$user->toArray();
		$user['products_wishlist']=$id;
		return $user;

	}

	public function index() {
		return User::where( 'is_admin', 0 )->get();
	}

	public function show( User $user ) {
		return $user;
	}

	public function destroy( User $user ) {
		$user->delete();

		return \response( [
			'message' => 'Delete User Successfully',
			'user'    => $user,
		], 200 );
	}

	protected function save( array $data, User $user = null ) {
		//If Create User
		if ( $user == null ) {
			$user = new User();
		}

		return $user;
	}

	public function update( Request $request, User $user ) {
		$this->validate( $request, [
			'name'          => 'required',
			'last_name'     => 'required',
			'email'         => 'required|email',
			'mobile'        => 'required|max:15',
			'phone'         => 'required|max:11',
			'national_code' => 'required|max:10',
			'avatar'        => 'required|array',
			'avatar.id'     => 'required|int|exists:files,id',
			'password'      => 'min:6',
		], [], [
			'avatar'        => 'عکس',
			'national_code' => 'کد ملی',
		] );
		$data = $request->except( [ 'password', 'avatar' ] );
		if ( $request->input( 'password' ) ) {
			$data['password'] = \Hash::make( $request->input( 'password' ) );
		}
		if ( $request->has( 'avatar' ) ) {
			$user->files()->sync( $request->input( 'avatar' )['id'], [ 'default' => true, 'description' => 'avatar', 'number' => 0 ] );
		} else {
			$user->files()->delete();
		}
		$user->update( $data );

		return response( 'update user successfully', 200 );

	}

}
