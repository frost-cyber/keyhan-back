<?php

namespace App\Http\Controllers\Auth;

use App\Drivers\Farazsms;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tzsk\Sms\Channels\SmsChannel;
use Illuminate\Support\Facades\Notification;

class RegisterController extends Controller
{
    protected array $validationRules;
    protected array $validationCustomMessage;
    protected array $validationAttributes;

    protected function username(): string
    {
        if (is_numeric(request('username'))) {
            return 'mobile';
        }
        return 'email';
    }

    protected function validationParams(){
        $this->validationRules['password'] = ['required' , 'min:6'];
        $this->validationRules['username'] = ['required'];

        if ($this->username() === 'mobile'){
            $this->validationRules['username'][] =  'regex:/^9\d{9}$/i';
            $this->validationRules['username'][] =  'unique:users,mobile';
            $this->validationCustomMessage['username.regex'] = 'موبایل باید با فرمت (9##-###-##-##) باشد.';
            $this->validationAttributes['username'] = 'موبایل';
        }else{
            $this->validationRules['username'][] = 'email';
            $this->validationRules['username'][] =  'unique:users,email';
            $this->validationAttributes['username'] = 'ایمیل';
        }
    }

    protected function validationRequest(){
        request()->validate($this->validationRules , $this->validationCustomMessage , $this->validationAttributes);
    }

    public function sendVerifyCode(){
        if (!session()->exists(['usernameType' , 'usernameValue']) || request('username') !== session('usernameValue')){
            return response(['message' => 'نام کاربری شما بررسی نشده است'], 403);
        }

        $code = random_int(100000 , 999999);

        cache()->put('verifyCode_'.session("usernameValue") , $code , 300);

        if (session('usernameType') === 'mobile'){
//            \Tzsk\Sms\Facades\Sms::send('pattern' , function(Farazsms $sms) use($code) {
//                return $sms->pattern('juzfb6mki5' ,['name' => 'کاربر گرامی' , 'verification-code'=> $code] , session("usernameValue"));
//            });

            return response("success , $code",200);
        }

        return response()->noContent();
    }

    public function checkVerifyCode(Request $request){

        if (!session()->exists(['usernameType' , 'usernameValue' , ])){

            return response(['message' => 'نام کاربری شما بررسی نشده است'], 403);

        }else if (! cache()->has('verifyCode_'.session('usernameValue')) ) {

            return response(['message' => 'کد تایید ارسال نشده است'], 403);

        }

        $request->validate(['verifyCode' => 'required|integer']);

        $code = cache()->get('verifyCode_'.session("usernameValue"));
        if ( $code === (int) $request->input('verifyCode')){
            cache()->put('verified_'.session("usernameValue") , true , 300);
            return response('success' , 200);
        }

        cache()->put('verified_'.session("usernameValue") , false , 300);
        return response()->noContent();
    }

    public function register(Request $request){

        $sessionExist = session()->exists(['usernameType' , 'usernameValue' ]);

        if (! $sessionExist || !$request->has('username')  || (string) session('usernameValue') !== (string) $request->username ){
            return response(['message' => 'نام کاربری شما بررسی نشده است'], 403);
        }
        else if (! cache()->has('verifyCode_'.session('usernameValue')) ){

            return response(['message' => 'کد تایید ارسال نشده است'], 403);

        }
        else if (! cache()->get('verified_'.session('usernameValue')) ){

            return response(['message' => 'نام کاربری شما تایید نشده است'], 403);
        }

        $this->validationParams();
        $this->validationRequest();

        $data = [
            'name' => 'نام خود را وارد نمایید',
            'password' => \Hash::make($request->input('password')),
            session('usernameType') => $request->input('username'),
            session('usernameType').'_verified_at' => now(),
        ];

        $user = User::create($data);

        auth()->login($user);

        return $user->createToken('web' )->plainTextToken;

    }

}
