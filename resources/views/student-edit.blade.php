@extends('layouts.layout-with-nav')

@section('content')
<div style="width: 100%; display:flex; flex-direction: row; justify-content: flex-start; align-items: center;">
     <h2 style="width:100%; text-align: left;">Edit Student</h2>
     <h2 style="width:100%; text-align: left;">{{ $result }}</h2>
 </div>
 <div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">
     <form style="display: flex; flex-direction: column; width: 100%;" method="POST" action="/admin/student/update">

        @method('PUT')
        @php
        #dd($student_info);
        @endphp
        
        <label> 
            Student ID: 
            <input type="text" value="{{$student_info->student_id}}" name="student_id" class="student_id" style="border: solid 1px black;" />
            @csrf
        </label>
        <label> 
            Username: 
            <input type="text" value="{{$student_info->user_name}}" name="user_name" class="user_name" style="border: solid 1px black;" />
            @csrf
        </label>
        <label> 
            Password: 
            <input type="password" value="{{$student_info->password}}" name="password" class="password" style="border: solid 1px black;" />
            @csrf
        </label>
        <label> 
            First Name: 
            <input type="text" value="{{$student_info->first_name}}" name="first_name" class="first_name" style="border: solid 1px black;" />
            @csrf
        </label>
        <label> 
            Last Name: 
            <input type="text" value="{{$student_info->last_name}}" name="last_name" class="last_name" style="border: solid 1px black;" />
            @csrf
        </label>
        <input type="text" value="{{ $student_info->user_no }}" name="user_no" hidden/>
        <button style="background-color: white; border:solid 1px black; width: fit-content; color: black !important;">Edit</button>

     </form>
 </div>
@endsection