@php
$config = array_merge([
    'tableId' => 'datatable',
    'tableClass' => 'display table table-striped table-bordered',
    'useBootstrap' => true,
    'serverSide' => true,
    'processing' => true,
    'responsive' => true,
    'ordering' => true,
    'paging' => true,
    'info' => true,
    'autoWidth' => false,
    'language' => 'fr',
    'buttons' => ['copy', 'csv', 'excel', 'pdf', 'print'],
    'lengthChange'=> true,
    'pageLength' => 5,
    'lengthMenu' => [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "Tout"]],
    'searching' => true,
    'searchDelay' => 500,
    'stateSave' => false,
    'ajaxUrl' => '',
    'columns' => [],
    'filters' => [],
    'btnFiltrer' => '#btn-filtrer',
    'btnFiltrerQuery' => '.btn-filtrer-query',
    'btnResetFiltre' => '#btn-reset-filtre',
    'tableAttributes' => [],
    'order' => [],
    'scrollCollapse' => false,
    'scrollY' => null,
    'scrollX' => false,
    'rowEvent' => null,
    'individualColumnSearch' => false,
    'fixedHeader' => false,
    'keyTable' => false,
    'fixedColumns' => false,
    'colReorder' => false,
    'rowReorder' => false,
    'select' => false,
    'checkboxs' => false,
    'customJs' => null,
    'drawCallback' => null,
    'initComplete' => null,
    'rowCallback' => null,
    'createdRow' => null,
    'headerCallback' => null,
    'footerCallback' => null,
    'preDrawCallback' => null,
    'stateLoadCallback' => null,
    'stateSaveCallback' => null,
    'infoCallback' => null,

    // Configuration du layout personnalisé - si vide, utilise le layout par défaut
    'customLayout' => [],
    'widgets' => [],
    
    'buttonClass' => 'btn btn-sm btn-primary',
    'buttonContainer' => 'div.custom-buttons',
    'buttonAlignment' => 'left mt-4',
    'buttonPosition' => 'bottom', // top, bottom, both
], $config ?? []);

$columns = $columns ?? [];
$processedColumns = [];

foreach ($columns as $column) {
    $processedColumns[] = [
        'data' => $column['data'] ?? $column['name'] ?? '',
        'name' => $column['name'] ?? $column['data'] ?? '',
        'title' => $column['title'] ?? $column['label'] ?? ucfirst($column['name'] ?? ''),
        'orderable' => $column['orderable'] ?? true,
        'searchable' => $column['searchable'] ?? true,
        'visible' => $column['visible'] ?? true,
        'className' => $column['className'] ?? $column['class'] ?? '',
        'width' => $column['width'] ?? null,
        'type' => $column['type'] ?? 'string',
        'render' => $column['render'] ?? null,
        'attributes' => $column['attributes'] ?? [],
        'searchType' => $column['searchType'] ?? 'text',
        'searchOptions' => $column['searchOptions'] ?? [],
        'searchCheck' => $column['searchCheck'] ?? [],
        'format' => $column['format'] ?? null,
        'defaultContent' => $column['defaultContent'] ?? '',
        'createdCell' => $column['createdCell'] ?? null,
    ];
}

if(!function_exists('booleanToString')) {
function booleanToString($value = null) {
    return match ($value) {
        true => 'true',
        false => 'false',
        default => 'true',
    };
}
}

$languages = [
    'fr' => asset('assets/js/datatablefr.json'),
    'en' => '//cdn.datatables.net/plug-ins/1.13.6/i18n/en-GB.json',
    'es' => '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
    'de' => '//cdn.datatables.net/plug-ins/1.13.6/i18n/de-DE.json',
    'it' => '//cdn.datatables.net/plug-ins/1.13.6/i18n/it-IT.json',
    'pt' => '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json',
    'ar' => '//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json',
];

$tableId = $config['tableId'];
$hasButtons = !empty($config['buttons']);
$useCustomLayout = !empty($config['customLayout']);

// Détermination du DOM layout
if ($useCustomLayout) {
    //$domLayout = '<"top"lfB>rt<"bottom"ip>'; // Retire tous les éléments pour contrôle manuel
     $domLayout = 'Blfrtip';
} elseif ($hasButtons) {
    if ($config['buttonPosition'] === 'bottom') {
        $domLayout = 'lfrtipB';
    } elseif ($config['buttonPosition'] === 'both') {
        $domLayout = 'Blfrtip<"bottom-buttons"B>';
    } else {
        $domLayout = 'Blfrtip';
    }
} else {
    $domLayout = 'lfrtip'; // Layout par défaut
}
@endphp

{{-- CSS Dependencies --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@if($hasButtons)
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
@endif
@if($config['responsive'])
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endif
@if($config['fixedHeader'])
<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.bootstrap5.min.css">
@endif
@if($config['select'])
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.bootstrap5.min.css">
@endif
@if($config['colReorder'])
<link rel="stylesheet" href="https://cdn.datatables.net/colreorder/1.7.0/css/colReorder.bootstrap5.min.css">
@endif
@if($config['fixedColumns'])
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.bootstrap5.min.css">
@endif
@if($config['rowReorder'])
<link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.4.1/css/rowReorder.bootstrap5.min.css">
@endif
@if($config['keyTable'])
<link rel="stylesheet" href="https://cdn.datatables.net/keytable/2.9.0/css/keyTable.bootstrap5.min.css">
@endif

{{-- Custom CSS --}}
<style>
.datatable-wrapper {
    position: relative;
}

.datatable-top-controls,
.datatable-bottom-controls {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin: 15px 0;
}

.datatable-top-left,
.datatable-top-center, 
.datatable-top-right,
.datatable-bottom-left,
.datatable-bottom-center,
.datatable-bottom-right {
    display: flex;
    align-items: center;
    gap: 10px;
}

.datatable-top-controls {
    justify-content: space-between;
}

.datatable-bottom-controls {
    justify-content: space-between;
}

.datatable-top-center,
.datatable-bottom-center {
    flex: 1;
    justify-content: center;
}

/* Cache les éléments DataTables par défaut si on utilise le layout personnalisé */
.custom-layout .dataTables_wrapper .dataTables_length,
.custom-layout .dataTables_wrapper .dataTables_filter,
.custom-layout .dataTables_wrapper .dataTables_info,
.custom-layout .dataTables_wrapper .dataTables_paginate,
.custom-layout .dataTables_wrapper .dt-buttons {
    display: none;
}

.individual-search {
    width: 100%;
    max-width: 150px;
}

.fullscreen {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    z-index: 9999 !important;
    background: white;
    padding: 20px;
}

/* .custom-buttons .dt-button {
    margin: 0 2px;
} */

.custom-search-input {
    max-width: 300px;
}

.custom-length-select {
    max-width: 120px;
}

.processing-indicator {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1000;
    background: rgba(255, 255, 255, 0.9);
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}


.dataTables_paginate .pagination {
  margin-top: var(--bs-spacer, 1rem);
}

@media (max-width: 768px) {
    .datatable-top-controls,
    .datatable-bottom-controls {
        flex-direction: column;
    }
    
    .datatable-top-left,
    .datatable-top-center, 
    .datatable-top-right,
    .datatable-bottom-left,
    .datatable-bottom-center,
    .datatable-bottom-right {
        width: 100%;
        justify-content: center;
    }
}
</style>

{{-- Layout personnalisé --}}
<div class="datatable-wrapper {{ $useCustomLayout ? 'custom-layout' : '' }}">
    
    @if($useCustomLayout)
    {{-- Contrôles du haut --}}
    <div class="datatable-top-controls">
        <div class="datatable-top-left">
            @if(in_array('buttons', $config['customLayout']['topLeft'] ?? []))
                <div id="custom-buttons-container"></div>
            @endif
            @if(in_array('length', $config['customLayout']['topLeft'] ?? []))
                <div id="custom-length-container"></div>
            @endif
            @if(in_array('search', $config['customLayout']['topLeft'] ?? []))
                <div id="custom-search-container"></div>
            @endif
            @if(in_array('info', $config['customLayout']['topLeft'] ?? []))
                <div id="custom-info-container"></div>
            @endif
            @if(in_array('pagination', $config['customLayout']['topLeft'] ?? []))
                <div id="custom-pagination-container"></div>
            @endif
            @if(in_array('widget', $config['customLayout']['topLeft'] ?? []))
                {!! $config['widgets']['topLeft'] ?? '' !!}
            @endif
        </div>
        
        <div class="datatable-top-center">
            @if(in_array('buttons', $config['customLayout']['topCenter'] ?? []))
                <div id="custom-buttons-container"></div>
            @endif
            @if(in_array('length', $config['customLayout']['topCenter'] ?? []))
                <div id="custom-length-container"></div>
            @endif
            @if(in_array('search', $config['customLayout']['topCenter'] ?? []))
                <div id="custom-search-container"></div>
            @endif
            @if(in_array('info', $config['customLayout']['topCenter'] ?? []))
                <div id="custom-info-container"></div>
            @endif
            @if(in_array('pagination', $config['customLayout']['topCenter'] ?? []))
                <div id="custom-pagination-container"></div>
            @endif
            @if(in_array('widget', $config['customLayout']['topCenter'] ?? []))
                {!! $config['widgets']['topCenter'] ?? '' !!}
            @endif
        </div>
        
        <div class="datatable-top-right">
            @if(in_array('buttons', $config['customLayout']['topRight'] ?? []))
                <div id="custom-buttons-container"></div>
            @endif
            @if(in_array('length', $config['customLayout']['topRight'] ?? []))
                <div id="custom-length-container"></div>
            @endif
            @if(in_array('search', $config['customLayout']['topRight'] ?? []))
                <div id="custom-search-container"></div>
            @endif
            @if(in_array('info', $config['customLayout']['topRight'] ?? []))
                <div id="custom-info-container"></div>
            @endif
            @if(in_array('pagination', $config['customLayout']['topRight'] ?? []))
                <div id="custom-pagination-container"></div>
            @endif
            @if(in_array('widget', $config['customLayout']['topRight'] ?? []))
                {!! $config['widgets']['topRight'] ?? '' !!}
            @endif
        </div>
    </div>
    @endif

    <!-- @if(!$useCustomLayout && $hasButtons && ($config['buttonPosition'] ?? 'top') === 'top')
        <div class="mb-3">
            <div id="buttons-container-{{ $tableId }}"></div>
        </div>
    @endif -->

    {{-- Conteneur de processing personnalisé --}}
    <div class="position-relative">
        <div id="custom-processing-container" class="processing-indicator" style="display: none;">
            <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                <span>Chargement en cours...</span>
            </div>
        </div>

        {{-- Table --}}
        <table id="{{ $tableId }}" class="{{ $config['tableClass'] }}" {{ collect($config['tableAttributes'])->map(fn($v, $k) => "$k=\"$v\"")->implode(' ') }}>
            <thead>
                <tr>
                    @if($config['checkboxs'])
                    <th>
                        <input type="checkbox" id="select-all" class="form-check-input">
                    </th>
                    @endif
                    @foreach ($processedColumns as $column)
                    <th {!! isset($column['attributes']) ? collect($column['attributes'])->map(fn($v, $k) => "$k=\"$v\"")->implode(' ') : '' !!}>
                        {{ $column['title'] }}
                    </th>
                    @endforeach
                </tr>
            </thead>
            @if($config['individualColumnSearch'])
            <thead>
                <tr>
                    @if($config['checkboxs'])
                    <td></td>
                    @endif
                    @foreach ($processedColumns as $column)
                    <td>
                        @if($column['searchable'])
                            @if($column['searchType'] === 'select')
                            <select class="form-control form-select form-select-sm individual-search" 
                                    data-column="{{ $config['checkboxs'] ? $loop->index + 1 : $loop->index }}" 
                                    id="individual-search-{{ $column['data'] }}">
                                <option value="">Tous</option>
                                @foreach($column['searchOptions'] as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @elseif($column['searchType'] === 'checkbox')
                            <div class="{{ $column['searchCheck']['class'] ?? 'd-flex justify-content-center gap-2' }}">
                                <input type="checkbox" 
                                       data-column="{{ $config['checkboxs'] ? $loop->index + 1 : $loop->index }}" 
                                       value="{{ $column['searchCheck']['value'] ?? true }}" 
                                       class="form-check individual-search" 
                                       id="individual-search-{{ $column['data'] }}">
                                <span>{{ $column['searchCheck']['label'] ?? '' }}</span>
                            </div>
                            @elseif(in_array($column['searchType'], ['date', 'datetime-local', 'text', 'number', 'color', 'month', 'time', 'week']))
                            <input type="{{ $column['searchType'] }}" 
                                   class="form-control form-control-sm individual-search" 
                                   id="individual-search-{{ $column['data'] }}"
                                   placeholder="Rechercher {{ strtolower($column['title']) }}" 
                                   data-column="{{ $config['checkboxs'] ? $loop->index + 1 : $loop->index }}">
                            @endif
                        @endif
                    </td>
                    @endforeach
                </tr>
            </thead>
            @endif
        </table>
    </div>

    @if($useCustomLayout)
    {{-- Contrôles du bas --}}
    <div class="datatable-bottom-controls">
        <div class="datatable-bottom-left">
            @if(in_array('buttons', $config['customLayout']['bottomLeft'] ?? []))
                <div id="custom-buttons-container"></div>
            @endif
            @if(in_array('length', $config['customLayout']['bottomLeft'] ?? []))
                <div id="custom-length-container"></div>
            @endif
            @if(in_array('search', $config['customLayout']['bottomLeft'] ?? []))
                <div id="custom-search-container"></div>
            @endif
            @if(in_array('info', $config['customLayout']['bottomLeft'] ?? []))
                <div id="custom-info-container"></div>
            @endif
            @if(in_array('pagination', $config['customLayout']['bottomLeft'] ?? []))
                <div id="custom-pagination-container"></div>
            @endif
            @if(in_array('widget', $config['customLayout']['bottomLeft'] ?? []))
                {!! $config['widgets']['bottomLeft'] ?? '' !!}
            @endif
        </div>
        
        <div class="datatable-bottom-center">
            @if(in_array('buttons', $config['customLayout']['bottomCenter'] ?? []))
                <div id="custom-buttons-container"></div>
            @endif
            @if(in_array('length', $config['customLayout']['bottomCenter'] ?? []))
                <div id="custom-length-container"></div>
            @endif
            @if(in_array('search', $config['customLayout']['bottomCenter'] ?? []))
                <div id="custom-search-container"></div>
            @endif
            @if(in_array('info', $config['customLayout']['bottomCenter'] ?? []))
                <div id="custom-info-container"></div>
            @endif
            @if(in_array('pagination', $config['customLayout']['bottomCenter'] ?? []))
                <div id="custom-pagination-container"></div>
            @endif
            @if(in_array('widget', $config['customLayout']['bottomCenter'] ?? []))
                {!! $config['widgets']['bottomCenter'] ?? '' !!}
            @endif
        </div>
        
        <div class="datatable-bottom-right">
            @if(in_array('buttons', $config['customLayout']['bottomRight'] ?? []))
                <div id="custom-buttons-container"></div>
            @endif
            @if(in_array('length', $config['customLayout']['bottomRight'] ?? []))
                <div id="custom-length-container"></div>
            @endif
            @if(in_array('search', $config['customLayout']['bottomRight'] ?? []))
                <div id="custom-search-container"></div>
            @endif
            @if(in_array('info', $config['customLayout']['bottomRight'] ?? []))
                <div id="custom-info-container"></div>
            @endif
            @if(in_array('pagination', $config['customLayout']['bottomRight'] ?? []))
                <div id="custom-pagination-container"></div>
            @endif
            @if(in_array('widget', $config['customLayout']['bottomRight'] ?? []))
                {!! $config['widgets']['bottomRight'] ?? '' !!}
            @endif
        </div>
    </div>
    @endif

    @if(!$useCustomLayout && $hasButtons && ($config['buttonPosition'] ?? 'top') === 'bottom')
    <div class="mt-3">
        <div id="buttons-container-bottom-{{ $tableId }}"></div>
    </div>
    @endif
</div>

{{-- Modals --}}
<div class="modal fade" id="row-details-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails de la ligne</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="row-details-content"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

{{-- Script Dependencies --}}
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

@if($hasButtons)
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
@endif

@if($config['responsive'])
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
@endif

@if($config['fixedHeader'])
<script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>
@endif

@if($config['select'])
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
@endif

@if($config['colReorder'])
<script src="https://cdn.datatables.net/colreorder/1.7.0/js/dataTables.colReorder.min.js"></script>
@endif

@if($config['fixedColumns'])
<script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>
@endif

@if($config['rowReorder'])
<script src="https://cdn.datatables.net/rowreorder/1.4.1/js/dataTables.rowReorder.min.js"></script>
@endif

@if($config['keyTable'])
<script src="https://cdn.datatables.net/keytable/2.9.0/js/dataTables.keyTable.min.js"></script>
@endif

<script>
$(document).ready(function() {
    'use strict';
    var filterQuery = null;
    
    // Configuration centralisée
    const tableConfig = {
        processing: {{ booleanToString($config['processing']) }},
        serverSide: {{ booleanToString($config['serverSide']) }},
        responsive: {{ booleanToString($config['responsive']) }},
        ordering: {{ booleanToString($config['ordering']) }},
        info: {{ booleanToString($config['info']) }},
        searching: {{ booleanToString($config['searching']) }},
        paging: {{ booleanToString($config['paging']) }},
        autoWidth: {{ booleanToString($config['autoWidth']) }},
        stateSave: {{ booleanToString($config['stateSave']) }},
        searchDelay: {{ $config['searchDelay'] }},
        pageLength: {{ $config['pageLength'] }},
        lengthMenu: {!! json_encode($config['lengthMenu']) !!},
        order: {!! json_encode($config['order']) !!},
        scrollCollapse: {{ booleanToString($config['scrollCollapse']) }},
        scrollY: {!! json_encode($config['scrollY']) !!},
        scrollX: {{ booleanToString($config['scrollX']) }},
        lengthChange: {{ booleanToString($config['lengthChange']) }},
        
        @if($config['language'] && isset($languages[$config['language']]))
        language: {
            url: '{{ $languages[$config['language']] }}'
        },
        @endif
        
        // Configuration du DOM
        dom: '{{ $domLayout }}',
        
        @if($config['serverSide'] && !empty($config['ajaxUrl']))
        ajax: {
            url: @json($config['ajaxUrl']) ,
            type: 'POST',
            data: function(d) {
                d.filters = [];
                @if(!empty($config['filters']))
                @foreach($config['filters'] as $filter)
                d.filters.push({
                    name: '{{ $filter['name'] }}',
                    value: $('#{{ $filter['id'] }}').val()
                });
                @endforeach
                @endif

            if (filterQuery) {
                d.filters.push(filterQuery); 
            }
             
                
                d._token = $('meta[name="csrf-token"]').attr('content');
                
                if (!{{ booleanToString($config['paging']) }}) {
                    d.length = {{ $config['pageLength'] }};
                }
            },
            error: function(xhr, error, thrown) {
                console.error('DataTable Ajax Error:', error);
                showNotification('Erreur lors du chargement des données', 'error');
            }
        },
        @endif
        
        columns: [
            @if($config['checkboxs'])
            {
                data: null,
                name: 'checkbox',
                title: '<input type="checkbox" id="select-all" class="form-check-input">',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(data, type, row, meta) {
                    return '<input type="checkbox" class="form-check-input row-checkbox" value="' + row.id + '">';
                }
            },
            @endif
            @foreach ($processedColumns as $index => $column)
            {
                data: '{{ $column['data'] }}',
                name: '{{ $column['name'] }}',
                title: '{{ $column['title'] }}',
                orderable: {{ booleanToString($column['orderable']) }},
                searchable: {{ booleanToString($column['searchable']) }},
                visible: {{ booleanToString($column['visible']) }},
                className: '{{ $column['className'] }}',
                @if($column['width'])
                width: '{{ $column['width'] }}',
                @endif
                type: '{{ $column['type'] }}',
                @if($column['render'])
                render: {!! $column['render'] !!},
                @endif
                @if($column['createdCell'])
                createdCell: {!! $column['createdCell'] !!},
                @endif
                @if($column['defaultContent'])
                defaultContent: '{{ $column['defaultContent'] }}',
                @endif
            }{{ !$loop->last ? ',' : '' }}
            @endforeach
        ],
        
        @if($hasButtons)
        buttons: {
            dom: {
                button: {
                    className: '{{ $config['buttonClass'] ?? 'btn btn-sm btn-outline-primary' }}'
                }
                // container: {
                //     className: '{{ $config['buttonAlignment'] ?? 'text-start' }} custom-buttons'
                // }
            },
            buttons: [
                @foreach($config['buttons'] as $button)
                @if($button === 'copy')
                {
                    extend: 'copy',
                    text: '<i class="fas fa-copy"></i> Copier',
                    className: 'btn btn-sm btn-outline-info'
                },
                @elseif($button === 'csv')
                {
                    extend: 'csv',
                    text: '<i class="fas fa-file-csv"></i> CSV',
                    className: 'btn btn-sm btn-outline-success'
                },
                @elseif($button === 'excel')
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-sm btn-outline-success'
                },
                @elseif($button === 'pdf')
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-sm btn-outline-danger',
                    orientation: 'landscape',
                    pageSize: 'A4'
                },
                @elseif($button === 'print')
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Imprimer',
                    className: 'btn btn-sm btn-outline-secondary'
                },
                @endif
                @endforeach
            ]
        },
        @endif
        
        @if($config['select'])
        select: {
            style: 'multi',
            selector: 'td:first-child'
        },
        @endif
        
        // Callbacks
        @if($config['drawCallback'])
        drawCallback: function(settings) {
            {!! $config['drawCallback'] !!}
        },
        @endif
        
        @if($config['initComplete'])
        initComplete: function(settings, json) {
            {!! $config['initComplete'] !!}
            
            // Repositionnement des éléments en mode custom
            @if($useCustomLayout)
            setTimeout(function() {
                repositionElements();
            }, 100);
            @endif
        },
        @endif
        
        @if($config['rowCallback'])
        rowCallback: function(row, data, index) {
            {!! $config['rowCallback'] !!}
        },
        @endif
        
        @if($config['createdRow'])
        createdRow: function(row, data, dataIndex) {
            {!! $config['createdRow'] !!}
        },
        @endif
        
        @if($config['headerCallback'])
        headerCallback: function(thead, data, start, end, display) {
            {!! $config['headerCallback'] !!}
        },
        @endif
        
        @if($config['footerCallback'])
        footerCallback: function(tfoot, data, start, end, display) {
            {!! $config['footerCallback'] !!}
        },
        @endif
        
        @if($config['preDrawCallback'])
        preDrawCallback: function(settings) {
            {!! $config['preDrawCallback'] !!}
        },
        @endif
        
        @if($config['stateLoadCallback'])
        stateLoadCallback: function(settings) {
            {!! $config['stateLoadCallback'] !!}
        },
        @endif
        
        @if($config['stateSaveCallback'])
        stateSaveCallback: function(settings, data) {
            {!! $config['stateSaveCallback'] !!}
        },
        @endif
        
        @if($config['infoCallback'])
        infoCallback: function(settings, data) {
            {!! $config['infoCallback'] !!}
        },
        @endif
    }; 
    
    
    // Initialisation du DataTable
    if (!$('#{{ $tableId }}').length) {
        console.error('Table element not found:', '{{ $tableId }}');
        return;
    }
    
    const table = $('#{{ $tableId }}').DataTable(tableConfig);
    
    @if($useCustomLayout)
    // Fonction pour repositionner les éléments DataTable
    function repositionElements() {
        const wrapper = $('#{{ $tableId }}_wrapper');
        
        // Fonction utilitaire pour trouver le bon conteneur
        function findTargetContainer(element) {
            const positions = ['topLeft', 'topCenter', 'topRight', 'bottomLeft', 'bottomCenter', 'bottomRight'];
            for (let position of positions) {
                if (Array.isArray(window.customLayout[position]) && window.customLayout[position].includes(element)) {
                    return `.datatable-${position.replace(/([A-Z])/g, '-$1').toLowerCase()} #custom-${element}-container`;
                }
            }
            return `#custom-${element}-container`;
        }
        
        // Stocker la configuration pour usage dynamique
        window.customLayout = {!! json_encode($config['customLayout']) !!};
        
        // Déplacer les boutons
        @if($hasButtons)
        const buttons = wrapper.find('.dt-buttons').first();
        if (buttons.length) {
            const targetContainer = findTargetContainer('buttons');
            const container = $(targetContainer);
            if (container.length) {
                buttons.appendTo(container);
            }
        }
        @endif
        
        // Déplacer la recherche
        const searchBox = wrapper.find('.dataTables_filter');
        if (searchBox.length) {
            const targetContainer = findTargetContainer('search');
            const container = $(targetContainer);
            if (container.length) {
                const searchInput = searchBox.find('input');
                const customSearch = $(`
                    <div class="input-group custom-search-input">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="search" class="form-control" placeholder="Rechercher..." id="custom-search-input" value="${searchInput.val()}">
                    </div>
                `);
                
                // Synchroniser avec l'input original
                customSearch.find('input').on('keyup', function() {
                    searchInput.val($(this).val()).trigger('keyup');
                });
                
                searchInput.on('keyup', function() {
                    customSearch.find('input').val($(this).val());
                });
                
                container.html(customSearch);
            }
        }
        
        // Déplacer le sélecteur de longueur
        const lengthSelect = wrapper.find('.dataTables_length');
        if (lengthSelect.length) {
            const targetContainer = findTargetContainer('length');
            const container = $(targetContainer);
            if (container.length) {
                const originalSelect = lengthSelect.find('select');
                const customLength = $(`
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0">Afficher:</label>
                        <select class="form-select custom-length-select" id="custom-length-select">
                            ${originalSelect.html()}
                        </select>
                        <label class="form-label mb-0">entrées</label>
                    </div>
                `);
                
                // Synchroniser la valeur actuelle
                customLength.find('select').val(originalSelect.val());
                
                // Synchroniser avec le select original
                customLength.find('select').on('change', function() {
                    originalSelect.val($(this).val()).trigger('change');
                });
                
                originalSelect.on('change', function() {
                    customLength.find('select').val($(this).val());
                });
                
                container.html(customLength);
            }
        }
        
        // Déplacer les informations
        const info = wrapper.find('.dataTables_info');
        if (info.length) {
            const targetContainer = findTargetContainer('info');
            const container = $(targetContainer);
            if (container.length) {
                const customInfo = $('<div class="datatable-info"></div>');
                
                // Fonction pour mettre à jour les infos
                function updateCustomInfo() {
                    customInfo.html(info.html());
                }
                
                // Mettre à jour initialement
                updateCustomInfo();
                
                // Écouter les changements
                table.on('draw', updateCustomInfo);
                
                container.html(customInfo);
            }
        }
        
        // Déplacer la pagination
        const pagination = wrapper.find('.dataTables_paginate');
        if (pagination.length) {
            const targetContainer = findTargetContainer('pagination');
            const container = $(targetContainer);
            if (container.length) {
                pagination.appendTo(container);
            }
        }
        
        // Masquer les éléments originaux
        wrapper.find('.dataTables_length, .dataTables_filter').hide();
    }
    
    // Appeler immédiatement si la table est déjà initialisée
    table.on('init.dt', function() {
        setTimeout(function() {
            repositionElements();
        }, 50);
    });
    @else
    // Gestion normale des boutons pour les autres modes
    @if($hasButtons)
    @if(($config['buttonPosition'] ?? 'top') === 'bottom')
    table.buttons().container().appendTo('#buttons-container-bottom-{{ $tableId }}');
    @elseif(($config['buttonPosition'] ?? 'top') === 'both')
    table.buttons().container().clone().appendTo('#buttons-container-bottom-{{ $tableId }}');
    table.buttons().container().appendTo('#buttons-container-{{ $tableId }}');
    @else
    table.buttons().container().appendTo('#buttons-container-{{ $tableId }}');
    @endif
    @endif
    @endif
    
    // Extensions
    @if($config['fixedHeader'])
    new $.fn.dataTable.FixedHeader(table);
    @endif
    
    @if($config['keyTable'])
    new $.fn.dataTable.KeyTable(table);
    @endif
    
    @if($config['fixedColumns'])
    new $.fn.dataTable.FixedColumns(table, {
        leftColumns: {{ is_array($config['fixedColumns']) ? ($config['fixedColumns']['left'] ?? 1) : 1 }},
        rightColumns: {{ is_array($config['fixedColumns']) ? ($config['fixedColumns']['right'] ?? 0) : 0 }}
    });
    @endif
    
    @if($config['colReorder'])
    new $.fn.dataTable.ColReorder(table);
    @endif
    
    @if($config['rowReorder'])
    new $.fn.dataTable.RowReorder(table);
    @endif
    
    // Event Handlers
    @if($config['rowEvent'])
    table.on('click', 'tbody tr', function() {
        const data = table.row(this).data();
        {!! $config['rowEvent'] !!}
    });
    @endif
    
    // Recherche individuelle par colonne
    @if($config['individualColumnSearch'])
    $('.individual-search').on('keyup change clear', function() {
        const column = $(this).data('column');
        const value = $(this).val();
        
        if (table.column(column).search() !== value) {
            table.column(column).search(value).draw();
        }
    });
    @endif
    
    // Gestion des filtres
    @if(!empty($config['filters']) && $config['btnFiltrer'])
    $('{{ $config['btnFiltrer'] }}').on('click', function() {
        table.ajax.reload();
    });
    @endif

    @if(!empty($config['btnFiltrerQuery']) && $config['btnFiltrerQuery'])
    $('{{ $config['btnFiltrerQuery'] }}').on('click', function() {

        var filterName = $(this).data('filtername');
        var filterValue = $(this).data('filtervalue');

        // Ajouter le filtre au tableau
            filterQuery = {
                    name: filterName,
                    value: filterValue
                    }; 
        table.ajax.reload();
    });
    @endif

    @if(!empty($config['btnResetFiltre']) && $config['btnResetFiltre'])
    $('{{ $config["btnResetFiltre"] }}').on('click', function() {
        filterQuery = null;
        table.ajax.reload();
    });
    @endif
    
    // Gestion des checkboxs
    @if($config['checkboxs'])
    $(document).on('change', '#select-all', function() {
        const isChecked = $(this).is(':checked');
        $('.row-checkbox').prop('checked', isChecked);
        $(document).trigger('checkboxsChanged', [getSelectedRows()]);
    });
    
    $(document).on('change', '.row-checkbox', function() {
        const totalCheckboxs = $('.row-checkbox').length;
        const checkedCheckboxs = $('.row-checkbox:checked').length;
        
        $('#select-all').prop('checked', checkedCheckboxs === totalCheckboxs);
        $(document).trigger('checkboxsChanged', [getSelectedRows()]);
    });
    @endif
    
    // Fonctions utilitaires globales
    window.DataTableUtils = window.DataTableUtils || {};
    window.DataTableUtils['{{ $tableId }}'] = {
        table: table,
        
        // Gestion des données
        refreshTable: function() {
            table.ajax.reload(null, false);
        },
        
        resetTable: function() {
            table.state.clear();
            table.ajax.reload();
        },
        
        getAllData: function() {
            return table.rows().data().toArray();
        },

        getRowData: function(rowSelector) {
            return table.row(rowSelector).data();
        },
        
        addRow: function(rowData) {
            table.row.add(rowData).draw();
        },
        
        removeRow: function(rowSelector) {
            table.row(rowSelector).remove().draw();
        },
        
        updateRow: function(rowSelector, rowData) {
            table.row(rowSelector).data(rowData).draw();
        },
        
        // Gestion des filtres et recherches
        globalSearch: function(value) {
            table.search(value).draw();
        },
        
        filterByColumn: function(columnIndex, value) {
            table.column(columnIndex).search(value).draw();
        },
        
        clearAllFilters: function() {
            table.search('').columns().search('').draw();
            $('.individual-search').val('');
        },
        
        // Gestion des colonnes
        toggleColumn: function(columnIndex, visible = null) {
            if (visible === null) {
                visible = !table.column(columnIndex).visible();
            }
            table.column(columnIndex).visible(visible);
        },
        
        getVisibleColumns: function() {
            return table.columns().visible().toArray();
        },
        
        // Gestion de la pagination
        goToPage: function(pageNumber) {
            table.page(pageNumber).draw();
        },
        
        changePageLength: function(length) {
            table.page.len(length).draw();
        },
        
        // Tri
        sortByColumn: function(columnIndex, direction = 'asc') {
            table.order([columnIndex, direction]).draw();
        },
        
        // Export
        exportData: function(format = 'csv') {
            const button = table.button(`${format}:name`);
            if (button.length) {
                button.trigger();
            }
        },
        
        // Contrôle du layout personnalisé
        @if($useCustomLayout)
        moveElementTo: function(element, targetContainer) {
            const wrapper = $('#{{ $tableId }}_wrapper');
            let sourceElement;
            
            switch(element) {
                case 'buttons':
                    sourceElement = wrapper.find('.dt-buttons').first();
                    break;
                case 'search':
                    sourceElement = $('#custom-search-container').children().first();
                    break;
                case 'length':
                    sourceElement = $('#custom-length-container').children().first();
                    break;
                case 'info':
                    sourceElement = $('#custom-info-container').children().first();
                    break;
                case 'pagination':
                    sourceElement = wrapper.find('.dataTables_paginate');
                    break;
            }
            
            if (sourceElement && sourceElement.length) {
                sourceElement.appendTo(targetContainer);
            }
        },
        
        repositionElements: function(newLayout) {
            if (newLayout) {
                // Mettre à jour la configuration
                window.customLayout = newLayout;
                const positions = ['topLeft', 'topCenter', 'topRight', 'bottomLeft', 'bottomCenter', 'bottomRight'];
                
                // Vider tous les conteneurs
                positions.forEach(position => {
                    $(`.datatable-${position.replace(/([A-Z])/g, '-$1').toLowerCase()}`).empty();
                });
                
                // Repositionner selon le nouveau layout
                positions.forEach(position => {
                    if (newLayout[position]) {
                        newLayout[position].forEach(element => {
                            const container = `.datatable-${position.replace(/([A-Z])/g, '-$1').toLowerCase()}`;
                            this.moveElementTo(element, container);
                        });
                    }
                });
            } else {
                repositionElements();
            }
        },
        @endif
    };
    
    @if($config['checkboxs'])
    // Fonction pour obtenir les lignes sélectionnées
    window.getSelectedRows = function() {
        const selectedRows = [];
        $('.row-checkbox:checked').each(function() {
            const row = table.row($(this).closest('tr'));
            if (row.data()) {
                selectedRows.push(row.data());
            }
        });
        return selectedRows;
    };
    @endif
    
    // Fonction d'affichage des détails
    // window.showRowDetails = function(rowData) {
    //     let detailsHtml = '<div class="row">';
    //     @foreach($processedColumns as $column)
    //     if (rowData['{{ $column['data'] }}'] !== undefined) {
    //         detailsHtml += '<div class="col-md-6 mb-3">';
    //         detailsHtml += '<strong>{{ $column['title'] }}:</strong> ';
    //         detailsHtml += rowData['{{ $column['data'] }}'] || 'N/A';
    //         detailsHtml += '</div>';
    //     }
    //     @endforeach
    //     detailsHtml += '</div>';
        
    //     $('#row-details-content').html(detailsHtml);
    //     $('#row-details-modal').modal('show');
    // };

    window.showRowDetails = function(rowData) {
    let detailsHtml = '<div class="row">';

    @foreach($processedColumns as $column)
    if (rowData['{{ $column['data'] }}'] !== undefined) {
        let value = rowData['{{ $column['data'] }}'];
        let displayValue = '';

        if (!value) {
            displayValue = 'N/A';
        } else {
            let lowerVal = value.toString().toLowerCase();

            if (lowerVal.match(/\.(jpeg|jpg|png|gif|webp)$/)) {
                // Image
                displayValue = `<img src="${value}" alt="{{ $column['title'] }}" 
                                   class="img-fluid rounded shadow" style="max-width:250px;">`;
            } 
            else if (lowerVal.match(/\.(mp4|webm|ogg)$/)) {
                // Vidéo
                displayValue = `
                    <video controls style="max-width:300px;">
                        <source src="${value}" type="video/${lowerVal.split('.').pop()}">
                        Votre navigateur ne supporte pas la vidéo.
                    </video>`;
            }
            else if (lowerVal.match(/\.(pdf)$/)) {
                // PDF prévisualisé
                displayValue = `
                    <iframe src="${value}" style="width:100%; height:400px; border:1px solid #ddd;" 
                            class="rounded shadow"></iframe>
                    <br>
                    <a href="${value}" target="_blank" class="btn btn-sm btn-outline-danger mt-2">
                        <i class="fa fa-file-pdf"></i> Ouvrir PDF
                    </a>`;
            }
            else if (lowerVal.match(/\.(doc|docx|xls|xlsx|ppt|pptx)$/)) {
                // Documents Office via Office Online Viewer
                displayValue = `
                    <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(value)}" 
                            style="width:100%; height:400px; border:1px solid #ddd;" 
                            class="rounded shadow"></iframe>
                    <br>
                    <a href="${value}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fa fa-file-word"></i> Ouvrir Document
                    </a>`;
            }
            else if (lowerVal.startsWith('http://') || lowerVal.startsWith('https://')) {
                // Lien externe
                displayValue = `<a href="${value}" target="_blank" class="text-primary">${value}</a>`;
            }
            else {
                // Texte simple
                displayValue = value;
            }
        }

        detailsHtml += `
            <div class="col-md-6 mb-3">
                <strong>{{ $column['title'] }}:</strong><br>
                ${displayValue}
            </div>
        `;
    }
    @endforeach

    detailsHtml += '</div>';
    
    $('#row-details-content').html(detailsHtml);
    $('#row-details-modal').modal('show');
};

    
    
    // Fonction de notification
    window.showNotification = function(message, type = 'success') {
        const alertClass = type === 'error' ? 'alert-danger' : `alert-${type}`;
        const notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(notification);
        setTimeout(() => notification.alert('close'), 5000);
    };
    
    // Fonction de confirmation
    window.showConfirmModal = function(config, callback) {
        const defaultConfig = {
            title: 'Confirmation',
            message: 'Etes-vous sur de vouloir continuer ?',
            buttonAction: {color: 'primary', text: 'Confirmer'},
            niveau: 'top',
            data: null,
        }

        const {title, message, buttonAction, niveau, data} = {...defaultConfig, ...config};
        const modalId = 'confirm-modal-' + Date.now();
        const modal = $(`
            <div class="modal fade" id="${modalId}" tabindex="-1">
                <div class="modal-dialog ${niveau === 'center' ? 'modal-dialog-centered' : ''}">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body"><p>${message}</p></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="button" class="btn btn-${buttonAction.color} confirm-action">${buttonAction.text}</button>
                        </div>
                    </div>
                </div>
            </div>
        `);
        
        $('body').append(modal);
        const modalInstance = new bootstrap.Modal(modal[0]);
        modalInstance.show();
        
        modal.on('click', '.confirm-action', function() {
            if (typeof callback === 'function') {
                callback(data);
            }
            modalInstance.hide();
        });
        
        modal.on('hidden.bs.modal', function() {
            modal.remove();
        });
    };
    
    // Actions personnalisées
    $(document).on('click', '[data-action]', function(e) {
        e.preventDefault();
        const action = $(this).data('action');
        const row = table.row($(this).closest('tr'));
        const data = row.data();
        
        switch(action) {
            case 'view':
                showRowDetails(data);
                break;
            case 'edit':
                if (typeof window.editRow === 'function') {
                    window.editRow(data, row);
                }
                break;
            case 'delete':
                if (typeof window.deleteRow === 'function') {
                    window.deleteRow(data, row);
                }
                break;
            case 'duplicate':
                if (typeof window.duplicateRow === 'function') {
                    window.duplicateRow(data, row);
                }
                break;
            default:
                if (typeof window[action] === 'function') {
                    window[action](data, row);
                }
        }
    });

    // Gestionnaire pour les actions groupées
    $(document).on('click', '[data-bulk-action]', function(e) {
        e.preventDefault();
        const action = $(this).data('bulk-action');
        const selectedRows = getSelectedRows();
        
        if (selectedRows.length === 0) {
            alert('Veuillez sélectionner au moins une ligne');
            return;
        }
        
        switch(action) {
            case 'delete':
                if (typeof window.bulkDelete === 'function') {
                    window.bulkDelete(selectedRows);
                }
                break;
            case 'export':
                if (typeof window.bulkExport === 'function') {
                    window.bulkExport(selectedRows);
                }
                break;
            case 'update':
                if (typeof window.bulkUpdate === 'function') {
                    window.bulkUpdate(selectedRows);
                }
                break;
            default:
                if (typeof window['bulk' + action.charAt(0).toUpperCase() + action.slice(1)] === 'function') {
                    window['bulk' + action.charAt(0).toUpperCase() + action.slice(1)](selectedRows);
                }
        }
    });
    
    // Gestionnaire pour les tooltips
    $(document).on('mouseenter', '[data-bs-toggle="tooltip"]', function() {
        $(this).tooltip('show');
    });
    
    // Gestionnaire pour les popovers
    $(document).on('click', '[data-bs-toggle="popover"]', function() {
        $(this).popover('toggle');
    });
    
    // Sauvegarde automatique des filtres
    @if($config['stateSave'])
    $(document).on('change', '.column-filter, .global-filter', function() {
        const filters = {};
        $('.column-filter, .global-filter').each(function() {
            const id = $(this).attr('id');
            const value = $(this).val();
            if (value) {
                filters[id] = value;
            }
        });
        localStorage.setItem('datatable_filters_{{ $config['tableId'] }}', JSON.stringify(filters));
    });
    
    // Restauration des filtres sauvegardés
    const savedFilters = localStorage.getItem('datatable_filters_{{ $config['tableId'] }}');
    if (savedFilters) {
        const filters = JSON.parse(savedFilters);
        Object.keys(filters).forEach(function(filterId) {
            $('#' + filterId).val(filters[filterId]);
        });
    }
    @endif
    
    // Gestionnaire pour le redimensionnement des colonnes
    $(window).on('resize', function() {
        table.columns.adjust().draw();
    });
    
    // Gestionnaire pour l'impression
    $(document).on('click', '.print-table', function() {
        table.button('print:name').trigger();
    });
    
    // Gestionnaire pour le mode plein écran
    $(document).on('click', '.fullscreen-table', function() {
        const tableContainer = $('#{{ $config['tableId'] }}').closest('.card');
        if (tableContainer.hasClass('fullscreen')) {
            tableContainer.removeClass('fullscreen');
            $(this).html('<i class="fas fa-expand"></i>');
        } else {
            tableContainer.addClass('fullscreen');
            $(this).html('<i class="fas fa-compress"></i>');
        }
        setTimeout(() => {
            table.columns.adjust().draw();
        }, 100);
    });
    
    // Exposer l'instance du tableau globalement
    window.datatableInstance = table;
    
    // Callbacks personnalisés
    @if($config['customJs'])
    {!! $config['customJs'] !!}
    @endif
    
    // Initialisation terminée
    console.log('DataTable initialisé:', '{{ $config['tableId'] }}');
    
    // Événement personnalisé pour signaler que le tableau est prêt
    $(document).trigger('datatableReady', [table]);
});
</script>