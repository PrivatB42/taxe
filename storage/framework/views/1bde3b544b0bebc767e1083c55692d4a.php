

<?php $__env->startSection('pageTitle', 'Contribuables'); ?>

<?php $__env->startSection('content'); ?>

<div class="row">
    <div class="col-lg-4 p-3">
        <?php echo $__env->make('user::components.contribuable.contribuable-form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <div class="col-lg-8 pt-3">
        <?php echo $__env->make('user::components.contribuable.contribuable-liste', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('templates.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\Downloads\taxe\Modules/User\resources/views/pages/contribuable/index.blade.php ENDPATH**/ ?>