@extends('templates.layout')

@section('pageTitle', 'Gestion des Permissions')

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

    .page-header-modern > div {
        position: relative;
        z-index: 1;
    }

    .page-header-modern .btn-light {
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        backdrop-filter: blur(10px);
    }

    .page-header-modern .btn-light:hover {
        background: rgba(255,255,255,0.3);
        border-color: rgba(255,255,255,0.5);
    }
</style>
@endsection

@section('content')

<div class="container-fluid">
    <div class="page-header-modern">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>
                    <i class="fas fa-shield-alt me-2"></i>
                    Gestion des Permissions
                </h2>
                <p>Configurez les permissions pour chaque rôle utilisateur</p>
            </div>
            <div>
                <a href="{{ route('roles.index') }}" class="btn btn-light me-2">
                    <i class="fas fa-user-tag me-2"></i>
                    Rôles
                </a>
                <a href="{{ route('gestionnaires.index') }}" class="btn btn-light">
                    <i class="fas fa-users me-2"></i>
                    Utilisateurs
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .role-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }

    .role-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    }

    .role-header {
        padding: 1.5rem;
        color: white;
        font-weight: 600;
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .role-header.admin {
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
    }

    .role-header.regisseur {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .role-header.agent-de-la-regie {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .role-header.caissier {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    .role-header.superviseur {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    .permission-item {
        padding: 0.75rem 1rem;
        margin: 0.5rem 0;
        background: #f8f9fa;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s ease;
    }

    .permission-item:hover {
        background: #e9ecef;
        transform: translateX(4px);
    }

    .permission-item label {
        margin: 0;
        cursor: pointer;
        flex: 1;
        font-size: 0.95rem;
    }

    .permission-item input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .permissions-container {
        max-height: 500px;
        overflow-y: auto;
        padding: 1rem;
    }

    .permissions-container::-webkit-scrollbar {
        width: 6px;
    }

    .permissions-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .permissions-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .permissions-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .save-btn {
        margin-top: 1rem;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .save-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .role-icon {
        font-size: 1.5rem;
        margin-right: 0.5rem;
    }

    .permission-count {
        background: rgba(255,255,255,0.2);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        margin-left: auto;
    }

    .admin-badge {
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
        margin-left: 1rem;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-none">
                <div>
                    <a href="{{ route('roles.index') }}" class="btn btn-light me-2">
                        <i class="fas fa-user-tag me-2"></i>
                        Gérer les Rôles
                    </a>
                    <button class="btn btn-primary" onclick="initializePermissions()">
                        <i class="fas fa-sync-alt me-2"></i>
                        Initialiser les Permissions
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="roles-container">
        @foreach($roles as $role)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="role-card">
                    <div class="role-header {{ str_replace('_', '-', $role['id']) }}">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <i class="fas fa-user-shield role-icon"></i>
                                {{ $role['nom'] }}
                                @if($role['id'] === 'admin')
                                    <span class="admin-badge">Toutes les permissions</span>
                                @endif
                            </div>
                            <span class="permission-count" id="count-{{ $role['id'] }}">0</span>
                        </div>
                    </div>
                    <div class="permissions-container" id="permissions-{{ $role['id'] }}">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </div>
                    </div>
                    @if($role['id'] !== 'admin')
                        <div class="p-3 bg-light">
                            <button 
                                class="btn btn-primary w-100 save-btn" 
                                onclick="savePermissions('{{ $role['id'] }}')"
                                id="save-btn-{{ $role['id'] }}">
                                <i class="fas fa-save me-2"></i>
                                Enregistrer les Permissions
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    const roles = @json($roles);
    const permissionsByRole = {};

    document.addEventListener('DOMContentLoaded', function() {
        // Charger les permissions pour chaque rôle
        roles.forEach(role => {
            loadRolePermissions(role.id);
        });
    });

    function loadRolePermissions(role) {
        const url = `{{ route('permissions.role', ':role') }}`.replace(':role', role);
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                permissionsByRole[role] = data;
                renderPermissions(role, data);
                updatePermissionCount(role, data);
            })
            .catch(error => {
                console.error('Erreur lors du chargement des permissions:', error);
                document.getElementById(`permissions-${role}`).innerHTML = 
                    '<div class="text-center text-danger py-4">Erreur lors du chargement</div>';
            });
    }

    function renderPermissions(role, permissions) {
        const container = document.getElementById(`permissions-${role}`);
        
        if (role === 'admin') {
            container.innerHTML = `
                <div class="p-4 text-center">
                    <i class="fas fa-crown text-warning" style="font-size: 3rem;"></i>
                    <p class="mt-3 text-muted">L'administrateur a accès à toutes les permissions du système.</p>
                </div>
            `;
            return;
        }

        let html = '';
        permissions.forEach(permission => {
            html += `
                <div class="permission-item">
                    <label for="perm-${role}-${permission.id}">
                        <strong>${permission.nom}</strong>
                        ${permission.description ? `<br><small class="text-muted">${permission.description}</small>` : ''}
                    </label>
                    <input 
                        type="checkbox" 
                        id="perm-${role}-${permission.id}"
                        ${permission.has_permission ? 'checked' : ''}
                        onchange="updatePermissionCount('${role}')"
                    >
                </div>
            `;
        });

        container.innerHTML = html;
    }

    function updatePermissionCount(role, permissions = null) {
        if (!permissions) {
            permissions = permissionsByRole[role] || [];
        }
        
        if (permissions.length === 0) return;
        
        const checkedCount = permissions.filter(p => {
            const checkbox = document.getElementById(`perm-${role}-${p.id}`);
            return checkbox ? checkbox.checked : p.has_permission;
        }).length;

        const countElement = document.getElementById(`count-${role}`);
        if (countElement) {
            countElement.textContent = `${checkedCount} / ${permissions.length}`;
        }
    }

    function savePermissions(role) {
        const checkboxes = document.querySelectorAll(`#permissions-${role} input[type="checkbox"]`);
        const permissionIds = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => {
                const id = cb.id.replace(`perm-${role}-`, '');
                return parseInt(id);
            });

        const url = `{{ route('permissions.role.update', ':role') }}`.replace(':role', role);
        const saveBtn = document.getElementById(`save-btn-${role}`);
        const originalText = saveBtn.innerHTML;
        
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enregistrement...';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ permissions: permissionIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                x_successNotification(data.message || 'Permissions enregistrées avec succès');
                loadRolePermissions(role); // Recharger pour synchroniser
            } else {
                x_errorAlert(data.message || 'Erreur lors de l\'enregistrement');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            x_errorAlert('Une erreur est survenue lors de l\'enregistrement');
        })
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        });
    }

    function initializePermissions() {
        if (!confirm('Voulez-vous vraiment initialiser toutes les permissions ? Cette action va réinitialiser les permissions par défaut.')) {
            return;
        }

        const url = '{{ route("permissions.initialize") }}';
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                x_successNotification(data.message || 'Permissions initialisées avec succès');
                // Recharger toutes les permissions
                roles.forEach(role => {
                    loadRolePermissions(role.id);
                });
            } else {
                x_errorAlert(data.message || 'Erreur lors de l\'initialisation');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            x_errorAlert('Une erreur est survenue lors de l\'initialisation');
        });
    }
</script>

@endsection

