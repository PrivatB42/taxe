

<?php $__env->startSection('pageTitle', 'Contribuable - '. $action.($contribuableActivite ? ' - '.$contribuableActivite->activite?->nom : '')); ?>

<?php $__env->startSection('contribuable-content'); ?>

        <?php echo $__env->make('user::components.contribuable.'.$component, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('user::templates.contribuable-template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\Downloads\taxe\Modules/User\resources/views/pages/contribuable/show.blade.php ENDPATH**/ ?>