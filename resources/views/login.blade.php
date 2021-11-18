@extends('layouts.layouts')

@section('content')
<div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
    <style>
        button, label{ margin: 0 auto; }
        button { max-width: 150px; width: 100%; margin-top: 20px; }
    </style>

    <form style="color: white !important; display: flex; flex-direction: column; justify-content: space-between;" method="POST" action="/">
        <h1 style="text-align: center;">Login</h1>
        <h4 style="text-align: center; max-width: 300px">{{ $result }}</h4>
        <label> 
            Username: 
            <input type="text" name="username" class="username" />
            @csrf
        </label>
        <label> 
            Password: 
            <input type="password" name="password" class="password" />
            @csrf
        </label>
        <button style="background-color: white; color: black !important;">LOGIN</button>
    </form>
</div>
@endsection