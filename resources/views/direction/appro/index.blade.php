@extends('layouts.direction')

@section('title', 'Approvisionnement')
@section('page_title', 'Approvisionnement')

@section('content')
    <h1 class="text-2xl font-bold text-primary">
        Bonjour {{ auth()->user()->prenomUser }} —
        Approvisionnement
    </h1>
@endsection