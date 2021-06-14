<?php

namespace App\Http\Controllers;

use App\Models\Customization;
use Illuminate\Http\Request;

class CustomizationController extends Controller
{
	public function index(){
		return Customization::query()->latest()->get();
	}
    public function storeCustomization(Request $request){
			$request->validate([
				'name'=>'required',
				'contact'=>'required',
				'discription'=>'required',
				'product_id'=>'required|exists:products,id'
			]);
			Customization::create($request->all());
			return response('create cuctomization Succsessfully');
    }
    public function deleteCuctomization(Customization $customization){
    	$customization->delete();
    	return response('delete cuctomization Succsessfully');
    }
    public function toggleStatus(Customization $customization){
		$customization->status = !$customization->status;
		$customization->save();
		return $customization;
    }
}
