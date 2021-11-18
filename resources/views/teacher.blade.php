@extends('layouts.layout-with-nav')

@if( $page == 'attendance')
    @section('content')
        
    @endsection
@elseif( $page == 'attendance_report' )
    @section('content')
        
    @endsection
@else
    <h2>Unkown url: {{ $page }}</h2>
@endif