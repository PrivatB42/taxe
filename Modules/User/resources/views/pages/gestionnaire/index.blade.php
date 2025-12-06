@extends('templates.layout')

@section('pageTitle', 'Gestionnaires')

@section('style')
<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 30px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 50%;
        height: 200%;
        background: rgba(255,255,255,0.1);
        transform: rotate(30deg);
    }
    .page-header h2 {
        font-weight: 700;
        margin-bottom: 8px;
    }
    .page-header p {
        opacity: 0.9;
        margin-bottom: 0;
    }
    .form-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
    }
    .form-card .card-header {
        background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
        border-bottom: none;
        padding: 20px 25px;
    }
    .form-card .card-header h5 {
        font-weight: 700;
        color: #2d3748;
        margin: 0;
    }
    .form-card .card-body {
        padding: 25px;
    }
    .list-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
    }
    .list-card .card-header {
        background: white;
        border-bottom: 1px solid #f0f0f0;
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .gestionnaire-row {
        transition: all 0.3s ease;
    }
    .gestionnaire-row:hover {
        background: linear-gradient(135deg, #f8f9ff 0%, #fff 100%);
        transform: translateX(5px);
    }
    .avatar-circle {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #e8ecf1;
        transition: all 0.3s ease;
    }
    .avatar-circle:hover {
        border-color: #667eea;
        transform: scale(1.1);
    }
    .status-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-active {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
    }
    .status-inactive {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        color: #721c24;
    }
    .btn-action {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        border: none;
        margin: 0 2px;
    }
    .btn-action:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .stats-mini {
        display: flex;
        gap: 20px;
        margin-top: 15px;
    }
    .stats-mini-item {
        background: rgba(255,255,255,0.2);
        padding: 10px 20px;
        border-radius: 10px;
    }
    .stats-mini-item .number {
        font-size: 1.5rem;
        font-weight: 700;
    }
    .stats-mini-item .label {
        font-size: 0.8rem;
        opacity: 0.9;
    }
</style>
@endsection

@section('content')

<!-- En-tête de page -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h2><i class="fas fa-user-tie me-3"></i>Gestion des Gestionnaires</h2>
            <p>Gérez les comptes des gestionnaires et suivez leurs activités</p>
            <div class="stats-mini">
                <div class="stats-mini-item">
                    <div class="number" id="total-gestionnaires">-</div>
                    <div class="label">Gestionnaires</div>
                </div>
                <div class="stats-mini-item">
                    <div class="number" id="actifs-gestionnaires">-</div>
                    <div class="label">Actifs</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 text-end d-none d-lg-block">
            <i class="fas fa-users-cog" style="font-size: 6rem; opacity: 0.2;"></i>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="form-card card">
            <div class="card-header">
                <h5><i class="fas fa-user-plus me-2 text-primary"></i><span id="card-title">Ajouter un gestionnaire</span></h5>
            </div>
            <div class="card-body">
                @include('user::components.gestionnaire.gestionnaire-form-modern')
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="list-card card">
            <div class="card-header">
                <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2 text-primary"></i>Liste des gestionnaires</h5>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control form-control-sm" placeholder="Rechercher..." style="width: 200px;" id="search-gestionnaire">
                </div>
            </div>
            <div class="card-body p-0">
                @include('user::components.gestionnaire.gestionnaire-liste')
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    // Mettre à jour les stats
    document.addEventListener('DOMContentLoaded', function() {
        updateStats();
    });

    function updateStats() {
        // Ces valeurs seront mises à jour par le datatable
        const table = x_datatable('table-id');
        if (table) {
            setTimeout(() => {
                const data = table.getAllData();
                document.getElementById('total-gestionnaires').textContent = data.length;
                document.getElementById('actifs-gestionnaires').textContent = data.filter(d => d.is_active).length;
            }, 1000);
        }
    }
</script>
@endsection