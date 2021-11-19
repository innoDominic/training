<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

#use App\Http\Controllers;

Route::get('/', function () {
    if(!session()->has('user_no')){

        return view('login', ['result' => '']);
    }else{
        $user_type = session('user_type');

        if($user_type == 0){

            return view('admin', ['page' => 'student']);
        }else if($user_type == 1){

            return view('login', ['result' => 'teacher']);
        }
    }
});

Route::get('/logout', function () {

    session()->flush();
    return redirect('/');

});

Route::get('/admin/{page}', function ($page) {
    $user_type = session('user_type');

    if($user_type == 0){

        if( Str::contains($page, '-create') ){

            return view('admin', [
                'page' => $page,
                'result' => ''
            ]);

        }else{

            return view('admin', ['page' => $page]);

        }
    }else if($user_type == 1){

        return view('teacher', ['page' => 'attendance']);

    }else{

        return view('login', ['result' => '']);

    }
})->name('admin');

Route::get('/teacher/{page}', function ($page) {
    $user_type = session('user_type');

    if($user_type == 0){
        
        return view('admin', ['page' => 'student']);

    }else if($user_type == 1){

        return view('teacher', ['page' => $page]);

    }else{

        return view('login', ['result' => '']);
        
    }
});

Route::post('/', 'LoginController@authenticateUser');
Route::post('/admin/student-create', 'StudentController@createStudent');

/*Route::get('/create-teacher', function () {
    DB::table('user')->insert([
        'user_name' => 'joHiga',
        'first_name' => 'Josuke',
        'last_name' => 'Higashikata',
        'password' => Hash::make('teacher'),
        'user_type' => 1
    ]);

    $user = User::where('user_name', 'joHiga')->first();
    DB::table('teacher')->insert([
        'teacher_title' => 'Sensei',
        'teacher_id' => 'Josuke_0002',
        'user_no' => $user->user_no
    ]);
});*/
