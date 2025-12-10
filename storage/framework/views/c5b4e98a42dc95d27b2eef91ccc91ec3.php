<?php
$config = [
    'tableId' => 'table-activites',
    'ajaxUrl' => route('activites-log.data'),
    'lengthChange' => true,
    'pageLength' => 15,
    'buttons' => [],
    'serverSide' => true,
    'ajax' => [
        'data' => 'function(d) {
            d.gestionnaire_id = document.getElementById("filter-gestionnaire").value;
            d.action = document.getElementById("filter-action").value;
            d.date_debut = document.getElementById("filter-date-debut").value;
            d.date_fin = document.getElementById("filter-date-fin").value;
        }'
    ]
];

$columns = [
    [
        'title' => 'Gestionnaire',
        'data' => 'gestionnaire',
        'render' => 'function(data, type, row) {
            return `<div class="d-flex align-items-center">
                <img src="${row.gestionnaire.photo}" class="avatar me-3" alt="">
                <span class="fw-semibold">${row.gestionnaire.nom}</span>
            </div>`;
        }'
    ],
    [
        'title' => 'Action',
        'data' => 'action_label',
        'render' => 'function(data, type, row) {
            return `<span class="badge bg-${row.action_color}">
                <i class="fas ${row.action_icon} me-1"></i>${data}
            </span>`;
        }'
    ],
    [
        'title' => 'Type',
        'data' => 'model_type',
        'render' => 'function(data) {
            return `<span class="badge bg-light text-dark">${data}</span>`;
        }'
    ],
    [
        'title' => 'Description',
        'data' => 'description',
        'render' => 'function(data) {
            return `<span class="text-truncate d-inline-block" style="max-width: 300px;" title="${data}">${data}</span>`;
        }'
    ],
    [
        'title' => 'Adresse IP',
        'data' => 'ip_address',
        'render' => 'function(data) {
            return `<code class="bg-light px-2 py-1 rounded">${data || "N/A"}</code>`;
        }'
    ],
    [
        'title' => 'Date',
        'data' => 'created_at',
        'render' => 'function(data, type, row) {
            return `<div>
                <span class="d-block fw-semibold">${data}</span>
                <small class="text-muted">${row.created_at_human}</small>
            </div>`;
        }'
    ],
];

?>

<?php if (isset($component)) { $__componentOriginal5e60cfaad4ff714b0d911a049260ab59 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5e60cfaad4ff714b0d911a049260ab59 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.generic.xtable','data' => ['config' => $config,'columns' => $columns]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('generic.xtable'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['config' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($config),'columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($columns)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5e60cfaad4ff714b0d911a049260ab59)): ?>
<?php $attributes = $__attributesOriginal5e60cfaad4ff714b0d911a049260ab59; ?>
<?php unset($__attributesOriginal5e60cfaad4ff714b0d911a049260ab59); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5e60cfaad4ff714b0d911a049260ab59)): ?>
<?php $component = $__componentOriginal5e60cfaad4ff714b0d911a049260ab59; ?>
<?php unset($__componentOriginal5e60cfaad4ff714b0d911a049260ab59); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Admin\Downloads\taxe\Modules/User\resources/views/components/activites-log/activites-log-liste.blade.php ENDPATH**/ ?>