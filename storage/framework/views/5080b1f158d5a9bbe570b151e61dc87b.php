<?php

$form = xFormBuilder();

$form->action(route('gestionnaires.store'))
->method('POST')
->form(['id' => 'form-id'])
->csrf(true)


->text('nom_complet',
'Nom complet',
[
'required' => true,
'placeholder' => 'Entrez le nom complet',
]
)

->number('telephone',
'Telephone',
[
'required' => true,
'placeholder' => 'Entrez le numéro de téléphone',
]
)

->email('email',
'Email',
[
'required' => true,
'placeholder' => 'Entrez l\'email',
]
)



->button('Valider', ['id' => 'btn-form-id'])
->render();
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
     <?php $__env->slot('header', null, []); ?> 
        <i class="fas fa-building"></i>
        <span id="card-title">Ajouter</span>
        <a href="#" class="btn btn-sm btn-primary float-end" onClick="resetForm()">Renitialiser</a>
     <?php $__env->endSlot(); ?>
    <?php echo $form; ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2c374f138d53ce79c84b3f047c7b2a22)): ?>
<?php $attributes = $__attributesOriginal2c374f138d53ce79c84b3f047c7b2a22; ?>
<?php unset($__attributesOriginal2c374f138d53ce79c84b3f047c7b2a22); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2c374f138d53ce79c84b3f047c7b2a22)): ?>
<?php $component = $__componentOriginal2c374f138d53ce79c84b3f047c7b2a22; ?>
<?php unset($__componentOriginal2c374f138d53ce79c84b3f047c7b2a22); ?>
<?php endif; ?><?php /**PATH C:\Users\Admin\Downloads\taxe\Modules/User\resources/views/components/gestionnaire/gestionnaire-form.blade.php ENDPATH**/ ?>