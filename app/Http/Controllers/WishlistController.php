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
            $query = $query->where('is_virtual', false);
        } else if ($request->has('courses')) {
            $query = $query->where('is_virtual', true);
        }

        return $query->get();

    }
    public function lastWishlist(Request $request){
     return auth()->user()->productsWishlist()->limit(3)->with(['files','variants'])->get();
    }

}
