
@php
// Définition des tailles disponibles
$sizes = [
'sm' => 'modal-sm',
'md' => '',
'lg' => 'modal-lg',
'xl' => 'modal-xl',
'full' => 'modal-fullscreen',
'full-sm' => 'modal-fullscreen-sm-down',
'full-md' => 'modal-fullscreen-md-down',
'full-lg' => 'modal-fullscreen-lg-down',
'full-xl' => 'modal-fullscreen-xl-down'
];

$button = [
'name' => 'Ouvrir le modal',
'class' => 'btn btn-primary',
'icon' => null
];

// Valeurs par défaut
$modalId = $modalId ?? 'defaultModal';
$size = $size ?? 'md';
$isCenter = $isCenter ?? false;
$isScrollable = $isScrollable ?? false;

// Construction des classes
$modalDialogClass = 'modal-dialog ' . ($sizes[$size] ?? '');
$modalDialogClass .= $isCenter ? ' modal-dialog-centered' : '';
$modalDialogClass .= $isScrollable ? ' modal-dialog-scrollable' : '';
@endphp

<!-- Button trigger modal -->
<button type="button" class="{{ $button['class'] }}" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
    {{ $button['icon'] }} {{ $button['name'] }}
</button>

<!-- Modal Structure -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
    <div class="{{ $modalDialogClass }}">
        <div class="modal-content">
            <!-- Header Slot -->
            @isset($header)
            <div class="modal-header">
                {{ $header }}
            </div>
            @else
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ $modalTitle ?? 'Modal title' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @endisset

            <!-- Body Slot (main content) -->
            <div class="modal-body">
                {{ $slot }}
            </div>

            <!-- Footer Slot -->
            @isset($footer)
            <div class="modal-footer">
                {{ $footer }}
            </div>
            @else
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
            @endisset
        </div>
    </div>
</div>