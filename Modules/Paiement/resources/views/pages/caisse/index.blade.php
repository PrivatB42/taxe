@extends('templates.layout')

@section('pageTitle', 'Caisse')

@section('content')


        @include('paiement::components.caisse.caisse-liste')
    

@endsection