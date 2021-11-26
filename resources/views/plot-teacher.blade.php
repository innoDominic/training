@extends('layouts.layout-with-nav')

@section('content')

<div style="width: 100%; display:flex; flex-direction: row; justify-content: flex-start; align-items: center;">
     <h2>Plot Teachers</h2>
</div>
<div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">
    
     <form id="searchForm" style="display: flex; flex-direction: row; flex-wrap: wrap; padding: 20px; width: 100%; border: solid 1px black;" method="POST" action="/admin/plot-teacher">
         <div style="width:50%;">
             <label>
                 Select Teacher:
                 <select type="text" class="selected_teacher" name="selected_teacher" style="border: solid 1px black;">
                     @foreach($teacher_options as $teacher)
                         <option value="{{$teacher->user_no}}">({{$teacher->teacher_id}}) {{$teacher->teacher_title}} {{$teacher->first_name}} {{$teacher->last_name}} </option>
                     @endforeach
                 </select>
                 @csrf
             </label>     
         </div>
         <div style="width: 50%; display: flex; flex-direction: column; justify-content: flex-end;">
             <button style="max-width: 200px; margin: 0 auto; max-height: 50px; padding: 10px;">View</button>
         </div>
     </form>

     <script>
         $(".selected_teacher").val("{{$selected_teacher}}");
     </script>

</div>
<div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">
    
    <table class="table" style="border: solid 1px black; border-collapse: collapse; margin-top: 40px; width: 100%;">
        <thead>
            <tr>
                <th>Class</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
             @foreach($class_table_results as $class)
                 <tr>
                     <td>{{$class->classes_name}}</td>
                     <td><a href="/admin/plot-teacher/delete?id={{$class->classes_no}}">Remove</a></td>
                 </tr>
             @endforeach
        </tbody>
    </table>
    
</div>

<div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center; margin-top:40px;">
    
     <form style="display: flex; flex-direction: row; flex-wrap: wrap; padding: 20px; width: 100%; border: solid 1px black;" method="POST" action="/admin/plot-class/plot-student">
         <div style="width:50%;">
             <label>
                 Add Class:
                 <select type="text" class="user_no" name="user_no" style="border: solid 1px black;">
                     @foreach($class_options as $class)
                         <option value="{{$class->classes_no}}">{{$class->classes_name}}</option>
                     @endforeach
                 </select>
                 @csrf
             </label>
             <input type="text" class="selected_teacher_to_plot" name="selected_teacher_to_plot" value="{{$selected_teacher}}" hidden />   
             @csrf 
         </div>
         <div style="width: 50%; display: flex; flex-direction: column; justify-content: flex-end;">
             <button style="max-width: 200px; margin: 0 auto; max-height: 50px; padding: 10px;" class="addClassBtn">Add</button>
         </div>
     </form>

</div>

@endsection
