<div <?php echo e($attributes->merge(['class' => 'card'])); ?>>
    <?php if(isset($header)): ?>
    <div class="card-header">
        <?php echo e($header); ?>

    </div>
    <?php endif; ?>

    <div class="card-body">
        <?php echo e($slot); ?>

    </div>

    <?php if(isset($footer)): ?>
    <div class="card-footer">
        <?php echo e($footer); ?>

    </div>
    <?php endif; ?>
</div><?php /**PATH C:\Users\Admin\Downloads\taxe\resources\views/components/generic/card.blade.php ENDPATH**/ ?>