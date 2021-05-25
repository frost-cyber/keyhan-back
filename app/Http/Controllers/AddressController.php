<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index(){
        Auth::loginUsingId( 5 );
        if ( Auth::check() ) {

            $addresses = Auth::user()->addresses;
            return $addresses;
        } else {
            dd( 'no login' );
        }

    }

    public function delete(Address $address){
        $address->delete();
        return response('delete address successfully',200);
    }
    public function store(Request $request ){
        $request->validate( [
            'name'          => 'required',
            'last_name'     => 'required',
            'email'         => 'required|email',
            'mobile'        => 'required|max:15',
            'phone'         => 'required|max:11',
            'description'   => 'required',
            'address'       => 'required',
            'postcode'         => 'required|max:10',

        ],[],[
            'postcode' =>'کد پستی',
        ]);
        Auth::user()->addresses()->create($request->all());

        return response( 'create address successfully', '200' );
    }
    public function update(Request $request,Address $address){
        $this->validate($request,[
            'name'          => 'required',
            'last_name'     => 'required',
            'email'         => 'required|email',
            'mobile'        => 'required|max:15',
            'phone'         => 'required|max:11',
            'description'   => 'required',
            'address'       => 'required',
            'state'       => 'required',
            'city'       => 'required',
            'postcode'         => 'required|max:10',
        ],[],[
            'postcode'=>'کد پستی',
        ]);
        $address->update($request->all());
        return response('update address successfully',200);
    }
}
