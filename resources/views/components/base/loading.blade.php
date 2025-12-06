 @php
 $isOuverture = $isOuverture ?? false;
 @endphp
 
 <div class="loading-screen" id="loading-screen">
     <div class="loading-spinner"></div>
     @if ($isOuverture)
         <div class="loading-text text-3d" id="panacee">{{ $text ?? 'PANNACEE' }}</div>
     @else
         <div class="text-white bottom-0">{{ $text ?? 'Chargement en cours...' }}</div>
     @endif
 </div>