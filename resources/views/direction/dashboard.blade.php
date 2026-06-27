@extends('layouts.direction')

@section('title', 'Dashboard Direction')
@section('page_title', 'Dashboard Direction')

@section('content')
    <h1 class="text-2xl font-bold text-primary">
        Bonjour {{ auth()->user()->prenomUser }} —
        Dashboard Direction
    </h1>
@endsection