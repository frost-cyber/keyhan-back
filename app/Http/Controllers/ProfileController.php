<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller {
    public function user() {
        if ( Auth::check() ) {
            return Auth::user()->load( 'files' );
        } else {
            dd( 'no login' );
        }

    }

    public function update( Request $request ) {
        $this->validate( $request, [
            'name'          => 'required',
            'last_name'     => 'required',
            'email'         => 'required|email|unique:users,email,' . auth()->id(),
            'mobile'        => 'required|max:15|unique:users,mobile,' . auth()->id(),
            'phone'         => 'required|max:11|unique:users,phone,' . auth()->id(),
            'national_code' => 'required|max:10|unique:users,national_code,' . auth()->id(),
            'avatar'        => 'required|array',
            'avatar.id'     => 'required|int|exists:files,id',
        ],[],[
            'avatar' => 'اواتار',
            'national_code' => 'کد ملی'
        ]);
        $user = Auth::user();
        $data = $request->except( [ 'avatar' ] );
        if ( $request->has( 'avatar' ) ) {
            $user->files()->sync( $request->input( 'avatar' )['id'], [ 'default' => true, 'description' => 'avatar', 'number' => 0 ] );
        } else {
            $user->files()->delete();
        }
        $user->update( $data );

        return response( 'update profile successfully', 200 );

    }

    public function updateAvatar( Request $request ) {
        $this->validate( $request, [
            'avatar'    => 'required|array',
            'avatar.id' => 'required|int|exists:files,id',
        ] );
        Auth::user()->files()->sync( $request->input( 'avatar' )['id'], [ 'default' => true, 'description' => 'avatar', 'number' => 0 ] );

        return response( 'update avatar successfully', 200 );
    }

    public function password( Request $request ) {
        $this->validate( $request, [
            'password'         => 'required|min:6|confirmed',
            'current_password' => [
                'required',
                function ( $attribute, $value, $fail ) {
                    if ( ! \Hash::check( $value, \Auth::user()->password ) ) {
                        $fail( 'پسورد قبلی اشتباه است' );
                    }
                },
            ],
        ] );
        \Auth::user()->fill( [
            'password' => \Hash::make( $request->password ),
        ] )->save();

        return response( 'update password successfully', 200 );
    }
}
