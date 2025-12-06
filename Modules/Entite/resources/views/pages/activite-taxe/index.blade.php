@extends('templates.layout')

@section('pageTitle', 'Activités et taxes')

@section('content')

<div class="row">
    <div class="col-lg-4 p-3">
        @include('entite::components.activite-taxe.activite-taxe-form')
    </div>

    <div class="col-lg-8 pt-3">
        @include('entite::components.activite-taxe.activite-taxe-liste')
    </div>
</div>

@endsection