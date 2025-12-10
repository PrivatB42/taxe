 <?php
 $items = [
 [
 'name' => 'activités',
 'icon' => 'fas fa-list',
 'route' => route('contribuables.show', ['matricule' => $contribuable->matricule, 'action' => 'activites']),
 'condition' => true,
 'active' => url_tab(2) == 'activites',
 'badge' => null
 ],

//  [
//  'name' => 'Constantes',
//  'icon' => 'fas fa-list',
//  'route' => route('contribuables.show', ['matricule' => $contribuable->matricule, 'action' => 'constantes']),
//  'condition' => true,
//  'active' => url_tab(2) == 'constantes',
//  //'badge' => 22
//  ],

 [
 'name' => 'Taxes',
 'icon' => 'fas fa-list',
 'route' => route('contribuables.show', ['matricule' => $contribuable->matricule, 'action' => 'taxes']),
 'condition' => true,
 'active' => url_tab(2) == 'taxes',
 //'badge' => 22
 ],

 ];

 $menus = x_make_menu($items);

 ?>


 <div class="dropdown">
     <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
         Navigation
     </a>
     <ul class="dropdown-menu" aria-labelledby="navbarDropdown" style="width:250px;">
         <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
         <li style="height:40px;">
             <a href="<?php echo e($menu->route); ?>" class="dropdown-item d-flex justify-content-between align-items-center <?php echo e($menu->active ? 'active' : ''); ?>">
                 <span>
                     <i class="<?php echo e($menu->icon); ?>"></i> <?php echo e($menu->name); ?>

                 </span>
                 <span class="badge bg-primary rounded-pill"><?php echo e($menu->badge); ?></span>
             </a>
         </li>
         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
     </ul>
 </div><?php /**PATH C:\Users\Admin\Downloads\taxe\Modules/User\resources/views/components/contribuable/contribuable-menu.blade.php ENDPATH**/ ?>