<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class LoginController extends Controller
{
    public function authenticateUser(Request $request){

        if (Auth::attempt([
                'user_name' => $request->input('username'), 
                'password' => $request->input('password')
            ])) {

            $user = User::where('user_name', $request->input('username'))->first();

            $request->session()->put('user_no', $user->user_no);
            $request->session()->put('user_type', $user->user_type);

            # 0 : Admin / 1 : Teacher / 2 : Student
            if($user->user_type == 0){

                return redirect('/admin');
            }else if($user->user_type == 1){

                return view('login', ['result' => 'Teacher']);
            }else if($user->user_type == 2){

                return view('login', ['result' => 'Student']);
            }else{

                return view('login', ['result' => 'Unkown User: ' . $user->user_type]);
            }
        }

        // Authentication failed...
        return view('login', ['result' => 'Failed, please try again']);
    }
}
