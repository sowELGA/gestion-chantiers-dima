@extends('layouts.direction')

@section('title', 'Gestion des postes')
@section('page_title', 'Gestion des postes')

@section('content')
    <h1 class="text-2xl font-bold text-primary">
        Bonjour {{ auth()->user()->prenomUser }} —
        Gestion des postes
    </h1>
@endsection