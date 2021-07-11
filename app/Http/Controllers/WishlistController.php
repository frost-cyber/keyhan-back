<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->productsWishlist()->with(['files', 'variants']);

        if ($request->has('products')) {
             $query->where('is_virtual', false);
        } else if ($request->has('courses')) {
             $query->where('is_virtual', true);
        }
        if($request->has('limit')){
        	$query->limit($request->input('limit'));
        }

        return $query->get();

    }

}
