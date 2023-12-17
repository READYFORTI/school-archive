@extends('layout.app')
@section('title')
<title>Document Archiving and Tracking</title>
@endsection
@section('content')
    @if ($status)
        <h1>{{$message}}</h1>
        <a href="{{route('login-page')}}">Go to login page</a>
    @else
        <h1>{{$message}}</h1>
        <a href="{{route('login-page')}}">Go to login page</a>
    @endif
@endsection