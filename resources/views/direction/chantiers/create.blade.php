@extends('layouts.direction')

@section('title', 'Gestion des chantiers')
@section('page_title', 'Gestion des chantiers')

@section('content')
    <h1 class="text-2xl font-bold text-primary">
        Bonjour {{ auth()->user()->prenomUser }} —
        Gestion des chantiers
    </h1>
@endsection