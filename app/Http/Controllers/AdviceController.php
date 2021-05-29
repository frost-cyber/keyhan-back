<?php

namespace App\Http\Controllers;

use App\Models\Advice;
use Illuminate\Http\Request;

class AdviceController extends Controller
{
    public function storeAdvice(Request $request){
       $this->validate($request,[
           'name'=>'required',
           'phone'=>'required|min:11|max:11',
           'subject'=>'required',
       ]);
        $data=$request->only('name','phone','subject');
        Advice::create($data);
        return response('create Advice successfully');
    }
    public function showAdvice(){
       return Advice::all();
    }
    public function deleteAdvice(Advice $advice){
        $advice->delete();
        return response('delete Advice successfully',200);
    }
    public function toggleCheck(Advice $advice)
    {
        $advice->check = !$advice->check;
        $advice->save();
        return $advice;
    }
}
