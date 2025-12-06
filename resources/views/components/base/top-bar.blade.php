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

     <div class="d-flex align-items-center">
         <div class="notifications">
             <button
                 class="btn"
                 type="button"
                 data-bs-toggle="dropdown"
                 aria-expanded="false">
                 <i class="fas fa-bell"></i>
                 <span class="notification-badge">3</span>
             </button>
             <ul class="dropdown-menu dropdown-menu-end">
                 <li>
                     <h6 class="dropdown-header">Notifications</h6>
                 </li>
                 <li>
                     <a class="dropdown-item" href="#"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Nouvelle demande d'absence</a>
                 </li>
                 <li>
                     <a class="dropdown-item" href="#"><i class="fas fa-user-plus text-info me-2"></i>Nouvel
                         utilisateur inscrit</a>
                 </li>
                 <li>
                     <a class="dropdown-item" href="#"><i class="fas fa-calendar text-primary me-2"></i>Rappel:
                         Réunion dans 1h</a>
                 </li>
                 <li>
                     <hr class="dropdown-divider" />
                 </li>
                 <li>
                     <a class="dropdown-item text-center" href="#">Voir toutes les notifications</a>
                 </li>
             </ul>
         </div>

         <div class="user-menu dropdown">
             <a
                 href="#"
                 class="d-flex align-items-center text-decoration-none dropdown-toggle"
                 id="dropdownUser"
                 data-bs-toggle="dropdown"
                 aria-expanded="false">
                 <img src="" alt="Admin" />
                 <span class="d-none d-md-inline ms-2">Admin</span>
             </a>
             <ul
                 class="dropdown-menu dropdown-menu-end"
                 aria-labelledby="dropdownUser">
                 <li>
                     <a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profil</a>
                 </li>
                 <li>
                     <a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Paramètres</a>
                 </li>
                 <li>
                     <hr class="dropdown-divider" />
                 </li>
                 <li>
                     <a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a>
                 </li>
             </ul>
         </div>
     </div>
 </div>