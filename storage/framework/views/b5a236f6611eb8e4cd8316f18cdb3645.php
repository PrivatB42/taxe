<div class="topbar" id="topbar">
    <div class="d-flex align-items-center">
        <button class="toggle-sidebar" id="toggle-sidebar">
            <i class="fas fa-bars"></i>
        </button>
        <button class="toggle-sidebar-mobile" id="toggle-sidebar-mobile">
            <i class="fas fa-bars"></i>
        </button>
        <h4 class="mb-0 ms-3 fw-bold"><?php echo $__env->yieldContent('pageTitle', ''); ?></h4>
    </div>

    <div class="topbar-search d-none d-md-block">
        <input type="text" class="search-input" placeholder="Rechercher..." />
    </div>

    <div class="d-flex align-items-center">
        <!-- Badge du rôle utilisateur -->
        <?php if(auth()->guard()->check()): ?>
        <div class="me-3 d-none d-lg-block">
            <?php
                $typeCompte = Auth::user()->type_compte;
                $typeLabel = \App\Helpers\Constantes::COMPTES_LABELS[$typeCompte] ?? $typeCompte;
                $typeColor = \App\Helpers\Constantes::COMPTES_COLORS[$typeCompte] ?? 'secondary';
            ?>
            <span class="badge bg-<?php echo e($typeColor); ?> px-3 py-2">
                <i class="fas fa-shield-alt me-1"></i>
                <?php echo e($typeLabel); ?>

            </span>
        </div>
        <?php endif; ?>

        <!-- Notifications -->
        <div class="notifications">
            <button
                class="btn"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">3</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="min-width: 320px;">
                <li>
                    <h6 class="dropdown-header fw-bold py-3">
                        <i class="fas fa-bell me-2 text-primary"></i>Notifications
                    </h6>
                </li>
                <li>
                    <a class="dropdown-item py-3" href="#">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                            </div>
                            <div>
                                <span class="d-block fw-semibold">Nouvelle notification</span>
                                <small class="text-muted">Il y a 5 minutes</small>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider" />
                </li>
                <li>
                    <a class="dropdown-item text-center py-3 text-primary fw-semibold" href="#">
                        Voir toutes les notifications
                    </a>
                </li>
            </ul>
        </div>

        <!-- User Menu -->
        <div class="user-menu dropdown">
            <a
                href="#"
                class="d-flex align-items-center text-decoration-none dropdown-toggle"
                id="dropdownUser"
                data-bs-toggle="dropdown"
                aria-expanded="false">
                <?php if(auth()->guard()->check()): ?>
                    <img src="<?php echo e(session('user.photo', default_photo())); ?>" alt="Profil" class="avatar" />
                    <div class="d-none d-md-block ms-2">
                        <span class="fw-semibold"><?php echo e(session('user.nom_complet', Auth::user()->personne->nom_complet ?? 'Utilisateur')); ?></span>
                    </div>
                <?php else: ?>
                    <img src="<?php echo e(default_photo()); ?>" alt="Guest" class="avatar" />
                    <span class="d-none d-md-inline ms-2">Invité</span>
                <?php endif; ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="min-width: 220px;" aria-labelledby="dropdownUser">
                <?php if(auth()->guard()->check()): ?>
                <li class="px-3 py-2 border-bottom">
                    <div class="d-flex align-items-center">
                        <img src="<?php echo e(session('user.photo', default_photo())); ?>" alt="Profil" class="avatar-lg me-3" />
                        <div>
                            <span class="d-block fw-bold"><?php echo e(session('user.nom_complet', 'Utilisateur')); ?></span>
                            <small class="text-muted"><?php echo e(session('user.email', Auth::user()->personne->email ?? '')); ?></small>
                        </div>
                    </div>
                </li>
                <?php endif; ?>
                <li>
                    <a class="dropdown-item py-2" href="#">
                        <i class="fas fa-user me-2 text-primary"></i>Mon Profil
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="#">
                        <i class="fas fa-cog me-2 text-secondary"></i>Paramètres
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider" />
                </li>
                <li>
                    <form action="<?php echo e(route('logout')); ?>" method="POST" class="d-inline w-100">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="dropdown-item py-2 text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Admin\Downloads\taxe\resources\views/components/base/top-bar.blade.php ENDPATH**/ ?>