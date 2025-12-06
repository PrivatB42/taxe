@extends('templates.layout')

@section('pageTitle', 'Contribuables')

@section('style')
<style>
    .page-header-contribuable {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 30px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .page-header-contribuable::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 50%;
        height: 200%;
        background: rgba(255,255,255,0.1);
        transform: rotate(30deg);
    }
    .page-header-contribuable h2 {
        font-weight: 700;
        margin-bottom: 8px;
    }
    .page-header-contribuable p {
        opacity: 0.9;
        margin-bottom: 0;
    }
    .form-card-contribuable {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
    }
    .form-card-contribuable .card-header {
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        border-bottom: none;
        padding: 20px 25px;
    }
    .form-card-contribuable .card-header h5 {
        font-weight: 700;
        color: #1b5e20;
        margin: 0;
    }
    .form-card-contribuable .card-body {
        padding: 25px;
    }
    .list-card-contribuable {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
    }
    .contribuable-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #c8e6c9;
        transition: all 0.3s ease;
    }
    .contribuable-avatar:hover {
        border-color: #11998e;
        transform: scale(1.1);
    }
    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }
    .stat-item {
        background: rgba(255,255,255,0.2);
        padding: 15px 20px;
        border-radius: 12px;
        text-align: center;
    }
    .stat-item .number {
        font-size: 2rem;
        font-weight: 700;
    }
    .stat-item .label {
        font-size: 0.85rem;
        opacity: 0.9;
    }
    .matricule-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        font-family: 'Courier New', monospace;
    }
    .action-btn-group {
        display: flex;
        gap: 5px;
    }
    .action-btn-group .btn {
        width: 34px;
        height: 34px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .action-btn-group .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .modern-form-contribuable .form-control {
        border-radius: 10px;
        border: 2px solid #e8f5e9;
        padding: 12px 15px;
        transition: all 0.3s ease;
    }
    .modern-form-contribuable .form-control:focus {
        border-color: #11998e;
        box-shadow: 0 0 0 3px rgba(17, 153, 142, 0.1);
    }
    .modern-form-contribuable .btn-primary {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border: none;
        border-radius: 10px;
        padding: 14px;
        font-weight: 600;
    }
    .modern-form-contribuable .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(17, 153, 142, 0.4);
    }
</style>
@endsection

@section('content')

<!-- En-tête de page -->
<div class="page-header-contribuable">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h2><i class="fas fa-users me-3"></i>Gestion des Contribuables</h2>
            <p>Créez, modifiez et gérez les informations des contribuables</p>
            <div class="stats-cards">
                <div class="stat-item">
                    <div class="number" id="total-contribuables">-</div>
                    <div class="label">Total</div>
                </div>
                <div class="stat-item">
                    <div class="number" id="actifs-contribuables">-</div>
                    <div class="label">Actifs</div>
                </div>
                <div class="stat-item">
                    <div class="number" id="inactifs-contribuables">-</div>
                    <div class="label">Inactifs</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 text-end d-none d-lg-block">
            <i class="fas fa-user-friends" style="font-size: 6rem; opacity: 0.2;"></i>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="form-card-contribuable card">
            <div class="card-header">
                <h5><i class="fas fa-user-plus me-2"></i><span id="card-title">Ajouter un contribuable</span></h5>
            </div>
            <div class="card-body modern-form-contribuable">
                @include('user::components.contribuable.contribuable-form')
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="list-card-contribuable card">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2 text-success"></i>Liste des contribuables</h5>
            </div>
            <div class="card-body p-0">
                @include('user::components.contribuable.contribuable-liste')
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(updateContribuableStats, 1500);
    });

    function updateContribuableStats() {
        const table = x_datatable('table-id');
        if (table) {
            const data = table.getAllData();
            document.getElementById('total-contribuables').textContent = data.length;
            document.getElementById('actifs-contribuables').textContent = data.filter(d => d.is_active).length;
            document.getElementById('inactifs-contribuables').textContent = data.filter(d => !d.is_active).length;
        }
    }

    // Observer le refresh de la table
    const originalRefresh = window.refreshTable;
    window.refreshTable = function() {
        if (originalRefresh) originalRefresh();
        setTimeout(updateContribuableStats, 500);
    };
</script>
@endsection