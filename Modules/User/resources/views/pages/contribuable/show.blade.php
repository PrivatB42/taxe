@extends('user::templates.contribuable-template')

@section('pageTitle', 'Contribuable - '. $action.($contribuableActivite ? ' - '.$contribuableActivite->activite?->nom : ''))

@section('contribuable-content')

        @include('user::components.contribuable.'.$component)

@endsection