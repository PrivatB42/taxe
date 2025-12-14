@extends('templates.layout')

@section('content')

<x-generic.card>
    <x-slot name="header">
        <i class="fas fa-user"></i>
        <span id="card-title">{{ ucfirst($contribuable->personne?->nom_complet) }}</span> |
        <span>{{ $contribuable->matricule }}</span>

        <div class="float-end">
            <div class="row">
                @if(session('user.role') != _constantes()::ROLE_CAISSIER)
                <div class="col-lg-6">
                    @include('user::components.contribuable.contribuable-menu')
                </div>
                @endif
                <div class="col-lg-{{ session('user.role') != _constantes()::ROLE_CAISSIER ? '6' : '12' }}">
                     <a href="{{ route('contribuables.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Retour </a>
                </div>
            </div>
        </div>
    </x-slot>


    @yield('contribuable-content')
    

</x-generic.card>

@endsection