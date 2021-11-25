<?php use Illuminate\Support\Str; ?>

@extends('layouts.layout-with-nav')

@section('content')

<div style="width: 100%; display:flex; flex-direction: row; justify-content: flex-start; align-items: center;">
     <h2>Plot Classes</h2>
</div>
<div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">
    
     <form style="display: flex; flex-direction: row; flex-wrap: wrap; padding: 20px; width: 100%; border: solid 1px black;" method="POST" action="/admin/plot-class">
         <div style="width:50%;">
             <label>
                 Select Class:
                 <select type="text" class="srchStudntByClass" name="srchStudntByClass" style="border: solid 1px black;">
                     {!! $class_options !!}
                 </select>
                 @csrf
             </label>     
         </div>
         <div style="width: 50%; display: flex; flex-direction: column; justify-content: flex-end;">
             <button style="max-width: 200px; margin: 0 auto; max-height: 50px; padding: 10px;">View</button>
         </div>
     </form>

</div>
<div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">
    
    <table class="table" style="border: solid 1px black; border-collapse: collapse; margin-top: 40px; width: 100%;">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
             @foreach($student_table_results as $student)
                 <tr>
                     <td>{{$student->student_id}}</td>
                     <td>{{$student->last_name}} {{$student->first_name}}</td>
                     <td><a href="/admin/plot-class/delete?id={{$student->user_no}}">Remove</a></td>
                 </tr>
             @endforeach
        </tbody>
    </table>
    
</div>

<div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">
    
     <form style="display: flex; flex-direction: row; flex-wrap: wrap; padding: 20px; width: 100%; border: solid 1px black;" method="POST" action="/admin/plot-class/store">
         <div style="width:50%;">
             <label>
                 Add Student:
                 <select type="text" class="student_id" name="student_id" style="border: solid 1px black;">
                     {!! $student_options !!}
                 </select>
                 @csrf
             </label>     
         </div>
         <div style="width: 50%; display: flex; flex-direction: column; justify-content: flex-end;">
             <button style="max-width: 200px; margin: 0 auto; max-height: 50px; padding: 10px;">Add</button>
         </div>
     </form>

</div>

@endsection
