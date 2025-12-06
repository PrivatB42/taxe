@extends('templates.layout')

@section('pageTitle', 'Suivi des Activités')

@section('style')
<style>
    .activity-timeline {
        position: relative;
        padding-left: 30px;
    }
    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(180deg, var(--primary-color), var(--info-color));
    }
    .activity-item {
        position: relative;
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .activity-item::before {
        content: '';
        position: absolute;
        left: -24px;
        top: 20px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--primary-color);
        border: 2px solid white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        color: white;
        padding: 24px;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
    }
    .stats-card::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: rgba(255,255,255,0.1);
        transform: rotate(30deg);
    }
    .stats-card.green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .stats-card.orange { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .stats-card.blue { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .stats-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .stats-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    .filter-section {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
    }
    .badge-action {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .gestionnaire-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 10px;
    }
    .table-modern thead th {
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
        border: none;
        padding: 15px;
        font-weight: 600;
        color: #495057;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .table-modern tbody td {
        padding: 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
    }
    .table-modern tbody tr:hover {
        background: linear-gradient(135deg, #f8f9ff 0%, #fff 100%);
    }
</style>
@endsection

@section('content')

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1 fw-bold">
                    <i class="fas fa-history text-primary me-2"></i>
                    Suivi des Activités
                </h4>
                <p class="text-muted mb-0">Suivez toutes les actions effectuées par les gestionnaires</p>
            </div>
            <button class="btn btn-primary" onclick="refreshStats()">
                <i class="fas fa-sync-alt me-2"></i>Actualiser
            </button>
        </div>
    </div>
</div>

<!-- Statistiques -->
<div class="row mb-4" id="stats-container">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-number" id="stat-total">-</div>
            <div class="stats-label">Total des actions</div>
            <i class="fas fa-chart-line position-absolute" style="right: 20px; bottom: 20px; font-size: 2rem; opacity: 0.3;"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card green">
            <div class="stats-number" id="stat-today">-</div>
            <div class="stats-label">Actions aujourd'hui</div>
            <i class="fas fa-calendar-day position-absolute" style="right: 20px; bottom: 20px; font-size: 2rem; opacity: 0.3;"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card orange">
            <div class="stats-number" id="stat-week">-</div>
            <div class="stats-label">Cette semaine</div>
            <i class="fas fa-calendar-week position-absolute" style="right: 20px; bottom: 20px; font-size: 2rem; opacity: 0.3;"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card blue">
            <div class="stats-number" id="stat-gestionnaires">{{ $gestionnaires->count() }}</div>
            <div class="stats-label">Gestionnaires actifs</div>
            <i class="fas fa-users position-absolute" style="right: 20px; bottom: 20px; font-size: 2rem; opacity: 0.3;"></i>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="filter-section">
    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label fw-semibold">Gestionnaire</label>
            <select class="form-select" id="filter-gestionnaire">
                <option value="">Tous les gestionnaires</option>
                @foreach($gestionnaires as $gestionnaire)
                    <option value="{{ $gestionnaire->id }}">{{ $gestionnaire->nom_complet }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold">Action</label>
            <select class="form-select" id="filter-action">
                <option value="">Toutes</option>
                <option value="create">Création</option>
                <option value="update">Modification</option>
                <option value="delete">Suppression</option>
                <option value="toggle">Changement statut</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold">Date début</label>
            <input type="date" class="form-control" id="filter-date-debut">
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold">Date fin</label>
            <input type="date" class="form-control" id="filter-date-fin">
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary w-100" onclick="applyFilters()">
                <i class="fas fa-filter me-2"></i>Filtrer
            </button>
        </div>
    </div>
</div>

<!-- Tableau des activités -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-list text-primary me-2"></i>
            Historique des activités
        </h5>
    </div>
    <div class="card-body p-0">
        @include('user::components.activites-log.activites-log-liste')
    </div>
</div>

@endsection

@section('script')
<script>
    // Charger les statistiques au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        refreshStats();
    });

    function refreshStats() {
        fetch("{{ route('activites-log.stats') }}")
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('stat-total').textContent = data.data.total.toLocaleString();
                    document.getElementById('stat-today').textContent = data.data.today.toLocaleString();
                    document.getElementById('stat-week').textContent = data.data.this_week.toLocaleString();
                }
            })
            .catch(error => console.error('Erreur:', error));
    }

    function applyFilters() {
        const table = x_datatable('table-activites');
        if (table) {
            table.refreshTable();
        }
    }
</script>
@endsection


