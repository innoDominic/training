@extends('layouts.layout-with-nav')

@section('content')

<div style="width: 100%; display:flex; flex-direction: row; justify-content: flex-start; align-items: center;">
     <h2>Teachers</h2>
     <button onclick="window.open('/admin/teacher/create','_self')" style="max-height: 50px; padding: 10px;">Create</button>
     
     <form id="csvTeacherForm" method="POST" action="/admin/teacher/create/csv" enctype="multipart/form-data">
         <input type="file" id="selectedFile" name="csvFile" style="display: none;" onChange="document.getElementById('csvTeacherForm').submit();" />
         @csrf
         <input type="button" value="CSV Upload" style="max-height: 50px; padding: 10px;" onclick="document.getElementById('selectedFile').click();" />
     </form>
     <p style="float:right; width: fit-content; margin-left: auto;">{{ $result }}</p>
</div>

<div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">
    
     <form style="display: flex; flex-direction: row; flex-wrap: wrap; padding: 20px; width: 100%; border: solid 1px black;" action="/admin/teacher">
         <div style="width:50%;">
             <label>
                 Name:
                 <input type="text" class="srchTeacherByName" name="srchTeacherByName" style="border: solid 1px black;" />
             </label>     
         </div>
         <div style="width:50%;">
             <label>
                 Class:
                 <select type="text" class="srchTeacherByClass" name="srchTeacherByClass" style="border: solid 1px black;">
                     <option value ="">Classes</option>
                     @foreach($class_options as $class)
                         <option value="{{$class->classes_no}}">{{$class->classes_name}}</option>
                     @endforeach
                 </select>
             </label>
         </div><br />
         <div style="width: 100%; display: flex; flex-direction: column; justify-content: flex-end;">
             <button style="max-width: 200px; margin: 0 auto; max-height: 50px; padding: 10px;">Search</button>
         </div>
     </form>

     <script>
        $('.srchTeacherByClass').val('{{$selected_class}}');
        $('.srchTeacherByName').val('{{$teacher_to_search}}');
    </script>

</div>
<div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">
    
    <table class="table" style="border: solid 1px black; border-collapse: collapse; margin-top: 40px; width: 100%;">
        <thead>
            <tr>
                <th>Teacher ID</th>
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
                    <td>
                    <?php 
                        if(!empty($included_classes_under_teacher)){
                            $count = count($included_classes_under_teacher['teacher_no']);
                            $class_names = [];
                            for($i = 0; $i < $count; $i++){
                                if($teacher->user_no == $included_classes_under_teacher['teacher_no'][$i]){
                                    $class_names []= $included_classes_under_teacher['class_name'][$i];
                                }
                            }

                            $class_names = array_unique($class_names);
                            $classes_to_show = '';
                            foreach($class_names as $class){
                                $classes_to_show .= $class . ', ';
                            }

                            echo rtrim($classes_to_show, ', ');
                        }else{
                            if($teacher->period != null){
                                echo $teacher->classes_name . ' (' . $teacher->period . ')';
                            }
                        }
                    ?>
                    </td>
                    <td style="display:flex; flex-direction: row; justify-content: space-evenly;"><a href="/admin/teacher/edit?id={{$teacher->user_no}}">Edit</a> | 
                        <form action="{{ route('teacher.destroy', $teacher->user_no) }}" method="POST">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button style="color: white; background-color: red; cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="paginate-nav">{{$teacher_table_results->onEachSide(3)->appends($_GET)->links()}}</div>
    
</div>
    
@endsection
