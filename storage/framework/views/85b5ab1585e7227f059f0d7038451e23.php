 <?php
 $isOuverture = $isOuverture ?? false;
 ?>
 
 <div class="loading-screen" id="loading-screen">
     <div class="loading-spinner"></div>
     <?php if($isOuverture): ?>
         <div class="loading-text text-3d" id="panacee"><?php echo e($text ?? 'PANNACEE'); ?></div>
     <?php else: ?>
         <div class="text-white bottom-0"><?php echo e($text ?? 'Chargement en cours...'); ?></div>
     <?php endif; ?>
 </div><?php /**PATH C:\Users\Admin\Downloads\taxe\resources\views/components/base/loading.blade.php ENDPATH**/ ?>