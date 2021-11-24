@extends('layouts.layout-with-nav')

@section('content')

<div style="width: 100%; display:flex; flex-direction: row; justify-content: flex-start; align-items: center;">
     <h2>Teachers</h2>
     <button onclick="window.open('/admin/teacher/create','_self')" style="max-height: 50px; padding: 10px;">Create</button>
     
     <form id="csvTeacherForm" method="POST" action="/admin/teacher/csv" enctype="multipart/form-data">
         <input type="file" id="selectedFile" name="csvFile" style="display: none;" onChange="document.getElementById('csvTeachertForm').submit();" />
         @csrf
         <input type="button" value="CSV Upload" style="max-height: 50px; padding: 10px;" onclick="document.getElementById('selectedFile').click();" />
     </form>
     <p style="float:right; width: fit-content; margin-left: auto;">{{ $result }}</p>
</div>

<div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">
    
     <form style="display: flex; flex-direction: row; flex-wrap: wrap; padding: 20px; width: 100%; border: solid 1px black;" method="POST" action="/admin/student/search">
         <div style="width:50%;">
             <label>
                 Name:
                 <input type="text" class="srchStudntByName" name="srchStudntByName" style="border: solid 1px black;" />
             </label>     
         </div>
         <div style="width:50%;">
             <label>
                 Class:
                 <select type="text" class="srchStudntByClass" name="srchStudntByClass" style="border: solid 1px black;">
                     <option value ="">Classes</option>
                     {!! $class_options !!}
                 </select>
             </label>
         </div><br />
         <div style="width: 100%; display: flex; flex-direction: column; justify-content: flex-end;">
             <button style="max-width: 200px; margin: 0 auto; max-height: 50px; padding: 10px;">Search</button>
         </div>
     </form>

</div>
<div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">
    
    <table class="table" style="border: solid 1px black; border-collapse: collapse; margin-top: 40px; width: 100%;">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Full Name</th>
                <th>Class</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
             @foreach($teacher_table_results as $teacher)
                 <tr>
                     <td>{{$teacher->teacher_id}}</td>
                     <td>{{$teacher->teacher_title}} {{$teacher->first_name}} {{$teacher->last_name}}</td>
                     <td></td>
                     <td><a href="/admin/teacher/edit?id={{$teacher->user_no}}">Edit</a> | <a href="/admin/teacher/delete?id={{$teacher->user_no}}">Delete</a></td>
                 </tr>
             @endforeach
        </tbody>
    </table>
    <div class="paginate-nav">{{$teacher_table_results->onEachSide(3)->links()}}</div>
    
</div>
    
@endsection
