@php

use Illuminate\Support\HtmlString;

$form = xFormBuilder();

$form->action(route('gestionnaires.store'))
->method('POST')
->form(['id' => 'form-id', 'class' => 'modern-form'])
->csrf(true)

->text('nom_complet',
'Nom complet',
[
'required' => true,
'placeholder' => 'Ex: Jean Dupont',
'class' => 'form-control form-control-lg',
'icon' => 'fas fa-user'
]
)

->number('telephone',
'Téléphone',
[
'required' => true,
'placeholder' => 'Ex: 0700000000',
'class' => 'form-control form-control-lg',
'icon' => 'fas fa-phone'
]
)

->email('email',
'Adresse email',
[
'required' => true,
'placeholder' => 'Ex: jean@exemple.com',
'class' => 'form-control form-control-lg',
'icon' => 'fas fa-envelope'
]
)


@endphp

{!! $form !!}

<button type="submit" form="form-id" id="btn-form-id" class="btn btn-primary btn-lg w-100 mt-3">
    <i class="fas fa-save me-2"></i> Enregistrer
</button>


<button type="button" class="btn btn-outline-secondary w-100 mt-2" onclick="resetForm()">
    <i class="fas fa-undo me-2"></i>Réinitialiser
</button>

<style>
    .modern-form .form-control {
        border-radius: 10px;
        border: 2px solid #e8ecf1;
        padding: 12px 15px;
        transition: all 0.3s ease;
    }
    .modern-form .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    .modern-form .form-label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 8px;
    }
    .modern-form .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 10px;
        padding: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .modern-form .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }
    .modern-form .btn-outline-secondary {
        border-radius: 10px;
        padding: 12px;
        font-weight: 500;
    }
</style>


