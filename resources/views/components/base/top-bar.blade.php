<div class="topbar" id="topbar">
    <div class="d-flex align-items-center">
        <button class="toggle-sidebar" id="toggle-sidebar">
            <i class="fas fa-bars"></i>
        </button>
        <button class="toggle-sidebar-mobile" id="toggle-sidebar-mobile">
            <i class="fas fa-bars"></i>
        </button>
        <h4 class="mb-0 ms-3">@yield('pageTitle', '')</h4>
    </div>

    <div class="topbar-search d-none d-md-block">
        <input type="text" class="search-input" placeholder="Rechercher..." />
    </div>

    <div class="d-flex align-items-center gap-3">
        <!-- Admin Badge -->
        @if(session('user.role') === 'admin' || session('user.role') === 'ROLE_ADMIN')
        <span class="badge bg-danger px-3 py-2" style="font-size: 0.75rem; font-weight: 600;">
            Administrateur
        </span>
        @endif

        <!-- Notifications -->
        <div class="notifications position-relative">
            <button
                class="btn btn-link text-decoration-none position-relative"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                style="color: #6c757d; padding: 0.5rem;">
                <i class="fas fa-bell" style="font-size: 1.25rem;"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; padding: 0.25rem 0.4rem;">
                    3
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="min-width: 300px; border: none; border-radius: 12px; padding: 0.5rem;">
                <li>
                    <h6 class="dropdown-header fw-bold" style="color: #1e3c72;">Notifications</h6>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="#" style="border-radius: 8px; margin: 0.25rem 0;">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        <div>
                            <div class="fw-semibold" style="font-size: 0.9rem;">Nouvelle demande</div>
                            <small class="text-muted" style="font-size: 0.75rem;">Il y a 5 minutes</small>
                        </div>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="#" style="border-radius: 8px; margin: 0.25rem 0;">
                        <i class="fas fa-user-plus text-info me-2"></i>
                        <div>
                            <div class="fw-semibold" style="font-size: 0.9rem;">Nouvel utilisateur</div>
                            <small class="text-muted" style="font-size: 0.75rem;">Il y a 1 heure</small>
                        </div>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="#" style="border-radius: 8px; margin: 0.25rem 0;">
                        <i class="fas fa-calendar text-primary me-2"></i>
                        <div>
                            <div class="fw-semibold" style="font-size: 0.9rem;">Rappel: Réunion</div>
                            <small class="text-muted" style="font-size: 0.75rem;">Dans 1 heure</small>
                        </div>
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider my-2" />
                </li>
                <li>
                    <a class="dropdown-item text-center py-2 fw-semibold" href="#" style="color: #667eea; border-radius: 8px;">
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
                aria-expanded="false"
                style="color: #1e3c72; font-weight: 500;">
                <img src="{{ session('user.photo') ?? default_photo() }}" alt="{{ session('user.nom_complet', 'User') }}" 
                     style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #e0e0e0;" />
                <span class="d-none d-md-inline ms-2">{{ session('user.nom_complet', 'Admin Système') }}</span>
                <i class="fas fa-chevron-down ms-2" style="font-size: 0.75rem;"></i>
            </a>
            <ul
                class="dropdown-menu dropdown-menu-end shadow-lg"
                aria-labelledby="dropdownUser"
                style="border: none; border-radius: 12px; padding: 0.5rem; min-width: 200px;">
                <li>
                    <a class="dropdown-item py-2" href="#" style="border-radius: 8px; margin: 0.25rem 0;">
                        <i class="fas fa-user me-2" style="color: #667eea;"></i>Profil
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="#" style="border-radius: 8px; margin: 0.25rem 0;">
                        <i class="fas fa-cog me-2" style="color: #667eea;"></i>Paramètres
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider my-2" />
                </li>
                <li>
                    <a class="dropdown-item py-2 text-danger" href="#" 
                       data-bs-toggle="modal" 
                       data-bs-target="#modal-logout"
                       style="border-radius: 8px; margin: 0.25rem 0;">
                        <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

@if (auth()->check() || session()->has('user'))
    @include('components.ux.modal-logout')
@endif
