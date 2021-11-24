@extends('layouts.layout-with-nav')

@section('content')
<div style="width: 100%; display:flex; flex-direction: row; justify-content: flex-start; align-items: center;">
     <h2 style="width:100%; text-align: left;">Edit Class</h2>
     <h2 style="width:100%; text-align: left;">{{ $result }}</h2>
 </div>
 <div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">
     <form style="display: flex; flex-direction: column; width: 100%;" method="POST" action="/admin/class/edit">

         <label> 
             Class Name: 
             <input type="text" value="{{$class_info->classes_name}}" name="classes_name" class="classes_name" style="border: solid 1px black;" />
             @csrf
         </label>
         <input type="text" value="{{ $class_info->classes_no }}" name="classes_no" hidden/>
         <button style="background-color: white; border:solid 1px black; width: fit-content; color: black !important;">Edit</button>

     </form>
 </div>
@endsection