<?php use Illuminate\Support\Str; ?>

@extends('layouts.layout-with-nav')

@if( Str::contains($page, 'student') )

    @if( Str::contains($page, '-create') )
        @section('content')
            <div style="width: 100%; display:flex; flex-direction: row; justify-content: flex-start; align-items: center;">
                <h2 style="width:100%; text-align: left;">Create Student</h2>
                <h2 style="width:100%; text-align: left;">{{ $result }}</h2>
            </div>
            <div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">
                <form style="display: flex; flex-direction: column; width: 100%;" method="POST" action="/admin/student-create">

                    <label> 
                        Student ID: 
                        <input type="text" name="student_id" class="student_id" style="border: solid 1px black;" />
                        @csrf
                    </label>
                    <label> 
                        Username: 
                        <input type="text" name="user_name" class="user_name" style="border: solid 1px black;" />
                        @csrf
                    </label>
                    <label> 
                        Password: 
                        <input type="password" name="password" class="password" style="border: solid 1px black;" />
                        @csrf
                    </label>
                    <label> 
                        First Name: 
                        <input type="text" name="first_name" class="first_name" style="border: solid 1px black;" />
                        @csrf
                    </label>
                    <label> 
                        Last Name: 
                        <input type="text" name="last_name" class="last_name" style="border: solid 1px black;" />
                        @csrf
                    </label>
                    <button style="background-color: white; border:solid 1px black; width: fit-content; color: black !important;">Create</button>

                </form>
            </div>
        @endsection
    @elseif( Str::contains($page, '-edit') )
        @section('content')
        
        @endsection
    @else
        @section('content')

            <div style="width: 100%; display:flex; flex-direction: row; justify-content: flex-start; align-items: center;">
                 <h2>Students</h2>
                 <button onclick="window.open('/admin/student-create','_self')" style="max-height: 50px; padding: 10px;">Create</button>
                 
                 <form id="csvStudentForm" method="POST" action="/admin/student/csv" enctype="multipart/form-data">
                     <input type="file" id="selectedFile" name="csvFile" style="display: none;" onChange="document.getElementById('csvStudentForm').submit();" />
                     @csrf
                     <input type="button" value="CSV Upload" style="max-height: 50px; padding: 10px;" onclick="document.getElementById('selectedFile').click();" />
                 </form>
                 <p style="float:right; width: fit-content; margin-left: auto;">{{ $result }}</p>
            </div>

            <script>
               
            </script>

            <div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">
                
                 <form style="display: flex; flex-direction: row; flex-wrap: wrap; padding: 20px; width: 100%; border: solid 1px black;" method="POST" action="/admin/student">
                     <div style="width:33%;">
                         <label>
                             Name:
                             <input type="text" class="srchStudntByName" name="srchStudntByName" style="border: solid 1px black;" />
                         </label>     
                     </div>
                     <div style="width:33%;">
                         <label>
                             Class:
                             <select type="text" class="srchStudntByClass" name="srchStudntByClass" style="border: solid 1px black;">
                                 <option value ="">Classes</option>
                                 {!! $class_options !!}
                             </select>
                         </label>
                     </div>
                     <div style="width:33%;">
                         <label>
                             Teacher:
                             <select type="text" class="srchStudntByTeacher" name="srchStudntByTeacher" style="border: solid 1px black;">
                                 <option value ="">Teacher</option>
                                 {!! $teacher_options !!}
                             </select>
                         </label>
                     </div><br />
                     <div style="width: 100%; display: flex; flex-direction: column; justify-content: flex-end;">
                         <button style="max-width: 200px; margin: 0 auto; max-height: 50px; padding: 10px;">Search</button>
                     </div>
                 </form>

            </div>
            <div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">

                <style>
                    tr th, tr td{
                        border: solid black 1px;
                        border-collapse: collapse;
                        text-align: center;
                    }
                </style>
                
                <table class="table" style="border: solid 1px black; border-collapse: collapse; margin-top: 40px; width: 100%;">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Class</th>
                            <th>Teacher</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                         <tr>
                            <td>Student ID</td>
                            <td>Full Name</td>
                            <td>Class</td>
                            <td>Teacher</td>
                            <td>Actions</td>
                        </tr>
                    </tbody>
                </table>

            </div>

        @endsection
    @endif

@elseif( Str::contains($page, 'teacher') )

    @if( Str::contains($page, '-create') )
        @section('content')
        
        @endsection
    @elseif( Str::contains($page, '-edit') )
        @section('content')
        
        @endsection
    @else
        @section('content')
        
        @endsection
    @endif

@elseif( Str::contains($page, 'classes') )

    @if( Str::contains($page, '-create') )
        @section('content')
        
        @endsection
    @elseif( Str::contains($page, '-edit') )
        @section('content')
        
        @endsection
    @else
        @section('content')
        
        @endsection
    @endif

@elseif( Str::contains($page, 'plot_classes') )

    @if( Str::contains($page, '-create') )
        @section('content')
        
        @endsection
    @elseif( Str::contains($page, '-edit') )
        @section('content')
        
        @endsection
    @else
        @section('content')
        
        @endsection
    @endif

@elseif( Str::contains($page, 'plot_teacher') )

    @if( Str::contains($page, '-create') )
        @section('content')
        
        @endsection
    @elseif( Str::contains($page, '-edit') )
        @section('content')
        
        @endsection
    @else
        @section('content')
        
        @endsection
    @endif

@else
    <h2>Unkown url: {{ $page }}</h2>
@endif
