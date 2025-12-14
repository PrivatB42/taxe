@php
$form = xForm();
@endphp

<div class="row">
    <div class="col-md-4">
        @include('user::components.contribuable-parametre.contribuable-parametre-form',
        [
        'contribuable' => $contribuable,
        ])
    </div>
    <div class="col-md-8">
        @include('user::components.contribuable-parametre.contribuable-parametre-liste')
    </div>
</div>