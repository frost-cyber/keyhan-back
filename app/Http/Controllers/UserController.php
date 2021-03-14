<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{

    public function index()
    {
        return User::all();
    }
//    public function store(Request $request)
//    {
//        //
//        return \response( [
//		    'message'  => 'Create User Successfully' ,
//            'user' => NULL ,
//            ] , 200 );
//    }


    public function show(User $user)
    {
        return $user;
    }


//    public function update(Request $request, User $user)
//    {
//        //
//        return \response( [
//		    'message'  => 'Update User Successfully' ,
//            'user' => NULL ,
//            ] , 200 );
//    }


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
