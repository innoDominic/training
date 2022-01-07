@extends('layouts.layout-with-nav')

@section('content')

<div style="width: 100%; display:flex; flex-direction: row; justify-content: flex-start; align-items: center;">
     <h2>Students</h2>
     <button onclick="window.open('/admin/student/create','_self')" style="max-height: 50px; padding: 10px;">Create</button>
     
     <form id="csvStudentForm" method="POST" action="/admin/student/csv" enctype="multipart/form-data">
         <input type="file" id="selectedFile" name="csvFile" style="display: none;" onChange="document.getElementById('csvStudentForm').submit();" />
         @csrf
         <input type="button" value="CSV Upload" style="max-height: 50px; padding: 10px;" onclick="document.getElementById('selectedFile').click();" />
     </form>
     <p style="float:right; width: fit-content; margin-left: auto;">{{ $result }}</p>
</div>

<div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">
    
     <form style="display: flex; flex-direction: row; flex-wrap: wrap; padding: 20px; width: 100%; border: solid 1px black;" action="/admin/student">
         <div style="width:33%;">
             <label>
                 Name:
                 <input type="text" class="srchStudentByName" name="srchStudentByName" style="border: solid 1px black;" />
                 @csrf
             </label>     
         </div>
         <div style="width:33%;">
             <label>
                 Class:
                 <select type="text" class="srchStudentByClass" name="srchStudentByClass" style="border: solid 1px black;">
                     <option value ="">Classes</option>
                     @foreach($class_options as $class)
                         <option value="{{$class->classes_no}}">{{$class->classes_name}}</option>
                     @endforeach
                 </select>
                 @csrf
             </label>
         </div>
         <div style="width:33%;">
             <label>
                 Teacher:
                 <select type="text" class="srchStudentByTeacher" name="srchStudentByTeacher" style="border: solid 1px black;">
                     <option value ="">Teacher</option>
                     @foreach($teacher_options as $teacher)
                         <option value="{{$teacher->user_no}}">{{$teacher->teacher_title}} {{$teacher->first_name}} {{$teacher->last_name}}</option>
                     @endforeach
                 </select>
                 @csrf
             </label>
         </div><br />
         <div style="width: 100%; display: flex; flex-direction: column; justify-content: flex-end; padding-top:20px;">
             <button style="max-width: 200px; margin: 0 auto; max-height: 50px; padding: 10px;">Search</button>
         </div>
     </form>

</div>
<div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">

    <script>
        $('.srchStudentByName').val('{{$student_to_search}}');
        $('.srchStudentByClass').val('{{$selected_class}}');
        $('.srchStudentByTeacher').val('{{$selected_teacher}}');
    </script>
    
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
            @php
                #dd($student_table_results, $periods);
                $classes_no = [];
                $classes_name = [];
            @endphp
            @foreach($student_table_results as $students)
                <tr>
                    <td>{{$students->student_id}}</td>
                    <td>{{$students->first_name}} {{$students->last_name}}</td>

                    <td>
                        @if(empty($selected_class) && empty($selected_teacher))

                            @foreach($periods as $p)
                                @if($p->user_no == $students->user_no)
                                    @foreach($class_options as $c)
                                        @if($p->classes_no == $c->classes_no)
                                            @php
                                                $classes_name [] = $c->classes_name;
                                                $classes_no [] = $c->classes_no
                                            @endphp
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach

                        @elseif(empty($selected_class) && !empty($selected_teacher))
                            
                            @php
                                echo $students->classes_name;
                                $classes_no [] = $students->classes_no
                            @endphp

                        @else
                            {{$students->classes_name}}
                        @endif

                        <?php
  
                            $classes_name = array_values(array_unique($classes_name));
                            $classes_count = count($classes_name);
                            for($i = 0; $i <= $classes_count - 1; $i++){
                                if($i == $classes_count - 1){
                                    echo $classes_name[$i];
                                }else{
                                    echo $classes_name[$i] . ', ';
                                }
                            }

                        ?>
                    </td>

                    <td class="td-teacher-name">
                        @if(empty($selected_class) && empty($selected_teacher))
                            @foreach($periods as $period)

                                @php #echo in_array($period->classes_no, $classes_no); @endphp

                                @if(in_array($period->classes_no, $classes_no) && $period->user_no == $students->user_no)

                                    @foreach($teacher_options as $t_options)
                                        @if($period->teacher_user_no == $t_options->user_no)
                                            {{$t_options->last_name}} {{$t_options->first_name}},
                                        @endif
                                    @endforeach

                                @endif

                            @endforeach

                        @else
                            

                            @foreach($teacher_options as $t_options)
                                @if($students->teacher_user_no == $t_options->user_no)
                                    {{$t_options->last_name}} {{$t_options->first_name}}
                                @endif
                            @endforeach


                        @endif
                    </td>

                    <td style="display:flex; flex-direction: row; justify-content: space-evenly;"><a href="{{action('StudentController@edit', $students->user_no)}}">Edit</a> | <form action="{{ route('student.destroy', $students->user_no) }}" method="POST">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <button style="color: white; background-color: red; cursor:pointer;">Delete</button>
                    </form></td>
                </tr>
             @endforeach
        </tbody>
    </table>
    <div class="paginate-nav">{{$student_table_results->onEachSide(3)->appends($_GET)->links()}}</div>
    
</div>

@endsection
