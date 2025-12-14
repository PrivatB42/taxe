@extends('templates.layout')

@section('pageTitle', 'Gestion des Rôles')

@section('content')

<style>
    .page-header {
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
        color: white;
        padding: 2rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 40px rgba(124, 58, 237, 0.2);
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .page-header > div {
        position: relative;
        z-index: 1;
    }

    .role-form-card {
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
    }

    .role-form-header {
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
        color: white;
        padding: 1.5rem;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .role-list-card {
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
    }

    .role-list-header {
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
        color: white;
        padding: 1.5rem;
        font-weight: 600;
        font-size: 1.1rem;
    }
</style>

<div class="container-fluid">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0 fw-bold">
                    <i class="fas fa-user-tag me-2"></i>
                    Gestion des Rôles
                </h2>
                <p class="mb-0 mt-2">Créez et gérez les rôles du système</p>
            </div>
            <div>
                <a href="{{ route('permissions.index') }}" class="btn btn-light me-2">
                    <i class="fas fa-shield-alt me-2"></i>
                    Permissions
                </a>
                <a href="{{ route('gestionnaires.index') }}" class="btn btn-light">
                    <i class="fas fa-users me-2"></i>
                    Utilisateurs
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            @include('user::components.role.role-form')
        </div>

        <div class="col-lg-8">
            @include('user::components.role.role-liste')
        </div>
    </div>
</div>

@endsection

