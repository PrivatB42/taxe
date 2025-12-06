@extends('templates.layout')

@section('pageTitle', 'Constantes')

@section('content')

<div class="row">
    <div class="col-lg-4 p-3">
        @include('entite::components.taxe-constante.taxe-constante-form')
    </div>

    <div class="col-lg-8 pt-3">
        @include('entite::components.taxe-constante.taxe-constante-liste')
    </div>
</div>

@endsection