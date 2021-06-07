<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function currentUser(){
        return auth()->user()->load(['files']);
    }
    public function index()
    {
        return User::all();
    }

    public function show(User $user)
    {
        return $user;
    }

    public function destroy(User $user)
    {
        $user->delete();

        return \response( [
		    'message'  => 'Delete User Successfully' ,
            'user' => $user ,
            ] , 200 );
    }

    protected function save ( array $data , User $user = NULL )
    {
        //If Create User
        if ( $user == NULL ) {
            $user = new User();
        }

        return $user;
    }
}
