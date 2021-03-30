<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    protected array $validationRules = [];
    protected array $validationCustomMessage = [];
    protected array $validationAttributes = [];

    protected function usernameValidationParams()
    {
        $this->validationRules['username'] = ['required'];

        if ($this->username() === 'mobile') {
            $this->validationRules['username'][] = 'regex:/^9\d{9}$/i';
            $this->validationCustomMessage['username.regex'] = 'موبایل باید با فرمت (--.--.---.---9) باشد.';
            $this->validationAttributes['username'] = 'موبایل';
        } else {
            $this->validationRules['username'][] = 'email';
            $this->validationAttributes['username'] = 'ایمیل';
        }
    }

    protected function username(): string
    {
        if (is_numeric(request('username'))) {
            return 'mobile';
        }

        return 'email';
    }

    protected function validateRequest()
    {
        request()->validate($this->validationRules , $this->validationCustomMessage , $this->validationAttributes);
    }

    protected function passwordValidationParams()
    {
        $this->validationRules['password'] = ['required'];
    }

    protected function credentials(): array
    {
        return [
            $this->username() => request('username') ,
            'password'        => request('password') ,
        ];
    }

    public function checkUsername(Request $request){
        $this->usernameValidationParams();
        $this->validateRequest();

        session()->put('usernameType', $this->username() );
        session()->put('usernameValue', $request->input('username') );

        $status = (boolean)User::query()->where($this->username() , $request->input('username'))->first();
        return response(['status' => $status]);
    }

    public function login(Request $request)
    {
        $this->usernameValidationParams();
        $this->passwordValidationParams();
        $this->validateRequest();

        if (!auth()->attempt($this->credentials())) {
            throw ValidationException::withMessages([
                'username' => [trans('auth.failed')] ,
            ]);
        }

        return auth()->user()->createToken('Web')->plainTextToken;

    }

    public function logout()
    {

    }

}
