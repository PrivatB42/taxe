@extends('templates.layout')

@section('pageTitle', 'Gestionnaires')

@section('content')

<div class="row">
    <div class="col-lg-4 p-3">
        @include('user::components.gestionnaire.gestionnaire-form')
    </div>

    <div class="col-lg-8 pt-3">
        @include('user::components.gestionnaire.gestionnaire-liste')
    </div>
</div>

@endsection