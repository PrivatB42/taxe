
@php

$name = $name ?? 'Ouvrir le modal';
$class = $class ?? 'btn btn-primary';
$icon = $icon ?? null;
$modalId = $modalId ?? 'c-defaultModal';
$attributes = $attributes ?? null;
@endphp

<!-- Button trigger modal -->
<button type="button" id="button-{{ $modalId }}" class="{{ $class }}" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}" {{ $attributes }}>
    @if($icon) <i class="{{ $icon }}"></i> @endif {{ $name }}
</button>
