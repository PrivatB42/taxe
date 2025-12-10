@php
$form = xForm();
@endphp



<div class="row">
    <div class="col-md-4">
        @include('user::components.contribuable-activite.contribuable-activite-form',
        [
        'contribuable' => $contribuable,
        ])
    </div>
    <div class="col-md-8">
        @include('user::components.contribuable-activite.contribuable-activite-liste')
    </div>
</div>