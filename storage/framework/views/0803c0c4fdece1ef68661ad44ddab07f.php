

<?php $__env->startSection('pageTitle', 'Caisse'); ?>

<?php $__env->startSection('content'); ?>


        <?php echo $__env->make('paiement::components.caisse.caisse-liste', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    

<?php $__env->stopSection(); ?>
<?php echo $__env->make('templates.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\Downloads\taxe\Modules/Paiement\resources/views/pages/caisse/index.blade.php ENDPATH**/ ?>