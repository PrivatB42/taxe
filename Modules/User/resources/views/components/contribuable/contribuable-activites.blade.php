@php
$form = xForm();
@endphp



<div class="row">
    @if(session('user.role') != _constantes()::ROLE_CAISSIER)
    <div class="col-md-4">
        @include('user::components.contribuable-activite.contribuable-activite-form',
        [
        'contribuable' => $contribuable,
        ])
    </div>
    @endif
    <div class="col-md-{{ session('user.role') != _constantes()::ROLE_CAISSIER ? '8' : '12' }}">
        @include('user::components.contribuable-activite.contribuable-activite-liste')
    </div>
</div>