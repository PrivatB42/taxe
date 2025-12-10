<?php
$config = [
'tableId' => 'table-id',
'ajaxUrl' => route('contribuables-parametres.data').'?contribuable_activite_id='.$contribuableActivite->id,
'lengthChange' => false,
'buttons' => [],
];

$columns = [

[
'title' => 'Paramètre',
'data' => 'nom',
'render' => 'function(data, type, row, meta) {
return `${data} : ${row.type}`
}',
],

[
'title' => 'Valeur',
'data' => 'valeur',
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
    var form = 'form-id';
    var btnForm = 'btn-form-id';
    var routeStore = "<?php echo e(route('contribuables-parametres.store')); ?>";
    var routeUpdate = "<?php echo e(route('contribuables-parametres.update', ':id')); ?>";
    var routeToggle = "<?php echo e(route('contribuables-parametres.toggle-active', ':id')); ?>";
    var titleForm = 'Ajouter';
    var titleUpdate = 'Modifier';
    var inputsId = ['nom', 'type', 'valeur'];

    function arrayButtons(data, type, row, meta) {
        return `
                <a href="#" onClick="editForm(${meta.row})" 
                   class="btn btn-sm btn-secondary btn-sm">
                   <i class="fas fa-edit"></i>
                </a>

                <button onClick="toggle(${row.id}, ${row.is_active})" 
                    class="btn btn-sm btn-${row.is_active ? 'danger' : 'success'}">
                    <i class="fas fa-toggle-${row.is_active ? 'off' : 'on'}"></i>
                </button>

            ` ;
    }

    document.addEventListener('DOMContentLoaded', function() {

        x_form_fetch(form, btnForm, {
            successCallback: 'refreshTable',
            formResetCallback: 'resetForm'
        });
    });

    function refreshTable() {
        x_datatable(tableName).refreshTable()
        x_inner('x-alerts-container', '');
    }

    function resetForm() {
        x_reset_form(form, {
            form_action: routeStore,
            form_method: 'POST'
        });
        x_inner('card-title', titleForm);
    }


    function editForm($index) {
        const table = x_datatable(tableName);
        const data = table.getRowData($index);

        x_form_edit(
            form,
            inputsId,
            data, {
                form_action: routeUpdate.replace(':id', data.id),
                form_method: 'POST',
            },
        );

        x_inner('card-title', titleUpdate);
    }


    function toggle(id, is_active) {
        const table = x_datatable(tableName);
        const configModal = configModalChangeStatut(
            is_active ? 'Voulez-vous vraiment desactiver ?' : 'Voulez-vous vraiment activer ?',
            id,
            function(config) {
                config.buttonAction.color = is_active ? 'danger' : 'success';
                config.buttonAction.text = is_active ? 'Desactiver' : 'Activer';
            }
        );

        const action = (id) => {
            const url = routeToggle.replace(':id', id);
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

        confirmModal(configModal, action);
    }

</script> <?php /**PATH C:\Users\Admin\Downloads\taxe\Modules/User\resources/views/components/contribuable-parametre/contribuable-parametre-liste.blade.php ENDPATH**/ ?>