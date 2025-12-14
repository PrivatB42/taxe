@extends('templates.layout')

@section('pageTitle', 'Tableau de bord')

@section('style')
<style>
    .welcome-card {
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
        border-radius: 20px;
        padding: 2.5rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 40px rgba(124, 58, 237, 0.3);
        position: relative;
        overflow: hidden;
    }

    @media (max-width: 768px) {
        .welcome-card {
            padding: 1.5rem;
        }
        .welcome-card h1 {
            font-size: 1.5rem !important;
        }
        .welcome-card p {
            font-size: 0.95rem !important;
        }
    }

    .welcome-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
        background-size: 50px 50px;
        animation: move 20s linear infinite;
    }

    @keyframes move {
        0% { transform: translate(0, 0); }
        100% { transform: translate(50px, 50px); }
    }

    .welcome-content {
        position: relative;
        z-index: 1;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--card-color);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .stat-card.purple { --card-color: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); }
    .stat-card.green { --card-color: linear-gradient(135deg, #10b981 0%, #34d399 100%); }
    .stat-card.pink { --card-color: linear-gradient(135deg, #ec4899 0%, #f472b6 100%); }
    .stat-card.blue { --card-color: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%); }
    .stat-card.orange { --card-color: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); }

    .stat-card.purple::before { background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); }
    .stat-card.green::before { background: linear-gradient(135deg, #10b981 0%, #34d399 100%); }
    .stat-card.pink::before { background: linear-gradient(135deg, #ec4899 0%, #f472b6 100%); }
    .stat-card.blue::before { background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%); }
    .stat-card.orange::before { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
        position: relative;
        z-index: 1;
    }

    .stat-card.purple .stat-icon {
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
        color: white;
    }

    .stat-card.green .stat-icon {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
    }

    .stat-card.pink .stat-icon {
        background: linear-gradient(135deg, #ec4899 0%, #f472b6 100%);
        color: white;
    }

    .stat-card.blue .stat-icon {
        background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
        color: white;
    }

    .stat-card.orange .stat-icon {
        background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        color: white;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1e3c72;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .activity-card, .quick-access-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
        height: 100%;
    }

    .card-header-modern {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f0f0f0;
    }

    .card-title-modern {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e3c72;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-title-modern i {
        color: #667eea;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }

    .quick-access-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: 12px;
        transition: all 0.3s ease;
        cursor: pointer;
        margin-bottom: 0.75rem;
    }

    .quick-access-item:hover {
        background: #f8f9fa;
        transform: translateX(5px);
    }

    .quick-access-item:last-child {
        margin-bottom: 0;
    }

    .quick-access-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
    }

    .quick-access-icon.purple { background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); }
    .quick-access-icon.green { background: linear-gradient(135deg, #10b981 0%, #34d399 100%); }
    .quick-access-icon.blue { background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%); }
    .quick-access-icon.orange { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); }

    .quick-access-text h4 {
        font-size: 1rem;
        font-weight: 600;
        color: #1e3c72;
        margin-bottom: 0.25rem;
    }

    .quick-access-text p {
        font-size: 0.85rem;
        color: #6c757d;
        margin: 0;
    }
</style>
@endsection

@section('content')

<!-- Welcome Card -->
<div class="welcome-card">
    <div class="welcome-content">
        <h1 class="mb-2" style="font-size: 2rem; font-weight: 700;">
            Bonjour, {{ session('user')['nom_complet'] ?? 'Utilisateur' }} 👋
        </h1>
        <p class="mb-0" style="font-size: 1.1rem; opacity: 0.95;">
            Bienvenue sur votre espace Administrateur. Vous avez accès à toutes les fonctionnalités.
        </p>
        <div class="mt-3 d-flex align-items-center gap-2" style="opacity: 0.9;">
            <i class="fas fa-calendar-alt"></i>
            <span>{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</span>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    @php
        $colors = ['purple', 'green', 'pink', 'blue', 'orange'];
        $colorIndex = 0;
    @endphp

    @foreach ($recaps as $recap)
        <div class="col-xl-{{ $recap['size'] }} col-md-6 col-lg-{{ $recap['size'] }}">
            <div class="stat-card {{ $colors[$colorIndex % count($colors)] }}">
                <div class="stat-icon">
                    <i class="{{ $recap['icon'] }}"></i>
                </div>
                <div class="stat-value">{{ number_format($recap['value'], 0, ',', ' ') }} FCFA</div>
                <div class="stat-label">{{ $recap['label'] }}</div>
            </div>
        </div>
        @php $colorIndex++; @endphp
    @endforeach
</div>

<!-- Bottom Section -->
<div class="row g-4">
    <!-- Latest Activities -->
    <div class="col-lg-8">
        <div class="activity-card">
            <div class="card-header-modern">
                <h3 class="card-title-modern">
                    <i class="fas fa-history"></i>
                    Dernières activités
                </h3>
                <a href="#" class="btn btn-sm btn-outline-primary">
                    Voir tout <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <p>Aucune activité récente</p>
            </div>
        </div>
    </div>

    <!-- Quick Access -->
    <div class="col-lg-4">
        <div class="quick-access-card">
            <div class="card-header-modern">
                <h3 class="card-title-modern">
                    <i class="fas fa-bolt"></i>
                    Accès rapides
                </h3>
            </div>
            <div class="quick-access-item" onclick="window.location.href='{{ route('gestionnaires.index') }}'">
                <div class="quick-access-icon purple">
                    <i class="fas fa-user"></i>
                </div>
                <div class="quick-access-text">
                    <h4>Gestionnaires</h4>
                    <p>Gérer les comptes</p>
                </div>
            </div>
            <div class="quick-access-item" onclick="window.location.href='{{ route('activites.index') }}'">
                <div class="quick-access-icon green">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="quick-access-text">
                    <h4>Activités</h4>
                    <p>Suivi des actions</p>
                </div>
            </div>
            <div class="quick-access-item" onclick="window.location.href='{{ route('contribuables.index') }}'">
                <div class="quick-access-icon blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="quick-access-text">
                    <h4>Contribuables</h4>
                    <p>Liste complète</p>
                </div>
            </div>
            <div class="quick-access-item" onclick="window.location.href='#'">
                <div class="quick-access-icon orange">
                    <i class="fas fa-cog"></i>
                </div>
                <div class="quick-access-text">
                    <h4>Configurations</h4>
                    <p>Paramètres système</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
