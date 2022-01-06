@extends('layouts.layout-with-nav')

@section('content')

<div style="width: 100%; display:flex; flex-direction: row; justify-content: flex-start; align-items: center;">
     <h2>Classes</h2>
     <button onclick="window.open('/admin/class/create','_self')" style="max-height: 50px; padding: 10px;">Create</button>
</div>
<div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">
    
    <table class="table" style="border: solid 1px black; border-collapse: collapse; margin-top: 40px; width: 100%;">
        <thead>
            <tr>
                <th>Class</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
             @foreach($class_table_results as $classes)
                 <tr>
                    <td>{{$classes->classes_name}}</td>
                    <td style="display:flex; flex-direction: row; justify-content: space-evenly;"><a href="/admin/class/edit?id={{$classes->classes_no}}">Edit</a> | 
                        <form action="{{ route('class.destroy', $classes->classes_no) }}" method="POST">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button style="color: white; background-color: red; cursor:pointer;">Delete</button>
                        </form>
                    </td>
                 </tr>
             @endforeach
        </tbody>
    </table>
    
</div>

@endsection
