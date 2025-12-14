@extends('templates.layout')

@section('pageTitle', 'Gestion des Activités')

@section('style')
<style>
    .page-header-modern {
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
        color: white;
        padding: 2rem 2.5rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 40px rgba(124, 58, 237, 0.2);
        position: relative;
        overflow: hidden;
    }

    .page-header-modern::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .page-header-modern h2 {
        margin: 0;
        font-weight: 700;
        font-size: 1.75rem;
        position: relative;
        z-index: 1;
    }

    .page-header-modern p {
        margin: 0.5rem 0 0 0;
        opacity: 0.95;
        font-size: 1rem;
        position: relative;
        z-index: 1;
    }

    .page-header-modern .header-icon {
        font-size: 3rem;
        opacity: 0.15;
        position: absolute;
        right: 2rem;
        top: 50%;
        transform: translateY(-50%);
    }
</style>
@endsection

@section('content')

<div class="container-fluid">
    <div class="page-header-modern">
        <div class="header-icon">
            <i class="fas fa-briefcase"></i>
        </div>
        <h2>
            <i class="fas fa-briefcase me-2"></i>
            Gestion des Activités
        </h2>
        <p>Configurez les activités taxables de la commune</p>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            @include('entite::components.activite.activite-form')
        </div>

        <div class="col-lg-8">
            @include('entite::components.activite.activite-liste')
        </div>
    </div>
</div>

@endsection