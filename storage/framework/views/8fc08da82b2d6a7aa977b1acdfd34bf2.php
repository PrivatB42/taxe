<?php
$widgets = [
//'topRight' => button_modal('Ajouter', 'btn btn-sm btn-primary', 'fas fa-plus', 'form-modal'),
'topRight' => '<button class="btn btn-sm btn-primary"  onClick="toggle(`create`)"> <i class="fas fa-plus"></i> Ajouter </button>'
];
$config = [
'tableId' => 'table-id',
'ajaxUrl' => route('caisses.data'),
'lengthChange' => true,
'buttons' => [],
'customLayout' => [
'topLeft' => ['length'],
'topRight' => ['widget'],
'topCenter' => ['search'],
'bottomLeft' => ['info'],
'bottomCenter' => [],
'bottomRight' => ['pagination']
],
'widgets' => $widgets,
];

$columns = [
[
'title' => 'Caisse',
'data' => 'nom',
],

[
'title' => 'Statut',
'data' => 'statut',
],

[
'data' => 'id',
'title' => 'Actions',
'render' => 'function(data, type, row, meta) {
return arrayButtons(data, type, row, meta)
}',
'searchable' => false,
'orderable' => false,
'className' => 'text-center',
]
];

$config['rowCallback'] = 'if (!data.is_active) {
$(row).addClass("table-danger");
}';


?>

<?php if (isset($component)) { $__componentOriginal2c374f138d53ce79c84b3f047c7b2a22 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2c374f138d53ce79c84b3f047c7b2a22 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.generic.card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('generic.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
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
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2c374f138d53ce79c84b3f047c7b2a22)): ?>
<?php $attributes = $__attributesOriginal2c374f138d53ce79c84b3f047c7b2a22; ?>
<?php unset($__attributesOriginal2c374f138d53ce79c84b3f047c7b2a22); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2c374f138d53ce79c84b3f047c7b2a22)): ?>
<?php $component = $__componentOriginal2c374f138d53ce79c84b3f047c7b2a22; ?>
<?php unset($__componentOriginal2c374f138d53ce79c84b3f047c7b2a22); ?>
<?php endif; ?>


<script>
    var tableName = 'table-id';
    var routeStore = "<?php echo e(route('caisses.store')); ?>";
    var routeToggle = "<?php echo e(route('caisses.toggle-active', ':id')); ?>";

    function arrayButtons(data, type, row, meta) {
        return `

                 <button onClick="toggle('toggle', ${row.id}, ${row.is_active})" 
                    class="btn btn-sm btn-${row.is_active ? 'danger' : 'success'}">
                    <i class="fas fa-toggle-${row.is_active ? 'off' : 'on'}"></i>
                </button>

            `;
    }


    function toggle(actionName, id = null, is_active = null) {
        const table = x_datatable(tableName);
        const actions = {
            create : {
                text: 'Voulez-vous vraiment créer une caisse ?',
                route: routeStore,
                colorButton: 'success',
                colorText: 'Créer'
            },
            toggle: {
                text: is_active ? 'Voulez-vous vraiment desactiver ?' : 'Voulez-vous vraiment activer ?',
                route: routeToggle.replace(':id', id),
                colorButton: is_active ? 'danger' : 'success',
                colorText: is_active ? 'Desactiver' : 'Activer'
            }
        };
        const action = actions[actionName]
        const configModal = configModalChangeStatut(
            action.text,
            id,
            function(config) {
                config.buttonAction.color = action.colorButton;
                config.buttonAction.text = action.colorText;
            }
        );

        const x_action = (id) => {
            const url = action.route;
            const callBacks = {
                success: function(result, response) {
                    x_successNotification(result.message);
                    table.refreshTable();
                },
                error: function(error) {
                    x_errorAlert(error.message);
                }
            };
            x_fetch(url, optionsPost(), null, callBacks);
        }

        confirmModal(configModal, x_action);
    }
</script><?php /**PATH C:\Users\Admin\Downloads\taxe\Modules/Paiement\resources/views/components/caisse/caisse-liste.blade.php ENDPATH**/ ?>