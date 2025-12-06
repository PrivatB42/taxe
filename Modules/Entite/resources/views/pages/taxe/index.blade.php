@extends('templates.layout')

@section('pageTitle', 'Taxes')

@section('content')

<div class="row">
    <div class="col-lg-4 p-3">
        @include('entite::components.taxe.taxe-form')
    </div>

    <div class="col-lg-8 pt-3">
        @include('entite::components.taxe.taxe-liste')
    </div>
</div>

@endsection