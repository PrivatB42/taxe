@extends('templates.layout')

@section('pageTitle', 'Tableau de bord')

@section('style')
<style>
    .welcome-card {
        background: var(--primary-gradient);
        border-radius: var(--border-radius);
        padding: 40px;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
    }

    .welcome-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 60%;
        height: 200%;
        background: rgba(255, 255, 255, 0.1);
        transform: rotate(20deg);
    }

    .welcome-card::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -10%;
        width: 40%;
        height: 150%;
        background: rgba(255, 255, 255, 0.05);
        transform: rotate(-15deg);
    }

    .welcome-content {
        position: relative;
        z-index: 1;
    }

    .welcome-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .welcome-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .welcome-date {
        margin-top: 16px;
        padding: 12px 20px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        backdrop-filter: blur(10px);
    }

    .stat-card-modern {
        border-radius: var(--border-radius);
        padding: 28px;
        position: relative;
        overflow: hidden;
        transition: var(--transition);
        border: none;
        height: 100%;
    }

    .stat-card-modern:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .stat-card-modern .stat-icon-bg {
        position: absolute;
        right: -15px;
        top: 50%;
        transform: translateY(-50%);
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-card-modern .stat-icon-bg i {
        font-size: 3rem;
        opacity: 0.5;
    }

    .stat-card-modern .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 8px;
    }

    .stat-card-modern .stat-label {
        font-size: 0.95rem;
        opacity: 0.9;
        font-weight: 500;
    }

    .stat-card-modern .stat-trend {
        margin-top: 12px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .bg-gradient-purple {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .bg-gradient-green {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }

    .bg-gradient-orange {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .bg-gradient-blue {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }

    .activity-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }

    .activity-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .activity-header h5 {
        margin: 0;
        font-weight: 700;
        color: #1e293b;
    }

    .activity-list {
        padding: 0;
        margin: 0;
        list-style: none;
        max-height: 400px;
        overflow-y: auto;
    }

    .activity-item {
        display: flex;
        align-items: flex-start;
        padding: 16px 24px;
        border-bottom: 1px solid #f8fafc;
        transition: var(--transition);
    }

    .activity-item:hover {
        background: #f8fafc;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
        flex-shrink: 0;
    }

    .activity-content {
        flex: 1;
    }

    .activity-content strong {
        color: #1e293b;
    }

    .activity-content p {
        margin: 4px 0 0;
        font-size: 0.9rem;
        color: #64748b;
    }

    .activity-time {
        font-size: 0.8rem;
        color: #94a3b8;
        white-space: nowrap;
    }

    .quick-links {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .quick-link-card {
        background: white;
        border-radius: var(--border-radius-sm);
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        text-decoration: none;
        color: #334155;
        transition: var(--transition);
        border: 2px solid transparent;
    }

    .quick-link-card:hover {
        border-color: var(--primary-color);
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.15);
        color: var(--primary-color);
    }

    .quick-link-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }

    .quick-link-text h6 {
        margin: 0 0 4px;
        font-weight: 600;
    }

    .quick-link-text span {
        font-size: 0.85rem;
        color: #64748b;
    }
</style>
@endsection

@section('content')

<!-- Carte de bienvenue -->
<div class="welcome-card animate-fade-in-up">
    <div class="welcome-content">
        <h1 class="welcome-title">
            Bonjour, {{ session('user.nom_complet', Auth::user()->personne->nom_complet ?? 'Utilisateur') }} 👋
        </h1>
        <p class="welcome-subtitle">
            @if($role === \App\Helpers\Constantes::COMPTE_ADMIN)
                Bienvenue sur votre espace Administrateur. Vous avez accès à toutes les fonctionnalités.
            @elseif($role === \App\Helpers\Constantes::COMPTE_SUPERVISEUR)
                Bienvenue sur votre espace Superviseur. Gérez vos gestionnaires et suivez leurs activités.
            @else
                Bienvenue sur votre tableau de bord.
            @endif
        </p>
        <div class="welcome-date">
            <i class="fas fa-calendar-alt"></i>
            <span>{{ now()->translatedFormat('l d F Y') }}</span>
        </div>
    </div>
</div>

<!-- Statistiques -->
@if(isset($stats))
<div class="row mb-4">
    <!-- Gestionnaires -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card-modern bg-gradient-purple">
            <div class="stat-icon-bg">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="stat-number">{{ $stats['gestionnaires']['total'] ?? 0 }}</div>
            <div class="stat-label">Gestionnaires</div>
            <div class="stat-trend">
                <i class="fas fa-check-circle me-1"></i>
                {{ $stats['gestionnaires']['actifs'] ?? 0 }} actifs
            </div>
        </div>
    </div>

    <!-- Activités aujourd'hui -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card-modern bg-gradient-green">
            <div class="stat-icon-bg">
                <i class="fas fa-history"></i>
            </div>
            <div class="stat-number">{{ $stats['activites']['today'] ?? 0 }}</div>
            <div class="stat-label">Actions aujourd'hui</div>
            <div class="stat-trend">
                <i class="fas fa-calendar-week me-1"></i>
                {{ $stats['activites']['this_week'] ?? 0 }} cette semaine
            </div>
        </div>
    </div>

    <!-- Total activités -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card-modern bg-gradient-orange">
            <div class="stat-icon-bg">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-number">{{ $stats['activites']['total'] ?? 0 }}</div>
            <div class="stat-label">Total activités</div>
            <div class="stat-trend">
                <i class="fas fa-database me-1"></i>
                Depuis le début
            </div>
        </div>
    </div>

    <!-- Contribuables (Admin uniquement) -->
    @if($role === \App\Helpers\Constantes::COMPTE_ADMIN && isset($stats['contribuables']))
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card-modern bg-gradient-blue">
            <div class="stat-icon-bg">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-number">{{ $stats['contribuables']['total'] ?? 0 }}</div>
            <div class="stat-label">Contribuables</div>
            <div class="stat-trend">
                <i class="fas fa-user-check me-1"></i>
                {{ $stats['contribuables']['actifs'] ?? 0 }} actifs
            </div>
        </div>
    </div>
    @else
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card-modern bg-gradient-blue">
            <div class="stat-icon-bg">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="stat-number">{{ $stats['gestionnaires']['actifs'] ?? 0 }}</div>
            <div class="stat-label">Gestionnaires actifs</div>
            <div class="stat-trend">
                <i class="fas fa-user-times me-1"></i>
                {{ $stats['gestionnaires']['inactifs'] ?? 0 }} inactifs
            </div>
        </div>
    </div>
    @endif
</div>
@endif

<div class="row">
    <!-- Dernières activités -->
    <div class="col-lg-8 mb-4">
        <div class="activity-card">
            <div class="activity-header">
                <h5><i class="fas fa-history text-primary me-2"></i>Dernières activités</h5>
                <a href="{{ route('activites-log.index') }}" class="btn btn-sm btn-outline-primary">
                    Voir tout <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <ul class="activity-list">
                @forelse($dernieres_activites ?? [] as $activite)
                <li class="activity-item">
                    <div class="activity-icon bg-{{ $activite->action_color }} bg-opacity-10">
                        <i class="fas {{ $activite->action_icon }} text-{{ $activite->action_color }}"></i>
                    </div>
                    <div class="activity-content">
                        <strong>{{ $activite->gestionnaire->nom_complet ?? 'Gestionnaire' }}</strong>
                        <p>{{ $activite->description }}</p>
                    </div>
                    <div class="activity-time">
                        {{ $activite->created_at->diffForHumans() }}
                    </div>
                </li>
                @empty
                <li class="activity-item">
                    <div class="text-center py-4 w-100">
                        <i class="fas fa-inbox text-muted fa-3x mb-3"></i>
                        <p class="text-muted mb-0">Aucune activité récente</p>
                    </div>
                </li>
                @endforelse
            </ul>
        </div>
    </div>

    <!-- Accès rapides -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-bolt text-warning me-2"></i>Accès rapides
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    @if($role === \App\Helpers\Constantes::COMPTE_SUPERVISEUR || $role === \App\Helpers\Constantes::COMPTE_ADMIN)
                    <a href="{{ route('gestionnaires.index') }}" class="quick-link-card">
                        <div class="quick-link-icon bg-primary bg-opacity-10">
                            <i class="fas fa-user-tie text-primary"></i>
                        </div>
                        <div class="quick-link-text">
                            <h6>Gestionnaires</h6>
                            <span>Gérer les comptes</span>
                        </div>
                    </a>

                    <a href="{{ route('activites-log.index') }}" class="quick-link-card">
                        <div class="quick-link-icon bg-success bg-opacity-10">
                            <i class="fas fa-history text-success"></i>
                        </div>
                        <div class="quick-link-text">
                            <h6>Activités</h6>
                            <span>Suivi des actions</span>
                        </div>
                    </a>
                    @endif

                    @if($role === \App\Helpers\Constantes::COMPTE_ADMIN)
                    <a href="{{ route('contribuables.index') }}" class="quick-link-card">
                        <div class="quick-link-icon bg-info bg-opacity-10">
                            <i class="fas fa-users text-info"></i>
                        </div>
                        <div class="quick-link-text">
                            <h6>Contribuables</h6>
                            <span>Liste complète</span>
                        </div>
                    </a>

                    <a href="{{ route('activites.index') }}" class="quick-link-card">
                        <div class="quick-link-icon bg-warning bg-opacity-10">
                            <i class="fas fa-cog text-warning"></i>
                        </div>
                        <div class="quick-link-text">
                            <h6>Configurations</h6>
                            <span>Paramètres système</span>
                        </div>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    // Animation des chiffres au chargement
    document.addEventListener('DOMContentLoaded', function() {
        const counters = document.querySelectorAll('.stat-number');
        counters.forEach(counter => {
            const target = parseInt(counter.innerText);
            if (!isNaN(target)) {
                let count = 0;
                const increment = target / 50;
                const updateCount = () => {
                    if (count < target) {
                        count += increment;
                        counter.innerText = Math.ceil(count);
                        requestAnimationFrame(updateCount);
                    } else {
                        counter.innerText = target;
                    }
                };
                updateCount();
            }
        });
    });
</script>
@endsection
