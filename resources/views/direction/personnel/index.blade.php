@extends('layouts.direction')

@section('title', 'Gestion du personnel')
@section('page_title', 'Gestion du personnel')

@section('content')
    <h1 class="text-2xl font-bold text-primary">
        Bonjour {{ auth()->user()->prenomUser }} —
        Gestion du personnel
    </h1>
@endsection