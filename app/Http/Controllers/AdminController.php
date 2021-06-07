<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	public function index()
	{

		return User::where('is_admin',1)->get();
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
	    $this->validate( $request, [
		    'name'          => 'required',
		    'last_name'     => 'required',
		    'email'         => 'required|email|unique:users',
		    'mobile'        => 'required|max:15|unique:users',
		    'phone'         => 'required|max:11|unique:users',
		    'national_code' => 'required|max:10|unique:users',
		    'avatar'        => 'required|array',
		    'avatar.id'     => 'required|int|exists:files,id',
		    'password'      => 'required|min:6'
	    ],[],[
		    'avatar' => 'عکس',
		    'national_code' => 'کد ملی'
	    ]);
	    $data=$request->except(['avatar','password']);
	    $data['password']=\Hash::make($request->input('password'));
	    $data['is_admin']=true;
	    $user=User::create($data);
	    $user->files()->sync($request->input('avatar')['id']);
	    return response('create admin successfully',200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function show(User $user)
	{
		return $user;
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
	    $this->validate( $request, [
		    'name'          => 'required',
		    'last_name'     => 'required',
		    'email'         => 'required|email|unique:users,email'.$user->id,
		    'mobile'        => 'required|max:15|unique:users,mobile'.$user->id,
		    'phone'         => 'required|max:11|unique:users,phone'.$user->id,
		    'national_code' => 'required|max:10|unique:users,national_code'.$user->id,
		    'avatar'        => 'required|array',
		    'avatar.id'     => 'required|int|exists:files,id',
		    'password'      => 'min:6'
	    ],[],[
		    'avatar' => 'عکس',
		    'national_code' => 'کد ملی'
	    ]);
	    $data=$request->except(['avatar','password']);
	    if($request->has('password')){
		    $data['password']=\Hash::make($request->input('password'));
	    }
	    $data['is_admin']=true;
	    $user->update($data);
	    $user->files()->sync($request->input('avatar')['id']);
	    return response('update admin successfully',200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function destroy(User $user)
	{
		$user->delete();

		return \response( [
			'message'  => 'Delete User Successfully' ,
			'user' => $user ,
		] , 200 );
	}
}
