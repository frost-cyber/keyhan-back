<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller {

	public function index() {

		return User::where( 'is_admin', 1 )->get();
	}

	public function store( Request $request ) {
		$this->validate( $request, [
			'name'          => 'required',
			'last_name'     => 'required',
			'email'         => 'required|email|unique:users',
			'mobile'        => 'required|max:15|unique:users',
			'phone'         => 'required|max:11|unique:users',
			'national_code' => 'required|max:10|unique:users',
			'avatar'        => 'required|array',
			'avatar.id'     => 'required|int|exists:files,id',
			'password'      => 'required|min:6',
			'role'          => 'required|exists:roles,id'
		], [], [
			'avatar'        => 'عکس',
			'national_code' => 'کد ملی',
		] );
		$data             = $request->except( [ 'avatar', 'password','role' ] );
		$data['password'] = \Hash::make( $request->input( 'password' ) );
		$data['is_admin'] = true;
		$user             = User::create( $data );
		$user->assignRole($request->input('role'));
		$user->files()->sync( $request->input( 'avatar' )['id'] );

		return response( 'create admin successfully', 200 );

	}

	public function show( User $user ) {
		$role=$user->roles->first()?->id;
		$user=$user->toArray();
		$user['role']=$role;
		return $user;
	}

	public function update( Request $request, User $user ) {
		$this->validate( $request, [
			'name'          => 'required',
			'last_name'     => 'required',
			'email'         => 'required|email|unique:users,email,' . $user->id,
			'mobile'        => 'required|max:15|unique:users,mobile,' . $user->id,
			'phone'         => 'required|max:11|unique:users,phone,' . $user->id,
			'national_code' => 'required|max:10|unique:users,national_code,' . $user->id,
			'avatar'        => 'required|array',
			'avatar.id'     => 'required|int|exists:files,id',
			'password'      => 'min:6',
			'role'          => 'required|exists:roles,id'
		], [], [
			'avatar'        => 'عکس',
			'national_code' => 'کد ملی',
		] );
		$data = $request->except( [ 'avatar', 'password' ] );
		if ( $request->has( 'password' ) ) {
			$data['password'] = \Hash::make( $request->input( 'password' ) );
		}
		$data['is_admin'] = true;
		$user->update( $data );
		$user->syncRoles($request->input('role'));
		$user->files()->sync( $request->input( 'avatar' )['id'] );

		return response( 'update admin successfully', 200 );
	}

	public function destroy( User $user ) {
		$user->delete();

		return \response( [
			'message' => 'Delete User Successfully',
			'user'    => $user,
		], 200 );
	}
}
