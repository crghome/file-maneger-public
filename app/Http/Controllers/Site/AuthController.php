<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(){
        if(Auth::check()){ return redirect('resource'); }

        $arrData = (object)[
            'title' => 'Вход'
        ];

        return view('main.views.login', compact('arrData'));
    }

    public function confirm(Request $request){
        $response = array('status' => false, 'message' => 'Не предвиденная остановка сервера', 'data' => array());
        try {
            if (!$request->isMethod('POST')){ throw new \Exception('Некорректное обращение к серверу'); }
            // $data = (object)($request->data??[]);
            if (empty($request->login)){ throw new \Exception('Необходимо ввести логин'); }
            if (empty($request->password)){ throw new \Exception('Необходимо ввести пароль'); }
            
            $login = env('USER_ROOT_LOGIN');
            $hash = env('USER_ROOT_HASH');

            // if($login == $request->login && Hash::check($request->password, $hash)){
            if($login == $request->login && Hash::check($request->password, $hash)){
                // $request->session()->regenerate();
                // session(['id' => 1, 'user' => ['login' => $request->login]]);
                return redirect('resource');
            } else {
                $response['message'] = 'Не совпадает Логин или пароль';
            }
            // $response['message'] = Hash::make($request->password);//'Не совпадает Логин или пароль';
            
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
        }
        return $request->wantsJson()
                    ? response()->json($response)
                    : back()->withErrors(['message' => $response['message']])->onlyInput('login');
    }
}
