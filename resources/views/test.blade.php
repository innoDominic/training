@extends('layouts.layouts')

@section('content')
    <style>
     .paginate-nav div{ display: block !important; }
    </style>

    @foreach($test as $val)
        <li> {{$val}} </li>
    @endforeach

    <div class="paginate-nav"> {{$test->onEachSide(3)->links()}} </div>
@endsection
