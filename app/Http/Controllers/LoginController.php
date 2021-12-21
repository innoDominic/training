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

        $user = User::where('user_name', $request->input('username'))->first();

        if($user->count() > 0 && $user->password === $request->input('password')){

            session()->put('user_no', $user->user_no);
            session()->put('user_type', $user->user_type);

            # 0 : Admin / 1 : Teacher / 2 : Student
            if($user->user_type == 0){

                return redirect('/admin/student');

            }else if($user->user_type == 1){

                return redirect('teacher/attendance');

            }else if($user->user_type == 2){

                return view('login', ['result' => 'Student']);

            }else{

                return view('login', ['result' => 'Unkown User: ' . $user->user_type]);
                
            }
        }

        // Authentication failed...
        return view('login', ['result' => 'Failed, please try again']);

    }

    public function authenticateUserApi(Request $request){
        
        $user = User::where('user_name', $request->input('user_name'))->first();

        if($user->count() > 0 && $user->password === $request->input('password')){
        
            $access_token = $user->createToken('authToken')->accessToken;
            return response([
                "user" => $user,
                "access_token" => $access_token
            ]);

        }

        return response(["message" => "Failed to login"]);

    }

    public function loginUsername(){
        return 'username';
    }
}

