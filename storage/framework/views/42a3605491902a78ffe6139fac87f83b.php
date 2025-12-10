<?php $__env->startSection('pageTitle', 'Suivi des Activités'); ?>

<?php $__env->startSection('style'); ?>
<style>
    .page-header-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: var(--border-radius);
        padding: 32px;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
    }

    .page-header-modern::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 50%;
        height: 200%;
        background: rgba(255, 255, 255, 0.1);
        transform: rotate(15deg);
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .stat-card-mini {
        background: white;
        border-radius: var(--border-radius-sm);
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: var(--card-shadow);
        transition: var(--transition);
    }

    .stat-card-mini:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
    }

    .stat-icon-mini {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }

    .stat-content h3 {
        font-size: 1.8rem;
        font-weight: 800;
        margin: 0;
        line-height: 1;
        color: #1e293b;
    }

    .stat-content span {
        font-size: 0.9rem;
        color: #64748b;
        font-weight: 500;
    }

    .filter-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: var(--card-shadow);
    }

    .filter-card .form-label {
        font-weight: 600;
        color: #334155;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table-card {
        background: white;
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--card-shadow);
    }

    .table-card .card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 20px 24px;
        border-bottom: 1px solid #e2e8f0;
    }

    .gestionnaire-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        object-fit: cover;
    }

    .badge-action {
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .table-modern thead th {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: none;
        padding: 16px 20px;
        font-weight: 600;
        color: #475569;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .table-modern tbody td {
        padding: 16px 20px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    .table-modern tbody tr:hover {
        background: linear-gradient(135deg, #f8fafc 0%, #fff 100%);
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<!-- En-tête de page -->
<div class="page-header-modern">
    <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 1;">
        <div>
            <h4 class="mb-2 fw-bold">
                <i class="fas fa-history me-2"></i>
                Suivi des Activités
            </h4>
            <p class="mb-0 opacity-90">Suivez toutes les actions effectuées par les gestionnaires en temps réel</p>
        </div>
        <button class="btn btn-light" onclick="refreshData()">
            <i class="fas fa-sync-alt me-2"></i>Actualiser
        </button>
    </div>
</div>

<!-- Statistiques -->
<div class="stats-row">
    <div class="stat-card-mini">
        <div class="stat-icon-mini bg-primary bg-opacity-10">
            <i class="fas fa-chart-line text-primary"></i>
        </div>
        <div class="stat-content">
            <h3 id="stat-total">-</h3>
            <span>Total des actions</span>
        </div>
    </div>

    <div class="stat-card-mini">
        <div class="stat-icon-mini bg-success bg-opacity-10">
            <i class="fas fa-plus-circle text-success"></i>
        </div>
        <div class="stat-content">
            <h3 id="stat-creations">-</h3>
            <span>Créations</span>
        </div>
    </div>

    <div class="stat-card-mini">
        <div class="stat-icon-mini bg-info bg-opacity-10">
            <i class="fas fa-edit text-info"></i>
        </div>
        <div class="stat-content">
            <h3 id="stat-modifications">-</h3>
            <span>Modifications</span>
        </div>
    </div>

    <div class="stat-card-mini">
        <div class="stat-icon-mini bg-danger bg-opacity-10">
            <i class="fas fa-trash text-danger"></i>
        </div>
        <div class="stat-content">
            <h3 id="stat-suppressions">-</h3>
            <span>Suppressions</span>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="filter-card">
    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Gestionnaire</label>
            <select class="form-select" id="filter-gestionnaire">
                <option value="">Tous les gestionnaires</option>
                <?php $__currentLoopData = $gestionnaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gestionnaire): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($gestionnaire->id); ?>"><?php echo e($gestionnaire->nom_complet); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Action</label>
            <select class="form-select" id="filter-action">
                <option value="">Toutes</option>
                <option value="create">Création</option>
                <option value="update">Modification</option>
                <option value="delete">Suppression</option>
                <option value="toggle">Changement statut</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Date début</label>
            <input type="date" class="form-control" id="filter-date-debut">
        </div>
        <div class="col-md-2">
            <label class="form-label">Date fin</label>
            <input type="date" class="form-control" id="filter-date-fin">
        </div>
        <div class="col-md-3">
            <div class="d-flex gap-2">
                <button class="btn btn-primary flex-fill" onclick="applyFilters()">
                    <i class="fas fa-filter me-2"></i>Filtrer
                </button>
                <button class="btn btn-outline-secondary" onclick="resetFilters()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Tableau des activités -->
<div class="table-card">
    <div class="card-header">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-list text-primary me-2"></i>
            Historique des activités
        </h5>
    </div>
    <div class="card-body p-0">
        <?php echo $__env->make('user::components.activites-log.activites-log-liste', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        refreshStats();
    });

    function refreshStats() {
        fetch("<?php echo e(route('activites-log.stats')); ?>")
            .then(response => response.json())
            .then(data => {
                if (data.success && data.stats) {
                    document.getElementById('stat-total').textContent = (data.stats.total || 0).toLocaleString();
                    document.getElementById('stat-creations').textContent = (data.stats.creations || 0).toLocaleString();
                    document.getElementById('stat-modifications').textContent = (data.stats.modifications || 0).toLocaleString();
                    document.getElementById('stat-suppressions').textContent = (data.stats.suppressions || 0).toLocaleString();
                }
            })
            .catch(error => console.error('Erreur:', error));
    }

    function refreshData() {
        refreshStats();
        applyFilters();
    }

    function applyFilters() {
        const table = x_datatable('table-activites');
        if (table) {
            table.refreshTable();
        }
    }

    function resetFilters() {
        document.getElementById('filter-gestionnaire').value = '';
        document.getElementById('filter-action').value = '';
        document.getElementById('filter-date-debut').value = '';
        document.getElementById('filter-date-fin').value = '';
        applyFilters();
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('templates.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\Downloads\taxe\Modules/User\resources/views/pages/activites-log/index.blade.php ENDPATH**/ ?>