@php

$form = xFormBuilder();

$form->action(route('roles.store'))
->method('POST')
->form(['id' => 'form-id'])
->csrf(true)

->text('code',
'Code du rôle',
[
'required' => true,
'placeholder' => 'Ex: gestionnaire_comptable',
'help' => 'Code unique en minuscules (sans espaces)',
]
)

->text('nom',
'Nom du rôle',
[
'required' => true,
'placeholder' => 'Ex: Gestionnaire Comptable',
]
)

->textarea('description',
'Description',
[
'placeholder' => 'Description du rôle',
'rows' => 3,
]
)

->button('Valider', ['id' => 'btn-form-id', 'class' => 'btn-primary btn-lg w-100'])
->render();
@endphp

<style>
    .role-form-card {
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
    }

    .role-form-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        font-weight: 600;
        font-size: 1.1rem;
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

    .form-control {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        outline: none;
    }
</style>

<div class="card role-form-card">
    <div class="role-form-header">
        <i class="fas fa-plus-circle"></i>
        <span id="card-title">Ajouter un Rôle</span>
        <a href="#" class="btn btn-sm btn-light float-end" onClick="resetForm()">
            <i class="fas fa-redo me-1"></i>Réinitialiser
        </a>
    </div>
    <div class="card-body p-4">
        {!! $form !!}
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const codeInput = document.querySelector('#form-id input[name="code"]');
        if (codeInput) {
            codeInput.addEventListener('input', function() {
                // Convertir en minuscules et remplacer les espaces par des underscores
                this.value = this.value.toLowerCase().replace(/\s+/g, '_');
            });
        }
    });
</script>

