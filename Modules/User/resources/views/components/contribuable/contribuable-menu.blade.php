 @php
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

 @endphp


 <div class="dropdown">
     <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
         Navigation
     </a>
     <ul class="dropdown-menu" aria-labelledby="navbarDropdown" style="width:250px;">
         @foreach ($menus as $menu)
         <li style="height:40px;">
             <a href="{{ $menu->route }}" class="dropdown-item d-flex justify-content-between align-items-center {{ $menu->active ? 'active' : '' }}">
                 <span>
                     <i class="{{ $menu->icon }}"></i> {{ $menu->name }}
                 </span>
                 <span class="badge bg-primary rounded-pill">{{ $menu->badge }}</span>
             </a>
         </li>
         @endforeach
     </ul>
 </div>