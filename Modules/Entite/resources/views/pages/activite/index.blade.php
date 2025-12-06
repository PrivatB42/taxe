@extends('templates.layout')

@section('pageTitle', 'Activités')

@section('content')

<div class="row">
    <div class="col-lg-4 p-3">
        @include('entite::components.activite.activite-form')
    </div>

    <div class="col-lg-8 pt-3">
        @include('entite::components.activite.activite-liste')
    </div>
</div>

@endsection