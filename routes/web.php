<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login', ['result' => '']);
});

Route::get('/admin', function () {
    if(session('user_type') == 0){
        return view('admin');
    }else if(session('user_type') == 1){
        return view('login', ['result' => 'Teacher']);
    }else{
        return view('login', ['result' => '']);
    }
});

Route::post('/', 'App\Http\Controllers\LoginController@authenticateUser');

/*Route::get('/create-admin', function () {
    DB::table('user')->insert([
        'user_name' => 'joOra',
        'first_name' => 'Jotaro',
        'last_name' => 'Kujo',
        'password' => Hash::make('admin'),
        'user_type' => 0
    ]);
});*/