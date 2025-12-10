<?php
$form = xForm();
?>



<div class="row">
    <div class="col-md-4">
        <?php echo $__env->make('user::components.contribuable-activite.contribuable-activite-form',
        [
        'contribuable' => $contribuable,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
    <div class="col-md-8">
        <?php echo $__env->make('user::components.contribuable-activite.contribuable-activite-liste', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</div><?php /**PATH C:\Users\Admin\Downloads\taxe\Modules/User\resources/views/components/contribuable/contribuable-activites.blade.php ENDPATH**/ ?>