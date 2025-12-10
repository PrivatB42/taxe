@extends('templates.layout')

@section('pageTitle', 'Contribuables')

@section('content')

<div class="row">
    <div class="col-lg-4 p-3">
        @include('user::components.contribuable.contribuable-form')
    </div>

    <div class="col-lg-8 pt-3">
        @include('user::components.contribuable.contribuable-liste')
    </div>
</div>

@endsection