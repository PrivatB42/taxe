<!DOCTYPE html>
<html lang="fr">

<head>
    @include('components.base.head')
    @yield('style')
</head>

<body>
    <!-- Loading Screen -->
    @include('components.base.loading')

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    
    <!-- Sidebar -->
    @include('components.base.sidebar')
    

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Topbar fixé -->
        @include('components.base.top-bar')

        <div class="content-padding">

            @include('components.generic.alerte')
            <div id="x-alerts-container" class="mb-4"></div>


            @yield('content')

        </div>
    </div>

    @include('components.base.script')

    @yield('script')
</body>

</html>