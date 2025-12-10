<?php
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
?>


<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<?php if($hasButtons): ?>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<?php endif; ?>
<?php if($config['responsive']): ?>
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<?php endif; ?>
<?php if($config['fixedHeader']): ?>
<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.bootstrap5.min.css">
<?php endif; ?>
<?php if($config['select']): ?>
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.bootstrap5.min.css">
<?php endif; ?>
<?php if($config['colReorder']): ?>
<link rel="stylesheet" href="https://cdn.datatables.net/colreorder/1.7.0/css/colReorder.bootstrap5.min.css">
<?php endif; ?>
<?php if($config['fixedColumns']): ?>
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.bootstrap5.min.css">
<?php endif; ?>
<?php if($config['rowReorder']): ?>
<link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.4.1/css/rowReorder.bootstrap5.min.css">
<?php endif; ?>
<?php if($config['keyTable']): ?>
<link rel="stylesheet" href="https://cdn.datatables.net/keytable/2.9.0/css/keyTable.bootstrap5.min.css">
<?php endif; ?>


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


<div class="datatable-wrapper <?php echo e($useCustomLayout ? 'custom-layout' : ''); ?>">
    
    <?php if($useCustomLayout): ?>
    
    <div class="datatable-top-controls">
        <div class="datatable-top-left">
            <?php if(in_array('buttons', $config['customLayout']['topLeft'] ?? [])): ?>
                <div id="custom-buttons-container"></div>
            <?php endif; ?>
            <?php if(in_array('length', $config['customLayout']['topLeft'] ?? [])): ?>
                <div id="custom-length-container"></div>
            <?php endif; ?>
            <?php if(in_array('search', $config['customLayout']['topLeft'] ?? [])): ?>
                <div id="custom-search-container"></div>
            <?php endif; ?>
            <?php if(in_array('info', $config['customLayout']['topLeft'] ?? [])): ?>
                <div id="custom-info-container"></div>
            <?php endif; ?>
            <?php if(in_array('pagination', $config['customLayout']['topLeft'] ?? [])): ?>
                <div id="custom-pagination-container"></div>
            <?php endif; ?>
            <?php if(in_array('widget', $config['customLayout']['topLeft'] ?? [])): ?>
                <?php echo $config['widgets']['topLeft'] ?? ''; ?>

            <?php endif; ?>
        </div>
        
        <div class="datatable-top-center">
            <?php if(in_array('buttons', $config['customLayout']['topCenter'] ?? [])): ?>
                <div id="custom-buttons-container"></div>
            <?php endif; ?>
            <?php if(in_array('length', $config['customLayout']['topCenter'] ?? [])): ?>
                <div id="custom-length-container"></div>
            <?php endif; ?>
            <?php if(in_array('search', $config['customLayout']['topCenter'] ?? [])): ?>
                <div id="custom-search-container"></div>
            <?php endif; ?>
            <?php if(in_array('info', $config['customLayout']['topCenter'] ?? [])): ?>
                <div id="custom-info-container"></div>
            <?php endif; ?>
            <?php if(in_array('pagination', $config['customLayout']['topCenter'] ?? [])): ?>
                <div id="custom-pagination-container"></div>
            <?php endif; ?>
            <?php if(in_array('widget', $config['customLayout']['topCenter'] ?? [])): ?>
                <?php echo $config['widgets']['topCenter'] ?? ''; ?>

            <?php endif; ?>
        </div>
        
        <div class="datatable-top-right">
            <?php if(in_array('buttons', $config['customLayout']['topRight'] ?? [])): ?>
                <div id="custom-buttons-container"></div>
            <?php endif; ?>
            <?php if(in_array('length', $config['customLayout']['topRight'] ?? [])): ?>
                <div id="custom-length-container"></div>
            <?php endif; ?>
            <?php if(in_array('search', $config['customLayout']['topRight'] ?? [])): ?>
                <div id="custom-search-container"></div>
            <?php endif; ?>
            <?php if(in_array('info', $config['customLayout']['topRight'] ?? [])): ?>
                <div id="custom-info-container"></div>
            <?php endif; ?>
            <?php if(in_array('pagination', $config['customLayout']['topRight'] ?? [])): ?>
                <div id="custom-pagination-container"></div>
            <?php endif; ?>
            <?php if(in_array('widget', $config['customLayout']['topRight'] ?? [])): ?>
                <?php echo $config['widgets']['topRight'] ?? ''; ?>

            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- <?php if(!$useCustomLayout && $hasButtons && ($config['buttonPosition'] ?? 'top') === 'top'): ?>
        <div class="mb-3">
            <div id="buttons-container-<?php echo e($tableId); ?>"></div>
        </div>
    <?php endif; ?> -->

    
    <div class="position-relative">
        <div id="custom-processing-container" class="processing-indicator" style="display: none;">
            <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                <span>Chargement en cours...</span>
            </div>
        </div>

        
        <table id="<?php echo e($tableId); ?>" class="<?php echo e($config['tableClass']); ?>" <?php echo e(collect($config['tableAttributes'])->map(fn($v, $k) => "$k=\"$v\"")->implode(' ')); ?>>
            <thead>
                <tr>
                    <?php if($config['checkboxs']): ?>
                    <th>
                        <input type="checkbox" id="select-all" class="form-check-input">
                    </th>
                    <?php endif; ?>
                    <?php $__currentLoopData = $processedColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <th <?php echo isset($column['attributes']) ? collect($column['attributes'])->map(fn($v, $k) => "$k=\"$v\"")->implode(' ') : ''; ?>>
                        <?php echo e($column['title']); ?>

                    </th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            </thead>
            <?php if($config['individualColumnSearch']): ?>
            <thead>
                <tr>
                    <?php if($config['checkboxs']): ?>
                    <td></td>
                    <?php endif; ?>
                    <?php $__currentLoopData = $processedColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td>
                        <?php if($column['searchable']): ?>
                            <?php if($column['searchType'] === 'select'): ?>
                            <select class="form-control form-select form-select-sm individual-search" 
                                    data-column="<?php echo e($config['checkboxs'] ? $loop->index + 1 : $loop->index); ?>" 
                                    id="individual-search-<?php echo e($column['data']); ?>">
                                <option value="">Tous</option>
                                <?php $__currentLoopData = $column['searchOptions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php elseif($column['searchType'] === 'checkbox'): ?>
                            <div class="<?php echo e($column['searchCheck']['class'] ?? 'd-flex justify-content-center gap-2'); ?>">
                                <input type="checkbox" 
                                       data-column="<?php echo e($config['checkboxs'] ? $loop->index + 1 : $loop->index); ?>" 
                                       value="<?php echo e($column['searchCheck']['value'] ?? true); ?>" 
                                       class="form-check individual-search" 
                                       id="individual-search-<?php echo e($column['data']); ?>">
                                <span><?php echo e($column['searchCheck']['label'] ?? ''); ?></span>
                            </div>
                            <?php elseif(in_array($column['searchType'], ['date', 'datetime-local', 'text', 'number', 'color', 'month', 'time', 'week'])): ?>
                            <input type="<?php echo e($column['searchType']); ?>" 
                                   class="form-control form-control-sm individual-search" 
                                   id="individual-search-<?php echo e($column['data']); ?>"
                                   placeholder="Rechercher <?php echo e(strtolower($column['title'])); ?>" 
                                   data-column="<?php echo e($config['checkboxs'] ? $loop->index + 1 : $loop->index); ?>">
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            </thead>
            <?php endif; ?>
        </table>
    </div>

    <?php if($useCustomLayout): ?>
    
    <div class="datatable-bottom-controls">
        <div class="datatable-bottom-left">
            <?php if(in_array('buttons', $config['customLayout']['bottomLeft'] ?? [])): ?>
                <div id="custom-buttons-container"></div>
            <?php endif; ?>
            <?php if(in_array('length', $config['customLayout']['bottomLeft'] ?? [])): ?>
                <div id="custom-length-container"></div>
            <?php endif; ?>
            <?php if(in_array('search', $config['customLayout']['bottomLeft'] ?? [])): ?>
                <div id="custom-search-container"></div>
            <?php endif; ?>
            <?php if(in_array('info', $config['customLayout']['bottomLeft'] ?? [])): ?>
                <div id="custom-info-container"></div>
            <?php endif; ?>
            <?php if(in_array('pagination', $config['customLayout']['bottomLeft'] ?? [])): ?>
                <div id="custom-pagination-container"></div>
            <?php endif; ?>
            <?php if(in_array('widget', $config['customLayout']['bottomLeft'] ?? [])): ?>
                <?php echo $config['widgets']['bottomLeft'] ?? ''; ?>

            <?php endif; ?>
        </div>
        
        <div class="datatable-bottom-center">
            <?php if(in_array('buttons', $config['customLayout']['bottomCenter'] ?? [])): ?>
                <div id="custom-buttons-container"></div>
            <?php endif; ?>
            <?php if(in_array('length', $config['customLayout']['bottomCenter'] ?? [])): ?>
                <div id="custom-length-container"></div>
            <?php endif; ?>
            <?php if(in_array('search', $config['customLayout']['bottomCenter'] ?? [])): ?>
                <div id="custom-search-container"></div>
            <?php endif; ?>
            <?php if(in_array('info', $config['customLayout']['bottomCenter'] ?? [])): ?>
                <div id="custom-info-container"></div>
            <?php endif; ?>
            <?php if(in_array('pagination', $config['customLayout']['bottomCenter'] ?? [])): ?>
                <div id="custom-pagination-container"></div>
            <?php endif; ?>
            <?php if(in_array('widget', $config['customLayout']['bottomCenter'] ?? [])): ?>
                <?php echo $config['widgets']['bottomCenter'] ?? ''; ?>

            <?php endif; ?>
        </div>
        
        <div class="datatable-bottom-right">
            <?php if(in_array('buttons', $config['customLayout']['bottomRight'] ?? [])): ?>
                <div id="custom-buttons-container"></div>
            <?php endif; ?>
            <?php if(in_array('length', $config['customLayout']['bottomRight'] ?? [])): ?>
                <div id="custom-length-container"></div>
            <?php endif; ?>
            <?php if(in_array('search', $config['customLayout']['bottomRight'] ?? [])): ?>
                <div id="custom-search-container"></div>
            <?php endif; ?>
            <?php if(in_array('info', $config['customLayout']['bottomRight'] ?? [])): ?>
                <div id="custom-info-container"></div>
            <?php endif; ?>
            <?php if(in_array('pagination', $config['customLayout']['bottomRight'] ?? [])): ?>
                <div id="custom-pagination-container"></div>
            <?php endif; ?>
            <?php if(in_array('widget', $config['customLayout']['bottomRight'] ?? [])): ?>
                <?php echo $config['widgets']['bottomRight'] ?? ''; ?>

            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if(!$useCustomLayout && $hasButtons && ($config['buttonPosition'] ?? 'top') === 'bottom'): ?>
    <div class="mt-3">
        <div id="buttons-container-bottom-<?php echo e($tableId); ?>"></div>
    </div>
    <?php endif; ?>
</div>


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


<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<?php if($hasButtons): ?>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<?php endif; ?>

<?php if($config['responsive']): ?>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<?php endif; ?>

<?php if($config['fixedHeader']): ?>
<script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>
<?php endif; ?>

<?php if($config['select']): ?>
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
<?php endif; ?>

<?php if($config['colReorder']): ?>
<script src="https://cdn.datatables.net/colreorder/1.7.0/js/dataTables.colReorder.min.js"></script>
<?php endif; ?>

<?php if($config['fixedColumns']): ?>
<script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>
<?php endif; ?>

<?php if($config['rowReorder']): ?>
<script src="https://cdn.datatables.net/rowreorder/1.4.1/js/dataTables.rowReorder.min.js"></script>
<?php endif; ?>

<?php if($config['keyTable']): ?>
<script src="https://cdn.datatables.net/keytable/2.9.0/js/dataTables.keyTable.min.js"></script>
<?php endif; ?>

<script>
$(document).ready(function() {
    'use strict';
    var filterQuery = null;
    
    // Configuration centralisée
    const tableConfig = {
        processing: <?php echo e(booleanToString($config['processing'])); ?>,
        serverSide: <?php echo e(booleanToString($config['serverSide'])); ?>,
        responsive: <?php echo e(booleanToString($config['responsive'])); ?>,
        ordering: <?php echo e(booleanToString($config['ordering'])); ?>,
        info: <?php echo e(booleanToString($config['info'])); ?>,
        searching: <?php echo e(booleanToString($config['searching'])); ?>,
        paging: <?php echo e(booleanToString($config['paging'])); ?>,
        autoWidth: <?php echo e(booleanToString($config['autoWidth'])); ?>,
        stateSave: <?php echo e(booleanToString($config['stateSave'])); ?>,
        searchDelay: <?php echo e($config['searchDelay']); ?>,
        pageLength: <?php echo e($config['pageLength']); ?>,
        lengthMenu: <?php echo json_encode($config['lengthMenu']); ?>,
        order: <?php echo json_encode($config['order']); ?>,
        scrollCollapse: <?php echo e(booleanToString($config['scrollCollapse'])); ?>,
        scrollY: <?php echo json_encode($config['scrollY']); ?>,
        scrollX: <?php echo e(booleanToString($config['scrollX'])); ?>,
        lengthChange: <?php echo e(booleanToString($config['lengthChange'])); ?>,
        
        <?php if($config['language'] && isset($languages[$config['language']])): ?>
        language: {
            url: '<?php echo e($languages[$config['language']]); ?>'
        },
        <?php endif; ?>
        
        // Configuration du DOM
        dom: '<?php echo e($domLayout); ?>',
        
        <?php if($config['serverSide'] && !empty($config['ajaxUrl'])): ?>
        ajax: {
            url: <?php echo json_encode($config['ajaxUrl'], 15, 512) ?> ,
            type: 'POST',
            data: function(d) {
                d.filters = [];
                <?php if(!empty($config['filters'])): ?>
                <?php $__currentLoopData = $config['filters']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                d.filters.push({
                    name: '<?php echo e($filter['name']); ?>',
                    value: $('#<?php echo e($filter['id']); ?>').val()
                });
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>

            if (filterQuery) {
                d.filters.push(filterQuery); 
            }
             
                
                d._token = $('meta[name="csrf-token"]').attr('content');
                
                if (!<?php echo e(booleanToString($config['paging'])); ?>) {
                    d.length = <?php echo e($config['pageLength']); ?>;
                }
            },
            error: function(xhr, error, thrown) {
                console.error('DataTable Ajax Error:', error);
                showNotification('Erreur lors du chargement des données', 'error');
            }
        },
        <?php endif; ?>
        
        columns: [
            <?php if($config['checkboxs']): ?>
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
            <?php endif; ?>
            <?php $__currentLoopData = $processedColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            {
                data: '<?php echo e($column['data']); ?>',
                name: '<?php echo e($column['name']); ?>',
                title: '<?php echo e($column['title']); ?>',
                orderable: <?php echo e(booleanToString($column['orderable'])); ?>,
                searchable: <?php echo e(booleanToString($column['searchable'])); ?>,
                visible: <?php echo e(booleanToString($column['visible'])); ?>,
                className: '<?php echo e($column['className']); ?>',
                <?php if($column['width']): ?>
                width: '<?php echo e($column['width']); ?>',
                <?php endif; ?>
                type: '<?php echo e($column['type']); ?>',
                <?php if($column['render']): ?>
                render: <?php echo $column['render']; ?>,
                <?php endif; ?>
                <?php if($column['createdCell']): ?>
                createdCell: <?php echo $column['createdCell']; ?>,
                <?php endif; ?>
                <?php if($column['defaultContent']): ?>
                defaultContent: '<?php echo e($column['defaultContent']); ?>',
                <?php endif; ?>
            }<?php echo e(!$loop->last ? ',' : ''); ?>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        ],
        
        <?php if($hasButtons): ?>
        buttons: {
            dom: {
                button: {
                    className: '<?php echo e($config['buttonClass'] ?? 'btn btn-sm btn-outline-primary'); ?>'
                }
                // container: {
                //     className: '<?php echo e($config['buttonAlignment'] ?? 'text-start'); ?> custom-buttons'
                // }
            },
            buttons: [
                <?php $__currentLoopData = $config['buttons']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $button): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($button === 'copy'): ?>
                {
                    extend: 'copy',
                    text: '<i class="fas fa-copy"></i> Copier',
                    className: 'btn btn-sm btn-outline-info'
                },
                <?php elseif($button === 'csv'): ?>
                {
                    extend: 'csv',
                    text: '<i class="fas fa-file-csv"></i> CSV',
                    className: 'btn btn-sm btn-outline-success'
                },
                <?php elseif($button === 'excel'): ?>
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-sm btn-outline-success'
                },
                <?php elseif($button === 'pdf'): ?>
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-sm btn-outline-danger',
                    orientation: 'landscape',
                    pageSize: 'A4'
                },
                <?php elseif($button === 'print'): ?>
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Imprimer',
                    className: 'btn btn-sm btn-outline-secondary'
                },
                <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ]
        },
        <?php endif; ?>
        
        <?php if($config['select']): ?>
        select: {
            style: 'multi',
            selector: 'td:first-child'
        },
        <?php endif; ?>
        
        // Callbacks
        <?php if($config['drawCallback']): ?>
        drawCallback: function(settings) {
            <?php echo $config['drawCallback']; ?>

        },
        <?php endif; ?>
        
        <?php if($config['initComplete']): ?>
        initComplete: function(settings, json) {
            <?php echo $config['initComplete']; ?>

            
            // Repositionnement des éléments en mode custom
            <?php if($useCustomLayout): ?>
            setTimeout(function() {
                repositionElements();
            }, 100);
            <?php endif; ?>
        },
        <?php endif; ?>
        
        <?php if($config['rowCallback']): ?>
        rowCallback: function(row, data, index) {
            <?php echo $config['rowCallback']; ?>

        },
        <?php endif; ?>
        
        <?php if($config['createdRow']): ?>
        createdRow: function(row, data, dataIndex) {
            <?php echo $config['createdRow']; ?>

        },
        <?php endif; ?>
        
        <?php if($config['headerCallback']): ?>
        headerCallback: function(thead, data, start, end, display) {
            <?php echo $config['headerCallback']; ?>

        },
        <?php endif; ?>
        
        <?php if($config['footerCallback']): ?>
        footerCallback: function(tfoot, data, start, end, display) {
            <?php echo $config['footerCallback']; ?>

        },
        <?php endif; ?>
        
        <?php if($config['preDrawCallback']): ?>
        preDrawCallback: function(settings) {
            <?php echo $config['preDrawCallback']; ?>

        },
        <?php endif; ?>
        
        <?php if($config['stateLoadCallback']): ?>
        stateLoadCallback: function(settings) {
            <?php echo $config['stateLoadCallback']; ?>

        },
        <?php endif; ?>
        
        <?php if($config['stateSaveCallback']): ?>
        stateSaveCallback: function(settings, data) {
            <?php echo $config['stateSaveCallback']; ?>

        },
        <?php endif; ?>
        
        <?php if($config['infoCallback']): ?>
        infoCallback: function(settings, data) {
            <?php echo $config['infoCallback']; ?>

        },
        <?php endif; ?>
    }; 
    
    
    // Initialisation du DataTable
    if (!$('#<?php echo e($tableId); ?>').length) {
        console.error('Table element not found:', '<?php echo e($tableId); ?>');
        return;
    }
    
    const table = $('#<?php echo e($tableId); ?>').DataTable(tableConfig);
    
    <?php if($useCustomLayout): ?>
    // Fonction pour repositionner les éléments DataTable
    function repositionElements() {
        const wrapper = $('#<?php echo e($tableId); ?>_wrapper');
        
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
        window.customLayout = <?php echo json_encode($config['customLayout']); ?>;
        
        // Déplacer les boutons
        <?php if($hasButtons): ?>
        const buttons = wrapper.find('.dt-buttons').first();
        if (buttons.length) {
            const targetContainer = findTargetContainer('buttons');
            const container = $(targetContainer);
            if (container.length) {
                buttons.appendTo(container);
            }
        }
        <?php endif; ?>
        
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
    <?php else: ?>
    // Gestion normale des boutons pour les autres modes
    <?php if($hasButtons): ?>
    <?php if(($config['buttonPosition'] ?? 'top') === 'bottom'): ?>
    table.buttons().container().appendTo('#buttons-container-bottom-<?php echo e($tableId); ?>');
    <?php elseif(($config['buttonPosition'] ?? 'top') === 'both'): ?>
    table.buttons().container().clone().appendTo('#buttons-container-bottom-<?php echo e($tableId); ?>');
    table.buttons().container().appendTo('#buttons-container-<?php echo e($tableId); ?>');
    <?php else: ?>
    table.buttons().container().appendTo('#buttons-container-<?php echo e($tableId); ?>');
    <?php endif; ?>
    <?php endif; ?>
    <?php endif; ?>
    
    // Extensions
    <?php if($config['fixedHeader']): ?>
    new $.fn.dataTable.FixedHeader(table);
    <?php endif; ?>
    
    <?php if($config['keyTable']): ?>
    new $.fn.dataTable.KeyTable(table);
    <?php endif; ?>
    
    <?php if($config['fixedColumns']): ?>
    new $.fn.dataTable.FixedColumns(table, {
        leftColumns: <?php echo e(is_array($config['fixedColumns']) ? ($config['fixedColumns']['left'] ?? 1) : 1); ?>,
        rightColumns: <?php echo e(is_array($config['fixedColumns']) ? ($config['fixedColumns']['right'] ?? 0) : 0); ?>

    });
    <?php endif; ?>
    
    <?php if($config['colReorder']): ?>
    new $.fn.dataTable.ColReorder(table);
    <?php endif; ?>
    
    <?php if($config['rowReorder']): ?>
    new $.fn.dataTable.RowReorder(table);
    <?php endif; ?>
    
    // Event Handlers
    <?php if($config['rowEvent']): ?>
    table.on('click', 'tbody tr', function() {
        const data = table.row(this).data();
        <?php echo $config['rowEvent']; ?>

    });
    <?php endif; ?>
    
    // Recherche individuelle par colonne
    <?php if($config['individualColumnSearch']): ?>
    $('.individual-search').on('keyup change clear', function() {
        const column = $(this).data('column');
        const value = $(this).val();
        
        if (table.column(column).search() !== value) {
            table.column(column).search(value).draw();
        }
    });
    <?php endif; ?>
    
    // Gestion des filtres
    <?php if(!empty($config['filters']) && $config['btnFiltrer']): ?>
    $('<?php echo e($config['btnFiltrer']); ?>').on('click', function() {
        table.ajax.reload();
    });
    <?php endif; ?>

    <?php if(!empty($config['btnFiltrerQuery']) && $config['btnFiltrerQuery']): ?>
    $('<?php echo e($config['btnFiltrerQuery']); ?>').on('click', function() {

        var filterName = $(this).data('filtername');
        var filterValue = $(this).data('filtervalue');

        // Ajouter le filtre au tableau
            filterQuery = {
                    name: filterName,
                    value: filterValue
                    }; 
        table.ajax.reload();
    });
    <?php endif; ?>

    <?php if(!empty($config['btnResetFiltre']) && $config['btnResetFiltre']): ?>
    $('<?php echo e($config["btnResetFiltre"]); ?>').on('click', function() {
        filterQuery = null;
        table.ajax.reload();
    });
    <?php endif; ?>
    
    // Gestion des checkboxs
    <?php if($config['checkboxs']): ?>
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
    <?php endif; ?>
    
    // Fonctions utilitaires globales
    window.DataTableUtils = window.DataTableUtils || {};
    window.DataTableUtils['<?php echo e($tableId); ?>'] = {
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
        <?php if($useCustomLayout): ?>
        moveElementTo: function(element, targetContainer) {
            const wrapper = $('#<?php echo e($tableId); ?>_wrapper');
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
        <?php endif; ?>
    };
    
    <?php if($config['checkboxs']): ?>
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
    <?php endif; ?>
    
    // Fonction d'affichage des détails
    // window.showRowDetails = function(rowData) {
    //     let detailsHtml = '<div class="row">';
    //     <?php $__currentLoopData = $processedColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    //     if (rowData['<?php echo e($column['data']); ?>'] !== undefined) {
    //         detailsHtml += '<div class="col-md-6 mb-3">';
    //         detailsHtml += '<strong><?php echo e($column['title']); ?>:</strong> ';
    //         detailsHtml += rowData['<?php echo e($column['data']); ?>'] || 'N/A';
    //         detailsHtml += '</div>';
    //     }
    //     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    //     detailsHtml += '</div>';
        
    //     $('#row-details-content').html(detailsHtml);
    //     $('#row-details-modal').modal('show');
    // };

    window.showRowDetails = function(rowData) {
    let detailsHtml = '<div class="row">';

    <?php $__currentLoopData = $processedColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    if (rowData['<?php echo e($column['data']); ?>'] !== undefined) {
        let value = rowData['<?php echo e($column['data']); ?>'];
        let displayValue = '';

        if (!value) {
            displayValue = 'N/A';
        } else {
            let lowerVal = value.toString().toLowerCase();

            if (lowerVal.match(/\.(jpeg|jpg|png|gif|webp)$/)) {
                // Image
                displayValue = `<img src="${value}" alt="<?php echo e($column['title']); ?>" 
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
                <strong><?php echo e($column['title']); ?>:</strong><br>
                ${displayValue}
            </div>
        `;
    }
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

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
    <?php if($config['stateSave']): ?>
    $(document).on('change', '.column-filter, .global-filter', function() {
        const filters = {};
        $('.column-filter, .global-filter').each(function() {
            const id = $(this).attr('id');
            const value = $(this).val();
            if (value) {
                filters[id] = value;
            }
        });
        localStorage.setItem('datatable_filters_<?php echo e($config['tableId']); ?>', JSON.stringify(filters));
    });
    
    // Restauration des filtres sauvegardés
    const savedFilters = localStorage.getItem('datatable_filters_<?php echo e($config['tableId']); ?>');
    if (savedFilters) {
        const filters = JSON.parse(savedFilters);
        Object.keys(filters).forEach(function(filterId) {
            $('#' + filterId).val(filters[filterId]);
        });
    }
    <?php endif; ?>
    
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
        const tableContainer = $('#<?php echo e($config['tableId']); ?>').closest('.card');
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
    <?php if($config['customJs']): ?>
    <?php echo $config['customJs']; ?>

    <?php endif; ?>
    
    // Initialisation terminée
    console.log('DataTable initialisé:', '<?php echo e($config['tableId']); ?>');
    
    // Événement personnalisé pour signaler que le tableau est prêt
    $(document).trigger('datatableReady', [table]);
});
</script><?php /**PATH C:\Users\Admin\Downloads\taxe\resources\views/components/generic/xtable.blade.php ENDPATH**/ ?>