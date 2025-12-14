<!-- Bootstrap 5 JS Bundle with Popper -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
<script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- iMask.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/imask/7.6.1/imask.min.js"></script>

<!-- Cleave.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>



<script src="{{ asset('assets/jsFx/js/xjs.js') }}"></script>
<script src="{{ asset('assets/jsFx/js/xnotification.js') }}"></script>
<script src="{{ asset('assets/jsFx/js/xform.js') }}"></script>
<script src="{{ asset('assets/jsFx/js/xui.js') }}"></script>
<script src="{{ asset('assets/jsFx/js/xdate.js') }}"></script>
<script src="{{ asset('assets/js/helpers.js') }}"></script>
<script src="{{ asset('assets/jsFx/js/xstring.js') }}"></script>
<script src="{{ asset('assets/jsFx/js/x_select.js') }}"></script>
<script src="{{ asset('assets/jsFx/js/xmask.js') }}"></script>
<script src="{{ asset('assets/jsFx/js/xclmask.js') }}"></script>
<script src="{{ asset('assets/jsFx/js/xdatePiker.js') }}"></script>
<script src="{{ asset('assets/jsFx/js/x_upload_file.js') }}"></script>

<script src="{{ asset('assets/jsFx/js/xgtfile.js') }}"></script>


<script src="{{ asset('assets/jsFx/js/video_player.js') }}"></script>

<!-- Auth Token Management -->
<script src="{{ asset('js/auth.js') }}"></script>

<script>
    let showLoaderTimeout;
    const loader = document.getElementById("loading-screen");

    function hideLoader() {
        if (!loader) return;
        loader.classList.add("fade-out");
        setTimeout(() => loader.remove(), 500); // correspond à la transition CSS
    }

    // Affiche le loader seulement si le chargement prend >500ms
    showLoaderTimeout = setTimeout(() => {
        if (!document.readyState || document.readyState !== "complete") {
            loader.style.display = "flex";
        }
    }, 500);

    // Quand la page est complètement chargée
    window.addEventListener("load", () => {
        clearTimeout(showLoaderTimeout); // annule l’affichage si non nécessaire
        if (loader.style.display !== "none") {
            hideLoader();
        }
    });



    // Toggle sidebar
    document
        .getElementById("toggle-sidebar")
        .addEventListener("click", function() {
            document.getElementById("sidebar").classList.toggle("collapsed");
            document.getElementById("main-content").classList.toggle("expanded");
            document.getElementById("topbar").classList.toggle("expanded");
        });

    // Toggle sidebar on mobile
    document
        .getElementById("toggle-sidebar-mobile")
        .addEventListener("click", function() {
            document.getElementById("sidebar").classList.toggle("active");
            document.getElementById("sidebar-overlay").classList.toggle("active");
        });

    // Close sidebar when clicking on overlay
    document
        .getElementById("sidebar-overlay")
        .addEventListener("click", function() {
            document.getElementById("sidebar").classList.remove("active");
            this.classList.remove("active");
        });


    document.querySelectorAll(".has-submenu > a").forEach(function(item) {
        item.addEventListener("click", function(e) {
            const sidebar = document.getElementById("sidebar");

            if (!sidebar.classList.contains("collapsed")) {
                e.preventDefault();
                const parent = this.parentElement;
                const isActive = parent.classList.contains("active");

                // Fermer tous les sous-menus d'abord
                document.querySelectorAll(".has-submenu.active").forEach(function(openItem) {
                    openItem.classList.remove("active");
                });

                // Si celui cliqué n'était pas déjà ouvert, on l'ouvre
                if (!isActive) {
                    parent.classList.add("active");
                }
            }
        });
    });




    const text = document.getElementById('{{ config("app.name", "STRATEGIES ET ESPERANCE") }}');
    let angle = 0;

    function animate() {
        angle += 1;
        const xRotation = Math.sin(angle * Math.PI / 180) * 20;
        const yRotation = angle % 360;

        text.style.transform = `rotateX(${xRotation}deg) rotateY(${yRotation}deg)`;

        requestAnimationFrame(animate);
    }

    // animate();

    document.querySelectorAll('input[type=number]').forEach(input => {
        input.addEventListener('keydown', function(e) {
            if (e.key === "ArrowUp" || e.key === "ArrowDown") {
                e.preventDefault();
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('#filtre-btn button');

        buttons.forEach(button => {
            button.addEventListener('click', function() {
                // Retire "active" de tous
                buttons.forEach(btn => btn.classList.remove('active'));
                // Ajoute "active" au bouton cliqué
                this.classList.add('active');
            });
        });

        // Met le bouton "Tous" actif par défaut au chargement
        const defaultBtn = document.querySelector('#btn-reset-filtre');
        if (defaultBtn) defaultBtn.classList.add('active');
    });
</script>

@if (session('success'))
<script>
    x_successNotification('{{ session("success") }}', 'success');
</script>
@endif