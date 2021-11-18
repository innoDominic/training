@extends('layouts.layout-with-nav')

@if( $page == 'student')
    @section('content')
        
    @endsection
@elseif( $page == 'teacher' )
    @section('content')
        
    @endsection
@elseif( $page == 'classes' )
    @section('content')
        
    @endsection
@elseif( $page == 'plot_classes' )
    @section('content')
        
    @endsection
@elseif( $page == 'plot_teacher' )
    @section('content')
        
    @endsection
@else
    <h2>Unkown url: {{ $page }}</h2>
@endif