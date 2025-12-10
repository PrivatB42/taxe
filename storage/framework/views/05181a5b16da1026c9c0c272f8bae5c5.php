

<?php $__env->startSection('pageTitle', 'Gestionnaires'); ?>

<?php $__env->startSection('content'); ?>

<div class="row">
    <div class="col-lg-4 p-3">
        <?php echo $__env->make('user::components.gestionnaire.gestionnaire-form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <div class="col-lg-8 pt-3">
        <?php echo $__env->make('user::components.gestionnaire.gestionnaire-liste', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('templates.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\Downloads\taxe\Modules/User\resources/views/pages/gestionnaire/index.blade.php ENDPATH**/ ?>