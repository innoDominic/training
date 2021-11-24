@extends('layouts.layout-with-nav')

@section('content')
<div style="width: 100%; display:flex; flex-direction: row; justify-content: flex-start; align-items: center;">
     <h2 style="width:100%; text-align: left;">Create Class</h2>
     <h2 style="width:100%; text-align: left;">{{ $result }}</h2>
 </div>
 <div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">
     <form style="display: flex; flex-direction: column; width: 100%;" method="POST" action="/admin/class/create">

         <label> 
             Class Name: 
             <input type="text" name="class_name" class="class_name" style="border: solid 1px black;" />
             @csrf
         </label>
         <button style="background-color: white; border:solid 1px black; width: fit-content; color: black !important;">Create</button>

     </form>
 </div>
@endsection