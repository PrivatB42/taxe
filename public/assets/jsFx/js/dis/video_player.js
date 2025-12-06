class VideoDropzone {
    constructor(options = {}) {
        this.containerId = options.containerId;
        this.lienVideo = options.lienVideo || null;
        this.lienForm = options.lienForm || null;
        this.thumbnail = options.thumbnail || null;
        this.mode = options.mode || "lecture";
        this.titre = options.titre || "Vidéo sans titre";
        this.selectedFile = null;
        this.isVideoLoaded = false;
        this.videoJsInstance = null;
        this.useVideoJs = options.useVideoJs !== false;
        this.autoplay = options.autoplay || false;
        this.muted = options.muted || false;
        this.fluid = options.fluid !== false;
        this.playbackRates = options.playbackRates || [0.25, 0.5, 0.75, 1, 1.25, 1.5, 1.75, 2];
        this.theme = options.theme || "primary";
        this.videoInstanceId = null;

        this.hasVideoJs = typeof window.videojs !== "undefined";
        this.init();
    }

    init() {
        this.container = document.getElementById(this.containerId);
        if (!this.container) {
            console.error(
                `Container avec l'id ${this.containerId} introuvable`
            );
            return;
        }
        this.render();
        this.bindEvents();
    }

    cleanupVideoJsInstance() {
        if (this.videoJsInstance) {
            try {
                if (
                    this.videoJsInstance.isDisposed &&
                    !this.videoJsInstance.isDisposed()
                ) {
                    this.videoJsInstance.dispose();
                }
            } catch (error) {
                console.warn("Erreur lors du nettoyage de Video.js:", error);
            } finally {
                this.videoJsInstance = null;
                this.videoInstanceId = null;
            }
        }
    }

    generateVideoId() {
        const timestamp = Date.now();
        const random = Math.floor(Math.random() * 1000);
        this.videoInstanceId = `video-player-${this.containerId}-${timestamp}-${random}`;
        return this.videoInstanceId;
    }

    render() {
        this.cleanupVideoJsInstance();
        const dropzoneContent = this.lienVideo
            ? this.renderVideoPlayer()
            : this.renderEmptyDropzone();

        this.container.innerHTML = `
                    <div class="video-dropzone position-relative">
                        ${dropzoneContent}
                        ${this.mode === "edit" ? this.renderEditControls() : ""}
                        ${this.mode === "edit" ? this.renderSubmitButton() : ""}
                        <input type="file" id="videoInput-${
                            this.containerId
                        }" class="d-none" accept="video/*">
                    </div>
                `;
    }

    renderVideoPlayer() {
        const videoId = this.generateVideoId();

        if (this.thumbnail) {
            return `
                        <div class="video-container position-relative bg-dark rounded overflow-hidden" style="aspect-ratio: 16/9;">
                            <img src="${
                                this.thumbnail
                            }" class="w-100 h-100 object-fit-cover video-thumbnail" alt="Thumbnail">
                            
                            <div class="position-absolute bottom-0 start-0 end-0 bg-gradient-to-t from-black to-transparent p-3 video-title-overlay">
                                <h5 class="text-white mb-0 video-title">${
                                    this.titre
                                }</h5>
                            </div>
                            
                            <div class="position-absolute top-50 start-50 translate-middle play-button-container">
                                <button class="btn btn-${
                                    this.theme
                                } btn-lg rounded-circle play-btn shadow-lg" 
                                        style="width: 80px; height: 80px; transition: all 0.3s ease;">
                                    <i class="fas fa-play fs-1"></i>
                                </button>
                            </div>
                            
                            ${this.renderVideoElement(videoId)}
                        </div>
                    `;
        } else {
            return `
                        <div class="video-container position-relative bg-${
                            this.theme
                        } bg-gradient rounded overflow-hidden" style="aspect-ratio: 16/9;">
                            <div class="d-flex flex-column align-items-center justify-content-center h-100 text-white p-4 video-placeholder">
                                <i class="fas fa-video fs-1 mb-3 opacity-75"></i>
                                <h4 class="text-center mb-3 video-title">${
                                    this.titre
                                }</h4>
                                <button class="btn btn-light btn-lg rounded-circle play-btn shadow-lg" 
                                        style="width: 80px; height: 80px; transition: all 0.3s ease;">
                                    <i class="fas fa-play fs-1 text-${
                                        this.theme
                                    }"></i>
                                </button>
                            </div>
                            ${this.renderVideoElement(videoId)}
                        </div>
                    `;
        }
    }

    renderVideoElement(videoId) {
        // Configuration complète pour Video.js avec tous les contrôles
        const videoJsConfig = {
            fluid: this.fluid,
            responsive: true,
            playbackRates: this.playbackRates,
            controls: true,
            preload: "metadata",
            muted: this.muted,
            autoplay: this.autoplay,
            plugins: {},
            // Configuration complète des contrôles
            controlBar: {
                playToggle: true,
                volumePanel: {
                    inline: false
                },
                currentTimeDisplay: true,
                timeDivider: true,
                durationDisplay: true,
                progressControl: {
                    seekBar: true
                },
                playbackRateMenuButton: {
                    playbackRates: this.playbackRates
                },
                fullscreenToggle: true,
                remainingTimeDisplay: false,
                customControlSpacer: true,
                pictureInPictureToggle: true
            },
            // Options de qualité si disponible
            html5: {
                vhs: {
                    overrideNative: true
                }
            }
        };

        const videoClasses = this.useVideoJs && this.hasVideoJs
            ? "video-js vjs-default-skin w-100 h-100"
            : "w-100 h-100";

        const videoAttributes = this.useVideoJs && this.hasVideoJs
            ? `data-setup='${JSON.stringify(videoJsConfig)}'`
            : "controls";

        return `
                    <video 
                        id="${videoId}"
                        class="${videoClasses} object-fit-cover position-absolute top-0 start-0 d-none video-player" 
                        ${videoAttributes}
                        preload="metadata"
                        ${this.muted ? "muted" : ""}
                        ${this.thumbnail ? `poster="${this.thumbnail}"` : ""}
                        style="background-color: #000;"
                    >
                        <source src="${this.lienVideo}" type="video/mp4">
                        <source src="${this.lienVideo}" type="video/webm">
                        <source src="${this.lienVideo}" type="video/ogg">
                        <p>Votre navigateur ne supporte pas la lecture de vidéos HTML5.</p>
                    </video>
                `;
    }

    renderEmptyDropzone() {
        return `
                    <div class="dropzone-empty border border-2 border-dashed border-${this.theme} rounded bg-light d-flex flex-column align-items-center justify-content-center p-5 transition-all" 
                         style="min-height: 300px; aspect-ratio: 16/9; transition: all 0.3s ease;">
                        <i class="fas fa-cloud-upload-alt fs-1 text-${this.theme} mb-3"></i>
                        <h5 class="text-${this.theme} mb-2">Déposer votre vidéo ici</h5>
                        <p class="text-muted text-center mb-3">
                            Glissez-déposez votre fichier vidéo ou cliquez pour sélectionner<br>
                            <small>Formats acceptés: MP4, WebM, AVI, MOV</small>
                        </p>
                        <button class="btn btn-${this.theme} shadow-sm">
                            <i class="fas fa-folder-open me-2"></i>
                            Choisir une vidéo
                        </button>
                    </div>
                `;
    }

    renderEditControls() {
        return `
                    <div class="position-absolute top-0 end-0 m-2" style="z-index: 20;">
                        <button class="btn btn-${this.theme} rounded-circle edit-btn shadow-sm me-2" 
                                style="width: 50px; height: 50px;" title="Changer la vidéo">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-${this.theme} rounded-circle fullscreen-btn shadow-sm" 
                                style="width: 50px; height: 50px;" title="Plein écran">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                `;
    }

    renderSubmitButton() {
        return `
                    <div class="text-center mt-3">
                        <button class="btn btn-${this.theme} btn-lg submit-btn shadow-sm px-4">
                            <i class="fas fa-upload me-2"></i>
                            Envoyer la vidéo
                        </button>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Taille maximale: 100MB
                            </small>
                        </div>
                    </div>
                `;
    }

    bindEvents() {
        const container = this.container;
        const playBtn = container.querySelector(".play-btn");
        if (playBtn) {
            playBtn.addEventListener("click", () => this.playVideo());
        }

        if (this.mode === "edit") {
            const editBtn = container.querySelector(".edit-btn");
            const submitBtn = container.querySelector(".submit-btn");
            const fullscreenBtn = container.querySelector(".fullscreen-btn");
            const videoInput = container.querySelector(
                `#videoInput-${this.containerId}`
            );

            if (editBtn)
                editBtn.addEventListener("click", () => this.openFileDialog());
            if (submitBtn)
                submitBtn.addEventListener("click", () => this.submitVideo());
            if (fullscreenBtn)
                fullscreenBtn.addEventListener("click", () =>
                    this.toggleFullscreen()
                );
            if (videoInput)
                videoInput.addEventListener("change", (e) =>
                    this.handleFileSelection(e)
                );
        }

        const emptyDropzone = container.querySelector(".dropzone-empty");
        if (emptyDropzone) {
            emptyDropzone.addEventListener("click", () =>
                this.openFileDialog()
            );
            emptyDropzone.addEventListener("dragover", (e) =>
                this.handleDragOver(e)
            );
            emptyDropzone.addEventListener("drop", (e) => this.handleDrop(e));
            emptyDropzone.addEventListener("dragleave", (e) =>
                this.handleDragLeave(e)
            );
        }
    }

    async playVideo() {
        const videoPlayer = this.container.querySelector(".video-player");
        const thumbnail = this.container.querySelector(".video-thumbnail");
        const playBtn = this.container.querySelector(".play-btn");
        const placeholder = this.container.querySelector(".video-placeholder");
        const titleOverlay = this.container.querySelector(
            ".video-title-overlay"
        );

        if (!videoPlayer) return;

        if (playBtn) {
            playBtn.innerHTML =
                '<div class="spinner-border text-white" role="status"></div>';
        }

        try {
            // Masquer les éléments de l'overlay avant d'initialiser le lecteur
            if (thumbnail) thumbnail.classList.add("d-none");
            if (placeholder) placeholder.classList.add("d-none");
            if (playBtn) playBtn.classList.add("d-none");
            if (titleOverlay) titleOverlay.classList.add("d-none");

            videoPlayer.classList.remove("d-none");

            if (this.useVideoJs && this.hasVideoJs && !this.videoJsInstance) {
                await this.initializeVideoJs();
            } else if (!this.videoJsInstance) {
                this.initializeNativePlayer();
            }

            // Démarrer la lecture
            if (this.videoJsInstance) {
                // S'assurer que Video.js est prêt avant de jouer
                this.videoJsInstance.ready(() => {
                    this.videoJsInstance.play().catch(console.error);
                });
            } else {
                videoPlayer.play().catch(console.error);
            }
        } catch (error) {
            console.error("Erreur lors du démarrage de la vidéo:", error);
            this.showAlert("Erreur lors du chargement de la vidéo", "danger");
            if (playBtn) {
                playBtn.innerHTML = '<i class="fas fa-play fs-1"></i>';
                playBtn.classList.remove("d-none");
            }
        }
    }

    async initializeVideoJs() {
        const videoElement = this.container.querySelector(".video-player");
        if (!videoElement) throw new Error("Élément vidéo introuvable");

        return new Promise((resolve, reject) => {
            try {
                // Attendre que Video.js soit complètement chargé
                if (typeof window.videojs === 'undefined') {
                    throw new Error('Video.js n\'est pas chargé');
                }

                // Configuration complète de Video.js
                const vjsConfig = {
                    fluid: this.fluid,
                    responsive: true,
                    playbackRates: this.playbackRates,
                    controls: true,
                    preload: "metadata",
                    muted: this.muted,
                    autoplay: false, // Ne pas autoplay, on va le faire manuellement
                    liveui: false,
                    // Configuration des contrôles
                    controlBar: {
                        playToggle: true,
                        volumePanel: {
                            inline: false
                        },
                        currentTimeDisplay: true,
                        timeDivider: true,
                        durationDisplay: true,
                        progressControl: {
                            seekBar: {
                                mouseTimeDisplay: true
                            }
                        },
                        playbackRateMenuButton: {
                            playbackRates: this.playbackRates
                        },
                        fullscreenToggle: true,
                        remainingTimeDisplay: false,
                        customControlSpacer: true,
                        pictureInPictureToggle: true
                    },
                    // Améliorer la compatibilité
                    techOrder: ['html5'],
                    html5: {
                        vhs: {
                            overrideNative: !window.videojs.browser.IS_SAFARI
                        },
                        nativeVideoTracks: false,
                        nativeAudioTracks: false,
                        nativeTextTracks: false
                    }
                };

                this.videoJsInstance = window.videojs(videoElement, vjsConfig);

                this.videoJsInstance.ready(() => {
                    console.log('Video.js est prêt');
                    
                    // Ajouter les événements
                    this.videoJsInstance.on("play", () => this.onVideoPlay());
                    this.videoJsInstance.on("pause", () => this.onVideoPause());
                    this.videoJsInstance.on("ended", () => this.onVideoEnded());
                    this.videoJsInstance.on("loadeddata", () => {
                        this.isVideoLoaded = true;
                        console.log('Données vidéo chargées');
                    });
                    this.videoJsInstance.on("error", (error) => {
                        console.error('Erreur Video.js:', error);
                        this.onVideoError(error);
                    });

                    resolve();
                });

                // Timeout de sécurité
                setTimeout(() => {
                    if (!this.isVideoLoaded) {
                        console.warn('Timeout lors du chargement de la vidéo');
                        resolve();
                    }
                }, 5000);

            } catch (error) {
                console.error('Erreur lors de l\'initialisation de Video.js:', error);
                reject(error);
            }
        });
    }

    initializeNativePlayer() {
        const videoElement = this.container.querySelector(".video-player");
        if (!videoElement) return;

        videoElement.addEventListener("play", () => this.onVideoPlay());
        videoElement.addEventListener("pause", () => this.onVideoPause());
        videoElement.addEventListener("ended", () => this.onVideoEnded());
        videoElement.addEventListener("loadeddata", () => {
            this.isVideoLoaded = true;
        });
        videoElement.addEventListener("error", (error) => this.onVideoError(error));
    }

    onVideoPlay() {
        const titleOverlay = this.container.querySelector(
            ".video-title-overlay"
        );
        if (titleOverlay) {
            titleOverlay.style.opacity = "0";
            titleOverlay.style.transition = "opacity 0.5s ease";
        }
    }

    onVideoPause() {
        const titleOverlay = this.container.querySelector(
            ".video-title-overlay"
        );
        if (titleOverlay) titleOverlay.style.opacity = "1";
    }

    onVideoEnded() {
        this.onVideoPause();
    }

    onVideoError(error) {
        console.error('Erreur vidéo:', error);
        this.showAlert("Erreur lors de la lecture de la vidéo", "danger");
        
        // Réafficher les contrôles de base
        const playBtn = this.container.querySelector(".play-btn");
        const thumbnail = this.container.querySelector(".video-thumbnail");
        const placeholder = this.container.querySelector(".video-placeholder");
        
        if (playBtn) {
            playBtn.innerHTML = '<i class="fas fa-play fs-1"></i>';
            playBtn.classList.remove("d-none");
        }
        if (thumbnail) thumbnail.classList.remove("d-none");
        if (placeholder) placeholder.classList.remove("d-none");
        
        const videoPlayer = this.container.querySelector(".video-player");
        if (videoPlayer) videoPlayer.classList.add("d-none");
    }

    toggleFullscreen() {
        const videoContainer = this.container.querySelector(".video-container");
        if (!document.fullscreenElement) {
            videoContainer.requestFullscreen().catch(console.error);
        } else {
            document.exitFullscreen();
        }
    }

    openFileDialog() {
        const videoInput = this.container.querySelector(
            `#videoInput-${this.containerId}`
        );
        if (videoInput) videoInput.click();
    }

    handleFileSelection(event) {
        const file = event.target.files[0];
        if (file && this.isValidVideoFile(file)) {
            this.selectedFile = file;
            this.previewVideo(file);
        } else {
            this.showAlert(
                "Veuillez sélectionner un fichier vidéo valide.",
                "danger"
            );
        }
    }

    isValidVideoFile(file) {
        const validTypes = [
            "video/mp4",
            "video/webm",
            "video/avi",
            "video/mov",
            "video/quicktime",
            "video/ogg"
        ];
        return validTypes.includes(file.type);
    }

    previewVideo(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            // Nettoyer l'instance précédente
            this.cleanupVideoJsInstance();
            
            // Mettre à jour les données
            this.lienVideo = e.target.result;
            this.titre = file.name.split(".")[0].replace(/[_-]/g, " ");
            this.isVideoLoaded = false;
            this.thumbnail = null; // Pas de thumbnail pour les fichiers locaux
            
            // Re-rendre et rebinder
            this.render();
            this.bindEvents();
            
            console.log('Vidéo prévisualisée:', this.titre);
        };
        reader.onerror = (error) => {
            console.error('Erreur lors de la lecture du fichier:', error);
            this.showAlert("Erreur lors de la lecture du fichier", "danger");
        };
        reader.readAsDataURL(file);
    }

    handleDragOver(e) {
        e.preventDefault();
        e.stopPropagation();
        const dropzone = e.target.closest(".dropzone-empty");
        if (dropzone) {
            dropzone.style.transform = "scale(1.02)";
            dropzone.classList.add('border-success');
        }
    }

    handleDragLeave(e) {
        e.preventDefault();
        e.stopPropagation();
        const dropzone = e.target.closest(".dropzone-empty");
        if (dropzone) {
            dropzone.style.transform = "scale(1)";
            dropzone.classList.remove('border-success');
        }
    }

    handleDrop(e) {
        e.preventDefault();
        e.stopPropagation();

        const dropzone = e.target.closest(".dropzone-empty");
        if (dropzone) {
            dropzone.style.transform = "scale(1)";
            dropzone.classList.remove('border-success');
        }

        const files = e.dataTransfer.files;
        if (files.length > 0 && this.isValidVideoFile(files[0])) {
            this.selectedFile = files[0];
            this.previewVideo(files[0]);
        } else {
            this.showAlert(
                "Veuillez déposer un fichier vidéo valide.",
                "danger"
            );
        }
    }

    async submitVideo() {
        if (!this.selectedFile && !this.lienVideo) {
            this.showAlert("Aucune vidéo sélectionnée.", "warning");
            return;
        }

        const submitBtn = this.container.querySelector(".submit-btn");
        const originalContent = submitBtn.innerHTML;

        submitBtn.innerHTML =
            '<div class="spinner-border spinner-border-sm me-2"></div>Simulation envoi...';
        submitBtn.disabled = true;

        // Simulation d'envoi
        setTimeout(() => {
            this.showAlert(
                "Simulation d'envoi réussie ! (Aucun serveur configuré)",
                "success"
            );
            submitBtn.innerHTML = originalContent;
            submitBtn.disabled = false;
        }, 2000);
    }

    showAlert(message, type = "info", duration = 5000) {
        const alertDiv = document.createElement("div");
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow-lg`;
        alertDiv.style.zIndex = "9999";
        alertDiv.style.minWidth = "300px";
        alertDiv.innerHTML = `
                    <i class="fas fa-${this.getAlertIcon(type)} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
        document.body.appendChild(alertDiv);

        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.classList.remove("show");
                setTimeout(() => alertDiv.remove(), 150);
            }
        }, duration);
    }

    getAlertIcon(type) {
        const icons = {
            success: "check-circle",
            danger: "exclamation-triangle",
            warning: "exclamation-circle",
            info: "info-circle",
        };
        return icons[type] || "info-circle";
    }

    updateVideo(lienVideo, titre = null, thumbnail = null) {
        this.cleanupVideoJsInstance();
        this.lienVideo = lienVideo;
        if (titre) this.titre = titre;
        if (thumbnail !== null) this.thumbnail = thumbnail;
        this.isVideoLoaded = false;
        this.render();
        this.bindEvents();
    }

    setMode(mode) {
        this.mode = mode;
        this.render();
        this.bindEvents();
    }

    setTheme(theme) {
        this.theme = theme;
        this.render();
        this.bindEvents();
    }

    getSelectedFile() {
        return this.selectedFile;
    }

    // Méthodes utilitaires pour contrôler la vidéo
    play() {
        if (this.videoJsInstance) {
            return this.videoJsInstance.play();
        } else {
            const video = this.container.querySelector('.video-player');
            return video ? video.play() : null;
        }
    }

    pause() {
        if (this.videoJsInstance) {
            this.videoJsInstance.pause();
        } else {
            const video = this.container.querySelector('.video-player');
            if (video) video.pause();
        }
    }

    getCurrentTime() {
        if (this.videoJsInstance) {
            return this.videoJsInstance.currentTime();
        } else {
            const video = this.container.querySelector('.video-player');
            return video ? video.currentTime : 0;
        }
    }

    setCurrentTime(time) {
        if (this.videoJsInstance) {
            this.videoJsInstance.currentTime(time);
        } else {
            const video = this.container.querySelector('.video-player');
            if (video) video.currentTime = time;
        }
    }

    getDuration() {
        if (this.videoJsInstance) {
            return this.videoJsInstance.duration();
        } else {
            const video = this.container.querySelector('.video-player');
            return video ? video.duration : 0;
        }
    }

    getVolume() {
        if (this.videoJsInstance) {
            return this.videoJsInstance.volume();
        } else {
            const video = this.container.querySelector('.video-player');
            return video ? video.volume : 0;
        }
    }

    setVolume(volume) {
        if (this.videoJsInstance) {
            this.videoJsInstance.volume(Math.max(0, Math.min(1, volume)));
        } else {
            const video = this.container.querySelector('.video-player');
            if (video) video.volume = Math.max(0, Math.min(1, volume));
        }
    }

    dispose() {
        this.cleanupVideoJsInstance();
    }
}

// Fonction d'initialisation
function createVideoDropzone(options) {
    return new VideoDropzone(options);
}

// Exemples d'utilisation corrigés:
/*
// Utilisation avec Video.js (recommandée)
const videoPlayer1 = createVideoDropzone({
    containerId: 'video-container-1',
    lienVideo: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
    mode: 'edit',
    titre: 'Ma super vidéo',
    theme: 'primary',
    useVideoJs: true,
    playbackRates: [0.25, 0.5, 0.75, 1, 1.25, 1.5, 1.75, 2],
    fluid: true
});

// Utilisation en mode lecture native
const videoPlayer2 = createVideoDropzone({
    containerId: 'video-container-2',
    lienVideo: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4',
    mode: 'lecture',
    titre: 'Vidéo de démonstration',
    theme: 'success',
    useVideoJs: false // Utilise le lecteur HTML5 natif
});

// Pour upload seulement
const videoUploader = createVideoDropzone({
    containerId: 'upload-container',
    mode: 'edit',
    theme: 'info',
    titre: 'Nouvelle vidéo',
    useVideoJs: true
});

// Test des méthodes de contrôle après quelques secondes
setTimeout(() => {
    console.log('Durée:', videoPlayer1.getDuration());
    console.log('Temps actuel:', videoPlayer1.getCurrentTime());
    console.log('Volume:', videoPlayer1.getVolume());
    
    // Exemples de contrôle
    // videoPlayer1.setVolume(0.5);
    // videoPlayer1.setCurrentTime(30);
}, 3000);
*/


class VideoDropzoneEnhanced extends VideoDropzone {
    constructor(options = {}) {
        super(options);
        
        // Nouvelles options
        this.maxFileSize = options.maxFileSize || 300 * 1024 * 1024; // 300MB par défaut
        this.supportedFormats = options.supportedFormats || ['mp4', 'webm', 'avi', 'mov'];
        this.enableProgress = options.enableProgress !== false;
        this.enableSubtitles = options.enableSubtitles || false;
        this.subtitles = options.subtitles || [];
        this.enableKeyboard = options.enableKeyboard !== false;
        this.customCSS = options.customCSS || {};
        this.onVideoLoad = options.onVideoLoad || null;
        this.onVideoError = options.onVideoError || null;
        this.onUploadProgress = options.onUploadProgress || null;
        this.quality = options.quality || 'auto';
        this.enableAnalytics = options.enableAnalytics || false;
        
        // État interne amélioré
        this.uploadProgress = 0;
        this.isUploading = false;
        this.analytics = {
            playCount: 0,
            totalWatchTime: 0,
            lastPosition: 0
        };
        
        this.initEnhancements();
    }
    
    initEnhancements() {
        // Ajouter les styles personnalisés
        this.addCustomStyles();
        
        // Initialiser les raccourcis clavier
        if (this.enableKeyboard) {
            this.initKeyboardControls();
        }
        
        // Initialiser l'analytics
        if (this.enableAnalytics) {
            this.initAnalytics();
        }
    }
    
    addCustomStyles() {
        if (Object.keys(this.customCSS).length === 0) return;
        
        const styleId = `video-dropzone-custom-${this.containerId}`;
        let styleEl = document.getElementById(styleId);
        
        if (!styleEl) {
            styleEl = document.createElement('style');
            styleEl.id = styleId;
            document.head.appendChild(styleEl);
        }
        
        let cssText = '';
        for (const [selector, styles] of Object.entries(this.customCSS)) {
            cssText += `#${this.containerId} ${selector} {`;
            for (const [property, value] of Object.entries(styles)) {
                cssText += `${property}: ${value};`;
            }
            cssText += '}';
        }
        
        styleEl.textContent = cssText;
    }
    
    initKeyboardControls() {
        document.addEventListener('keydown', (e) => {
            if (!this.isVideoActive()) return;
            
            switch(e.code) {
                case 'Space':
                    e.preventDefault();
                    this.togglePlayPause();
                    break;
                case 'ArrowLeft':
                    e.preventDefault();
                    this.seekRelative(-10);
                    break;
                case 'ArrowRight':
                    e.preventDefault();
                    this.seekRelative(10);
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    this.adjustVolume(0.1);
                    break;
                case 'ArrowDown':
                    e.preventDefault();
                    this.adjustVolume(-0.1);
                    break;
                case 'KeyF':
                    e.preventDefault();
                    this.toggleFullscreen();
                    break;
                case 'KeyM':
                    e.preventDefault();
                    this.toggleMute();
                    break;
            }
        });
    }
    
    initAnalytics() {
        // Charger les analytics depuis le stockage local
        const saved = localStorage.getItem(`video-analytics-${this.containerId}`);
        if (saved) {
            this.analytics = { ...this.analytics, ...JSON.parse(saved) };
        }
        
        // Sauvegarder périodiquement
        setInterval(() => this.saveAnalytics(), 10000);
    }
    
    isVideoActive() {
        const video = this.container.querySelector('.video-player');
        return video && !video.classList.contains('d-none');
    }
    
    togglePlayPause() {
        if (this.videoJsInstance) {
            if (this.videoJsInstance.paused()) {
                this.videoJsInstance.play();
            } else {
                this.videoJsInstance.pause();
            }
        } else {
            const video = this.container.querySelector('.video-player');
            if (video) {
                if (video.paused) {
                    video.play();
                } else {
                    video.pause();
                }
            }
        }
    }
    
    seekRelative(seconds) {
        if (this.videoJsInstance) {
            const currentTime = this.videoJsInstance.currentTime();
            this.videoJsInstance.currentTime(Math.max(0, currentTime + seconds));
        } else {
            const video = this.container.querySelector('.video-player');
            if (video) {
                video.currentTime = Math.max(0, video.currentTime + seconds);
            }
        }
    }
    
    adjustVolume(delta) {
        if (this.videoJsInstance) {
            const volume = Math.max(0, Math.min(1, this.videoJsInstance.volume() + delta));
            this.videoJsInstance.volume(volume);
            this.showVolumeIndicator(volume);
        } else {
            const video = this.container.querySelector('.video-player');
            if (video) {
                video.volume = Math.max(0, Math.min(1, video.volume + delta));
                this.showVolumeIndicator(video.volume);
            }
        }
    }
    
    toggleMute() {
        if (this.videoJsInstance) {
            this.videoJsInstance.muted(!this.videoJsInstance.muted());
        } else {
            const video = this.container.querySelector('.video-player');
            if (video) {
                video.muted = !video.muted;
            }
        }
    }
    
    showVolumeIndicator(volume) {
        const indicator = document.createElement('div');
        indicator.className = 'position-fixed top-50 start-50 translate-middle bg-dark bg-opacity-75 text-white p-3 rounded';
        indicator.style.zIndex = '9999';
        indicator.innerHTML = `
            <i class="fas fa-volume-${volume === 0 ? 'mute' : volume < 0.5 ? 'down' : 'up'} me-2"></i>
            Volume: ${Math.round(volume * 100)}%
        `;
        
        document.body.appendChild(indicator);
        
        setTimeout(() => {
            indicator.style.opacity = '0';
            indicator.style.transition = 'opacity 0.3s ease';
            setTimeout(() => indicator.remove(), 300);
        }, 1500);
    }
    
    // Override de la validation des fichiers avec taille max
    isValidVideoFile(file) {
        // Vérifier le type
        const extension = file.name.split('.').pop().toLowerCase();
        if (!this.supportedFormats.includes(extension)) {
            this.showAlert(`Format non supporté. Formats acceptés: ${this.supportedFormats.join(', ').toUpperCase()}`, 'danger');
            return false;
        }
        
        // Vérifier la taille
        if (file.size > this.maxFileSize) {
            const maxSizeMB = Math.round(this.maxFileSize / (1024 * 1024));
            const fileSizeMB = Math.round(file.size / (1024 * 1024));
            this.showAlert(`Fichier trop volumineux (${fileSizeMB}MB). Taille maximum: ${maxSizeMB}MB`, 'danger');
            return false;
        }
        
        return true;
    }
    
    // Override du rendu avec barre de progression
    renderSubmitButton() {
        const progressBar = this.enableProgress ? `
            <div class="progress mt-2 ${this.isUploading ? '' : 'd-none'}" style="height: 6px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-${this.theme}" 
                     style="width: ${this.uploadProgress}%"></div>
            </div>
        ` : '';
        
        return `
            <div class="text-center mt-3">
                <button class="btn btn-${this.theme} btn-lg submit-btn shadow-sm px-4" 
                        ${this.isUploading ? 'disabled' : ''}>
                    <i class="fas fa-upload me-2"></i>
                    ${this.isUploading ? 'Envoi en cours...' : 'Envoyer la vidéo'}
                </button>
                ${progressBar}
                <div class="mt-2">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Taille maximale: 300 MB
                    </small>
                </div>
            </div>
        `;
    }
    
    // Méthode d'upload améliorée avec progression
    async submitVideo() {
        if (!this.selectedFile && !this.lienVideo) {
            this.showAlert('Aucune vidéo sélectionnée.', 'warning');
            return;
        }
        
        if (!this.lienForm) {
            this.showAlert('URL de soumission non définie.', 'danger');
            return;
        }
        
        this.isUploading = true;
        this.uploadProgress = 0;
        this.render();
        this.bindEvents();
        
        try {
            const formData = new FormData();
            if (this.selectedFile) {
                formData.append('video', this.selectedFile);
            }
            formData.append('titre', this.titre);
            formData.append('thumbnail', this.thumbnail || '');
            formData.append('quality', this.quality);
            
            const response = await fetch(this.lienForm, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                },
                // Simuler progression
                onUploadProgress: (progressEvent) => {
                    if (progressEvent.lengthComputable) {
                        this.uploadProgress = Math.round((progressEvent.loaded / progressEvent.total) * 100);
                        this.updateProgressBar();
                        
                        if (this.onUploadProgress) {
                            this.onUploadProgress(this.uploadProgress, progressEvent);
                        }
                    }
                }
            });
            
            if (response.ok) {
                this.uploadProgress = 100;
                this.updateProgressBar();
                
                const result = await response.json();
                this.showAlert('Vidéo envoyée avec succès !', 'success');
                
                if (result.videoUrl) {
                    this.updateVideo(result.videoUrl, result.titre);
                }
                
                // Analytics
                if (this.enableAnalytics) {
                    this.analytics.uploadsCount = (this.analytics.uploadsCount || 0) + 1;
                    this.saveAnalytics();
                }
                
            } else {
                throw new Error('Erreur serveur');
            }
            
        } catch (error) {
            this.showAlert(`Erreur lors de l'envoi: ${error.message}`, 'danger');
        } finally {
            this.isUploading = false;
            this.uploadProgress = 0;
            setTimeout(() => {
                this.render();
                this.bindEvents();
            }, 2000);
        }
    }
    
    updateProgressBar() {
        const progressBar = this.container.querySelector('.progress-bar');
        if (progressBar) {
            progressBar.style.width = `${this.uploadProgress}%`;
        }
    }
    
    // Méthodes analytics
    onVideoPlay() {
        super.onVideoPlay();
        
        if (this.enableAnalytics) {
            this.analytics.playCount++;
            this.analytics.sessionStartTime = Date.now();
        }
    }
    
    onVideoPause() {
        super.onVideoPause();
        
        if (this.enableAnalytics && this.analytics.sessionStartTime) {
            const sessionTime = Date.now() - this.analytics.sessionStartTime;
            this.analytics.totalWatchTime += sessionTime;
            this.analytics.lastPosition = this.getCurrentTime();
        }
    }
    
    saveAnalytics() {
        if (this.enableAnalytics) {
            localStorage.setItem(`video-analytics-${this.containerId}`, JSON.stringify(this.analytics));
        }
    }
    
    // Méthodes publiques améliorées
    getAnalytics() {
        return { ...this.analytics };
    }
    
    setQuality(quality) {
        this.quality = quality;
        
        if (this.videoJsInstance) {
            // Implémentation de changement de qualité pour Video.js
            // Nécessite un plugin de qualité comme videojs-contrib-quality-levels
        }
    }
    
    addSubtitles(subtitles) {
        this.subtitles = [...this.subtitles, ...subtitles];
        
        if (this.videoJsInstance) {
            // Ajouter les sous-titres à Video.js
            subtitles.forEach(subtitle => {
                this.videoJsInstance.addRemoteTextTrack({
                    kind: 'subtitles',
                    src: subtitle.src,
                    srclang: subtitle.lang,
                    label: subtitle.label
                });
            });
        }
    }
    
    // Méthode pour capturer des screenshots
    captureScreenshot() {
        const video = this.container.querySelector('.video-player');
        if (!video) return null;
        
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0);
        
        return canvas.toDataURL('image/png');
    }
    
    // Méthode pour obtenir des informations détaillées sur la vidéo
    getVideoInfo() {
        const video = this.container.querySelector('.video-player');
        if (!video) return null;
        
        return {
            duration: this.getDuration(),
            currentTime: this.getCurrentTime(),
            videoWidth: video.videoWidth,
            videoHeight: video.videoHeight,
            volume: this.videoJsInstance ? this.videoJsInstance.volume() : video.volume,
            muted: this.videoJsInstance ? this.videoJsInstance.muted() : video.muted,
            paused: this.videoJsInstance ? this.videoJsInstance.paused() : video.paused,
            readyState: video.readyState,
            networkState: video.networkState
        };
    }
    
    // Méthode de nettoyage améliorée
    dispose() {
        super.dispose();
        
        // Sauvegarder les analytics une dernière fois
        if (this.enableAnalytics) {
            this.saveAnalytics();
        }
        
        // Supprimer les styles personnalisés
        const styleEl = document.getElementById(`video-dropzone-custom-${this.containerId}`);
        if (styleEl) {
            styleEl.remove();
        }
        
        // Nettoyer les événements clavier si nécessaire
        // (Dans une vraie implémentation, il faudrait stocker les listeners pour les supprimer)
    }
}

// Fonction d'initialisation améliorée
function createEnhancedVideoDropzone(options) {
    return new VideoDropzoneEnhanced(options);
}

// Exemple d'utilisation avancée
/*
const advancedPlayer = createEnhancedVideoDropzone({
    containerId: 'advanced-video-player',
    lienVideo: 'https://example.com/video.mp4',
    lienForm: '/api/upload-video',
    mode: 'edit',
    theme: 'primary',
    useVideoJs: true,
    
    // Nouvelles options
    maxFileSize: 200 * 1024 * 1024, // 200MB
    supportedFormats: ['mp4', 'webm', 'mov'],
    enableProgress: true,
    enableKeyboard: true,
    enableAnalytics: true,
    quality: 'hd',
    
    // Styles personnalisés
    customCSS: {
        '.video-container': {
            'border-radius': '20px',
            'box-shadow': '0 20px 40px rgba(0,0,0,0.1)'
        },
        '.play-btn': {
            'background': 'linear-gradient(45deg, #667eea, #764ba2)',
            'border': 'none'
        }
    },
    
    // Callbacks
    onVideoLoad: (info) => console.log('Vidéo chargée:', info),
    onVideoError: (error) => console.error('Erreur vidéo:', error),
    onUploadProgress: (progress) => console.log('Progression:', progress + '%')
});

// Utiliser les nouvelles fonctionnalités
setTimeout(() => {
    console.log('Analytics:', advancedPlayer.getAnalytics());
    console.log('Info vidéo:', advancedPlayer.getVideoInfo());
    
    // Capturer un screenshot
    const screenshot = advancedPlayer.captureScreenshot();
    if (screenshot) {
        console.log('Screenshot capturé');
    }
}, 5000);
*/