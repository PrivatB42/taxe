
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

// Valeurs par défaut
$modalId = $modalId ?? 'c-defaultModal';
$size = $size ?? 'md';
$isCenter = $isCenter ?? false;
$isScrollable = $isScrollable ?? false;

// Construction des classes
$modalDialogClass = 'modal-dialog ' . ($sizes[$size] ?? '');
$modalDialogClass .= $isCenter ? ' modal-dialog-centered' : '';
$modalDialogClass .= $isScrollable ? ' modal-dialog-scrollable' : '';
@endphp

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
                <h5 class="modal-title" id="modal-title-{{ $modalId }}">{{ $modalTitle ?? 'Modal title' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @endisset

            <!-- Body Slot (main content) -->
            <div class="modal-body" id="modal-body-{{ $modalId }}">
                {{ $slot }}
            </div>

            <!-- Footer Slot -->
            @isset($footer)
            <div class="modal-footer" id="modal-footer-{{ $modalId }}">
                {{ $footer }}
            </div>
            @else
            <div class="modal-footer" id="modal-footer-{{ $modalId }}">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
            @endisset
        </div>
    </div>
</div>