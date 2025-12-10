<!DOCTYPE html>
<html lang="fr">

<head>
    <?php echo $__env->make('components.base.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->yieldContent('style'); ?>
</head>

<body>
    <!-- Loading Screen -->
    <?php echo $__env->make('components.base.loading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Sidebar -->
    <?php echo $__env->make('components.base.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Topbar fixé -->
        <?php echo $__env->make('components.base.top-bar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="content-padding">

            <?php echo $__env->make('components.generic.alerte', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div id="x-alerts-container" class="mb-4"></div>


            <?php echo $__env->yieldContent('content'); ?>

        </div>
    </div>

    <?php echo $__env->make('components.base.script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->yieldContent('script'); ?>
</body>

</html><?php /**PATH C:\Users\Admin\Downloads\taxe\resources\views/templates/layout.blade.php ENDPATH**/ ?>