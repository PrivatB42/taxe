<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="header-icon">
            <i class="fas fa-user-shield"></i>
        </div>
        <div>
            <h3 class="mb-0">
              <i class="fas fa-user-shield"></i>  <?php echo e(config('app.name_back')); ?>

            </h3>
        </div>
    </div>

    <ul class="sidebar-menu">
        <?php $__currentLoopData = x_menu(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="<?php echo e($menu->hasSubMenu() ? 'has-submenu' : ''); ?> <?php echo e($menu->active ? 'menu-active' : ''); ?> ">
            <a href="<?php echo e($menu->hasSubMenu() ? '#' : $menu->route); ?>">
                <i class="<?php echo e($menu->icon); ?>"></i>
                <span class="menu-text"><?php echo e($menu->name); ?></span>
                <?php if($menu->badge !== null): ?>
                <span class="badge bg-danger ms-2"><?php echo e($menu->badge); ?></span>
                <?php endif; ?>
            </a>
            <?php if($menu->hasSubMenu()): ?>
            <ul class="submenu">
                <?php $__currentLoopData = $menu->subMenu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subMenu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li  class="<?php echo e($subMenu->active ? 'active' : ''); ?>">
                    <a href="<?php echo e($subMenu->route); ?>">
                        <i class="<?php echo e($subMenu->icon); ?>"></i>
                        <span class="menu-text"><?php echo e($subMenu->name); ?></span>
                        <?php if($subMenu->badge !== null): ?>
                        <span class="badge bg-danger ms-2"><?php echo e($subMenu->badge); ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <?php endif; ?>
        </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div><?php /**PATH C:\Users\Admin\Downloads\taxe\resources\views/components/base/sidebar.blade.php ENDPATH**/ ?>