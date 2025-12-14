@extends('templates.layout')

@section('pageTitle', 'Gestion des Utilisateurs')

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

    .page-header h2 {
        margin: 0;
        font-weight: 700;
        position: relative;
        z-index: 1;
    }

    .page-header p {
        margin: 0.5rem 0 0 0;
        opacity: 0.95;
        position: relative;
        z-index: 1;
    }

    .page-header > div {
        position: relative;
        z-index: 1;
    }

    .quick-stats {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
    }

    .stat-item {
        background: rgba(255,255,255,0.2);
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        backdrop-filter: blur(10px);
    }

    .stat-item strong {
        display: block;
        font-size: 1.5rem;
    }

    .stat-item small {
        opacity: 0.9;
    }
</style>

<div class="container-fluid">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>
                    <i class="fas fa-users-cog me-2"></i>
                    Gestion des Utilisateurs
                </h2>
                <p>Créez et gérez les utilisateurs de l'application avec leurs rôles et permissions</p>
            </div>
            <div>
                <a href="{{ route('roles.index') }}" class="btn btn-light me-2">
                    <i class="fas fa-user-tag me-2"></i>
                    Rôles
                </a>
                <a href="{{ route('permissions.index') }}" class="btn btn-light">
                    <i class="fas fa-shield-alt me-2"></i>
                    Permissions
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            @include('user::components.gestionnaire.gestionnaire-form')
        </div>

        <div class="col-lg-8">
            @include('user::components.gestionnaire.gestionnaire-liste')
        </div>
    </div>
</div>

@endsection