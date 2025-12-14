@php

$form = xFormBuilder();

$form->action(route('gestionnaires.store'))
->method('POST')
->form(['id' => 'form-id'])
->csrf(true)

->text('nom_complet',
'Nom complet',
[
'required' => true,
'placeholder' => 'Entrez le nom complet',
]
)

->number('telephone',
'Téléphone',
[
'required' => true,
'placeholder' => 'Entrez le numéro de téléphone',
]
)

->email('email',
'Email',
[
'required' => true,
'placeholder' => 'Entrez l\'email',
]
)

->select('role',
'Rôle',
$form->mapOptions($roles ?? [], 'id', 'nom', 'Sélectionnez un rôle'),
['required' => true],
)

->button('Valider', ['id' => 'btn-form-id', 'class' => 'btn-primary btn-lg w-100'])
->render();
@endphp

<style>
    .user-form-card {
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
    }

    .user-form-header {
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
        color: white;
        padding: 1.5rem;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .user-form-header i {
        margin-right: 0.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .role-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 0.5rem;
    }

    .role-badge.admin {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .role-badge.regisseur {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .role-badge.agent {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }

    .role-badge.caissier {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        color: white;
    }

    .role-badge.superviseur {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: white;
    }
</style>

<div class="card user-form-card">
    <div class="user-form-header">
        <i class="fas fa-user-plus"></i>
        <span id="card-title">Ajouter un Utilisateur</span>
        <a href="#" class="btn btn-sm btn-light float-end" onClick="resetForm()">
            <i class="fas fa-redo me-1"></i>Réinitialiser
        </a>
    </div>
    <div class="card-body p-4">
        {!! $form !!}
        <div id="role-description" class="mt-3"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.querySelector('#form-id select[name="role"]');
        if (roleSelect) {
            roleSelect.addEventListener('change', function() {
                updateRoleDescription(this.value);
            });
            // Initialiser la description si un rôle est déjà sélectionné
            if (roleSelect.value) {
                updateRoleDescription(roleSelect.value);
            }
        }
    });

    function updateRoleDescription(role) {
        const descriptions = {
            'admin': 'Administrateur avec toutes les permissions du système',
            'regisseur': 'Régisseur avec tous les droits des agents de la Régie, gestion des utilisateurs, caisses, tableau de bord et reportings',
            'agent_de_la_regie': 'Agent de la Régie : création et gestion des taxes, contribuables, activités taxables, caisses et caissiers',
            'caissier': 'Caissier : ouverture/fermeture de caisse, encaissement et impression de reçus',
            'superviseur': 'Superviseur : accès au tableau de bord et aux reportings'
        };

        const container = document.getElementById('role-description');
        if (role && descriptions[role]) {
            const roleNames = {
                'admin': 'Admin',
                'regisseur': 'Régisseur',
                'agent_de_la_regie': 'Agent de la Régie',
                'caissier': 'Caissier',
                'superviseur': 'Superviseur'
            };

            container.innerHTML = `
                <div class="alert alert-info mb-0">
                    <strong>${roleNames[role]}:</strong> ${descriptions[role]}
                </div>
            `;
        } else {
            container.innerHTML = '';
        }
    }
</script>