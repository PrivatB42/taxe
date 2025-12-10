

<?php $__env->startSection('content'); ?>

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
     <?php $__env->slot('header', null, []); ?> 
        <i class="fas fa-user"></i>
        <span id="card-title"><?php echo e(ucfirst($contribuable->personne?->nom_complet)); ?></span> |
        <span><?php echo e($contribuable->matricule); ?></span>

        <div class="float-end">
            <div class="row">
                <div class="col-lg-6">
                    <?php echo $__env->make('user::components.contribuable.contribuable-menu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
                <div class="col-lg-6">
                     <a href="<?php echo e(route('contribuables.index')); ?>" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Retour </a>
                </div>
            </div>
        </div>
     <?php $__env->endSlot(); ?>


    <?php echo $__env->yieldContent('contribuable-content'); ?>
    

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

<?php $__env->stopSection(); ?>
<?php echo $__env->make('templates.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\Downloads\taxe\Modules/User\resources/views/templates/contribuable-template.blade.php ENDPATH**/ ?>