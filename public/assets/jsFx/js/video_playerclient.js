class VideoDropzoneWithControls {
    constructor(options = {}) {
        this.containerId = options.containerId;
        this.videoUrl = options.videoUrl || null;
        this.title = options.title || "Vidéo sans titre";
        this.thumbnail = options.thumbnail || null;
        this.mode = options.mode || "player"; // 'player' ou 'upload'
        this.theme = options.theme || "primary";
        this.apiUrl = options.apiUrl || null; // URL pour l'upload
        this.uploadHeaders = options.uploadHeaders || {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
        }; // Headers personnalisés

        this.isPlaying = false;
        this.currentTime = 0;
        this.duration = 0;
        this.volume = 1;
        this.isMuted = false;
        this.isFullscreen = false;
        this.isDragging = false;
        this.selectedFile = null;
        this.playbackRate = 1;
        this.isUploading = false;
        this.uploadProgress = 0;

        this.init();
    }

    init() {
        if (!document.getElementById("video-player-styles")) {
            loadJS();
        }

        this.container = document.getElementById(this.containerId);
        if (!this.container) {
            console.error(`Container ${this.containerId} introuvable`);
            return;
        }

        this.render();
        this.bindEvents();
        this.initKeyboardControls();
    }

    render() {
        if (this.videoUrl || this.selectedFile) {
            this.renderPlayer();
        } else {
            this.renderUploadZone();
        }
    }

    renderPlayer() {
        const videoSrc = this.selectedFile
            ? URL.createObjectURL(this.selectedFile)
            : this.videoUrl;

        // Générer le thumbnail par défaut si aucun n'est fourni
        // const thumbnailElement = this.thumbnail
        //     ? `<img class="video-thumbnail" src="${this.thumbnail}" alt="${this.title}">`
        //     : `<div class="video-thumbnail-default">
        //      <div class="thumbnail-text">${this.getFileDisplayName()}</div>
        //    </div>`;

        const thumbnailElement = this.thumbnail
            ? `<div class="video-thumbnail-default" style="background-image: url('${this.thumbnail}'); background-size: cover;">
             <div class="thumbnail-text">${this.mode === "player" ? this.title : this.getFileDisplayName()}</div>
           </div>
            `
            : `<div class="video-thumbnail-default">
             <div class="thumbnail-text">${this.getFileDisplayName()}</div>
           </div>`;

        this.container.innerHTML = `
            <div class="video-dropzone">
                <div class="video-container">
                    <video class="video-player" preload="metadata" poster="${
                        this.thumbnail || ""
                    }">
                        <source src="${videoSrc}" type="video/mp4">
                        Votre navigateur ne supporte pas la lecture vidéo.
                    </video>

                    ${thumbnailElement}
                    
                    <div class="play-overlay ${this.isPlaying ? "d-none" : ""}">
                        <button class="play-btn">
                            <i class="fas fa-play"></i>
                        </button>
                    </div>
                    
                    <div class="loading-overlay d-none">
                        <div class="spinner-border text-white" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>

                    <div class="upload-progress-overlay d-none">
                        <div class="upload-progress-container">
                            <div class="upload-progress-bar">
                                <div class="upload-progress-fill" style="width: 0%"></div>
                            </div>
                            <div class="upload-progress-text">Upload: 0%</div>
                        </div>
                    </div>
                    
                    <div class="video-controls">
                        <div class="progress-container">
                            <div class="progress-bar" style="width: 0%">
                                <div class="progress-handle"></div>
                            </div>
                        </div>
                        
                        <div class="control-buttons">
                            <button class="control-btn play-pause">
                                <i class="fas fa-play"></i>
                            </button>
                            
                            <button class="control-btn rewind">
                                <i class="fas fa-backward"></i>
                            </button>
                            
                            <button class="control-btn forward">
                                <i class="fas fa-forward"></i>
                            </button>

                            <div class="speed-container">
                                <button class="control-btn speed-btn" title="Vitesse de lecture">
                                    <span class="speed-text">1x</span>
                                </button>
                                <div class="speed-menu d-none">
                                    <button class="speed-option" data-speed="0.5">0.5x</button>
                                    <button class="speed-option" data-speed="0.75">0.75x</button>
                                    <button class="speed-option active" data-speed="1">1x</button>
                                    <button class="speed-option" data-speed="1.25">1.25x</button>
                                    <button class="speed-option" data-speed="1.5">1.5x</button>
                                    <button class="speed-option" data-speed="2">2x</button>
                                </div>
                            </div>
                            
                            <div class="volume-container">
                                <button class="control-btn volume-btn">
                                    <i class="fas fa-volume-up"></i>
                                </button>
                                <div class="volume-slider">
                                    <div class="volume-progress" style="width: 100%"></div>
                                </div>
                            </div>
                            
                            <div class="time-display">
                                <span class="current-time">0:00</span> / <span class="total-time">0:00</span>
                            </div>

                            <button class="control-btn pip-btn" title="Mode incrustation">
                                <i class="fas fa-external-link-alt"></i>
                            </button>
                            
                            <button class="control-btn fullscreen">
                                <i class="fas fa-expand"></i>
                            </button>
                            
                            ${
                                this.mode === "upload"
                                    ? `
                                <button class="control-btn change-video" title="Changer la vidéo">
                                    <i class="fas fa-exchange-alt"></i>
                                </button>
                            `
                                    : ""
                            }
                        </div>
                    </div>
                </div>
                
                ${
                    this.mode === "upload"
                        ? `
                    <div class="text-center mt-3">
                        <button class="btn btn-${
                            this.theme
                        } btn-lg upload-btn" ${
                              !this.selectedFile ? "disabled" : ""
                          }>
                            <i class="fas fa-upload me-2"></i>
                            <span class="upload-text">Envoyer la vidéo</span>
                        </button>
                    </div>
                `
                        : ""
                }
                
                <input type="file" class="d-none video-input" accept="video/*">
            </div>
        `;
    }

    renderUploadZone() {
        this.container.innerHTML = `
            <div class="video-dropzone">
                <div class="dropzone-empty">
                    <i class="fas fa-cloud-upload-alt fs-1 text-${this.theme} mb-3"></i>
                    <h5 class="text-${this.theme} mb-2">Déposez votre vidéo ici</h5>
                    <p class="text-muted text-center mb-3">
                        Glissez-déposez un fichier vidéo ou cliquez pour sélectionner<br>
                        <small>Formats: MP4, WebM, AVI, MOV (max: 300MB)</small>
                    </p>
                    <button class="btn btn-${this.theme}">
                        <i class="fas fa-folder-open me-2"></i>Choisir une vidéo
                    </button>
                </div>
                <input type="file" class="d-none video-input" accept="video/*">
            </div>
        `;
    }

    getFileDisplayName() {
        if (this.selectedFile) {
            return this.selectedFile.name.replace(/\.[^/.]+$/, ""); // Retire l'extension
        }
        return this.title || "Vidéo";
    }

    bindEvents() {
        const container = this.container;

        // Éléments du lecteur
        const video = container.querySelector(".video-player");
        const playOverlay = container.querySelector(".play-overlay .play-btn");
        const playPauseBtn = container.querySelector(".play-pause");
        const rewindBtn = container.querySelector(".rewind");
        const forwardBtn = container.querySelector(".forward");
        const volumeBtn = container.querySelector(".volume-btn");
        const fullscreenBtn = container.querySelector(".fullscreen");
        const pipBtn = container.querySelector(".pip-btn");
        const speedBtn = container.querySelector(".speed-btn");
        const progressContainer = container.querySelector(
            ".progress-container"
        );
        const volumeSlider = container.querySelector(".volume-slider");
        const changeVideoBtn = container.querySelector(".change-video");
        const uploadBtn = container.querySelector(".upload-btn");

        // Upload zone
        const dropzone = container.querySelector(".dropzone-empty");
        const videoInput = container.querySelector(".video-input");

        if (video) {
            // Événements vidéo
            video.addEventListener("loadedmetadata", () => {
                this.duration = video.duration;
                this.updateTimeDisplay();
            });

            video.addEventListener("timeupdate", () => {
                this.currentTime = video.currentTime;
                this.updateProgress();
                this.updateTimeDisplay();
            });

            // video.addEventListener("ended", () => {
            //     this.isPlaying = false;
            //     this.updatePlayButton();
            //     container
            //         .querySelector(".play-overlay")
            //         .classList.remove("d-none");
            // });

            video.addEventListener("ended", () => {
                this.isPlaying = false;
                this.updatePlayButton();
                // Remettre le thumbnail et le bouton play quand la vidéo se termine
                this.showThumbnail();
                container
                    .querySelector(".play-overlay")
                    .classList.remove("d-none");
            });

            video.addEventListener("waiting", () => {
                container
                    .querySelector(".loading-overlay")
                    .classList.remove("d-none");
            });

            video.addEventListener("canplay", () => {
                container
                    .querySelector(".loading-overlay")
                    .classList.add("d-none");
            });

            video.addEventListener("enterpictureinpicture", () => {
                this.updatePipButton(true);
            });

            video.addEventListener("leavepictureinpicture", () => {
                this.updatePipButton(false);
            });
        }

        // Boutons de contrôle
        if (playOverlay) {
            playOverlay.addEventListener("click", () => this.togglePlay());
        }

        if (playPauseBtn) {
            playPauseBtn.addEventListener("click", () => this.togglePlay());
        }

        if (rewindBtn) {
            rewindBtn.addEventListener("click", () =>
                this.seek(this.currentTime - 10)
            );
        }

        if (forwardBtn) {
            forwardBtn.addEventListener("click", () =>
                this.seek(this.currentTime + 10)
            );
        }

        if (volumeBtn) {
            volumeBtn.addEventListener("click", () => this.toggleMute());
        }

        if (fullscreenBtn) {
            fullscreenBtn.addEventListener("click", () =>
                this.toggleFullscreen()
            );
        }

        if (pipBtn) {
            pipBtn.addEventListener("click", () =>
                this.togglePictureInPicture()
            );
        }

        if (speedBtn) {
            speedBtn.addEventListener("click", () => this.toggleSpeedMenu());
        }

        if (changeVideoBtn) {
            changeVideoBtn.addEventListener("click", () => videoInput.click());
        }

        if (uploadBtn) {
            uploadBtn.addEventListener("click", () => this.uploadVideo());
        }

        // Menu vitesse
        const speedOptions = container.querySelectorAll(".speed-option");
        speedOptions.forEach((option) => {
            option.addEventListener("click", (e) => {
                const speed = parseFloat(e.target.dataset.speed);
                this.setPlaybackRate(speed);
                this.hideSpeedMenu();
            });
        });

        // Fermer le menu vitesse en cliquant ailleurs
        document.addEventListener("click", (e) => {
            const speedContainer = container.querySelector(".speed-container");
            if (speedContainer && !speedContainer.contains(e.target)) {
                this.hideSpeedMenu();
            }
        });

        // Barre de progression
        if (progressContainer) {
            progressContainer.addEventListener("click", (e) => {
                const rect = progressContainer.getBoundingClientRect();
                const percent = (e.clientX - rect.left) / rect.width;
                this.seek(percent * this.duration);
            });

            progressContainer.addEventListener("mousedown", (e) => {
                this.isDragging = true;
                this.handleProgressDrag(e);
            });

            document.addEventListener("mousemove", (e) => {
                if (this.isDragging) {
                    this.handleProgressDrag(e);
                }
            });

            document.addEventListener("mouseup", () => {
                this.isDragging = false;
            });
        }

        // Volume slider
        if (volumeSlider) {
            volumeSlider.addEventListener("click", (e) => {
                const rect = volumeSlider.getBoundingClientRect();
                const percent = (e.clientX - rect.left) / rect.width;
                this.setVolume(percent);
            });
        }

        // Upload zone
        if (dropzone) {
            dropzone.addEventListener("click", () => videoInput.click());
            dropzone.addEventListener("dragover", (e) =>
                this.handleDragOver(e)
            );
            dropzone.addEventListener("drop", (e) => this.handleDrop(e));
            dropzone.addEventListener("dragleave", (e) =>
                this.handleDragLeave(e)
            );
        }

        if (videoInput) {
            videoInput.addEventListener("change", (e) =>
                this.handleFileSelect(e)
            );
        }
    }

    handleProgressDrag(e) {
        const progressContainer = this.container.querySelector(
            ".progress-container"
        );
        const rect = progressContainer.getBoundingClientRect();
        const percent = Math.max(
            0,
            Math.min(1, (e.clientX - rect.left) / rect.width)
        );
        this.seek(percent * this.duration);
    }

    // togglePlay() {
    //     const video = this.container.querySelector(".video-player");
    //     if (!video) return;

    //     if (this.isPlaying) {
    //         video.pause();
    //         this.isPlaying = false;
    //     } else {
    //         video.play();
    //         this.isPlaying = true;
    //         this.container
    //             .querySelector(".play-overlay")
    //             .classList.add("d-none");
    //     }

    //     this.updatePlayButton();
    // }

    togglePlay() {
        const video = this.container.querySelector(".video-player");
        //const videoContainer = this.container.querySelector(".video-container");

        if (!video) return;

        if (this.isPlaying) {
            video.pause();
            this.isPlaying = false;
            //videoContainer.classList.remove("playing");
        } else {
            video.play();
            this.isPlaying = true;
            //videoContainer.classList.add("playing");
            this.hideThumbnail();
            this.container
                .querySelector(".play-overlay")
                .classList.add("d-none");
        }

        this.updatePlayButton();
    }

    hideThumbnail() {
        const thumbnail = this.container.querySelector(".video-thumbnail");
        const thumbnailDefault = this.container.querySelector(
            ".video-thumbnail-default"
        );

        if (thumbnail) thumbnail.style.opacity = "0";
        if (thumbnailDefault) thumbnailDefault.style.opacity = "0";
    }

    showThumbnail() {
        const thumbnail = this.container.querySelector(".video-thumbnail");
        const thumbnailDefault = this.container.querySelector(
            ".video-thumbnail-default"
        );

        if (thumbnail) thumbnail.style.opacity = "1";
        if (thumbnailDefault) thumbnailDefault.style.opacity = "1";
    }

    seek(time) {
        const video = this.container.querySelector(".video-player");
        if (!video) return;

        video.currentTime = Math.max(0, Math.min(time, this.duration));
        this.currentTime = video.currentTime;
    }

    setVolume(volume) {
        const video = this.container.querySelector(".video-player");
        if (!video) return;

        this.volume = Math.max(0, Math.min(1, volume));
        video.volume = this.volume;
        this.updateVolumeDisplay();
    }

    toggleMute() {
        const video = this.container.querySelector(".video-player");
        if (!video) return;

        this.isMuted = !this.isMuted;
        video.muted = this.isMuted;
        this.updateVolumeDisplay();
    }

    toggleFullscreen() {
        const videoContainer = this.container.querySelector(".video-container");

        if (!document.fullscreenElement) {
            videoContainer.requestFullscreen().catch(console.error);
            this.isFullscreen = true;
        } else {
            document.exitFullscreen();
            this.isFullscreen = false;
        }

        this.updateFullscreenButton();
    }

    async togglePictureInPicture() {
        const video = this.container.querySelector(".video-player");
        if (!video) return;

        try {
            if (video !== document.pictureInPictureElement) {
                await video.requestPictureInPicture();
            } else {
                await document.exitPictureInPicture();
            }
        } catch (error) {
            console.error("Erreur Picture-in-Picture:", error);
            this.showAlert("Mode incrustation non supporté", "warning");
        }
    }

    toggleSpeedMenu() {
        const speedMenu = this.container.querySelector(".speed-menu");
        if (speedMenu) {
            speedMenu.classList.toggle("d-none");
        }
    }

    hideSpeedMenu() {
        const speedMenu = this.container.querySelector(".speed-menu");
        if (speedMenu) {
            speedMenu.classList.add("d-none");
        }
    }

    setPlaybackRate(rate) {
        const video = this.container.querySelector(".video-player");
        if (!video) return;

        this.playbackRate = rate;
        video.playbackRate = rate;
        this.updateSpeedDisplay();
    }

    async uploadVideo() {
        if (!this.selectedFile || !this.apiUrl) {
            this.showAlert(
                "Aucun fichier sélectionné ou URL API manquante",
                "danger"
            );
            return;
        }

        if (this.isUploading) return;

        const maxSize = 300 * 1024 * 1024; // 300MB
        if (this.selectedFile.size > maxSize) {
            this.showAlert(
                "Le fichier est trop volumineux (max: 300MB)",
                "danger"
            );
            return;
        }

        this.isUploading = true;
        this.showUploadProgress(true);
        this.updateUploadButton(true);

        const formData = new FormData();
        formData.append("video", this.selectedFile);
        formData.append("title", this.title);

        try {
            const response = await fetch(this.apiUrl, {
                method: "POST",
                body: formData,
                headers: {
                    ...this.uploadHeaders,
                },
            });

            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }

            const result = await response.json();

            this.showAlert("Vidéo uploadée avec succès!", "success");
            this.onUploadSuccess(result);
        } catch (error) {
            console.error("Erreur upload:", error);
            this.showAlert(
                "Erreur lors de l'upload: " + error.message,
                "danger"
            );
            this.onUploadError(error);
        } finally {
            this.isUploading = false;
            this.showUploadProgress(false);
            this.updateUploadButton(false);
        }
    }

    showUploadProgress(show) {
        const overlay = this.container.querySelector(
            ".upload-progress-overlay"
        );
        if (overlay) {
            overlay.classList.toggle("d-none", !show);
        }
    }

    updateUploadProgress(percent) {
        const fill = this.container.querySelector(".upload-progress-fill");
        const text = this.container.querySelector(".upload-progress-text");

        if (fill) fill.style.width = percent + "%";
        if (text) text.textContent = `Upload: ${Math.round(percent)}%`;
    }

    updateUploadButton(uploading) {
        const btn = this.container.querySelector(".upload-btn");
        const text = this.container.querySelector(".upload-text");
        const icon = btn?.querySelector("i");

        if (btn) {
            btn.disabled = uploading;
            if (uploading) {
                btn.classList.add("uploading");
                if (icon) icon.className = "fas fa-spinner fa-spin me-2";
                if (text) text.textContent = "Upload en cours...";
            } else {
                btn.classList.remove("uploading");
                if (icon) icon.className = "fas fa-upload me-2";
                if (text) text.textContent = "Envoyer la vidéo";
            }
        }
    }

    updatePlayButton() {
        const playPauseBtn = this.container.querySelector(".play-pause i");
        if (playPauseBtn) {
            playPauseBtn.className = this.isPlaying
                ? "fas fa-pause"
                : "fas fa-play";
        }
    }

    updateProgress() {
        if (this.isDragging) return;

        const progressBar = this.container.querySelector(".progress-bar");
        if (progressBar && this.duration > 0) {
            const percent = (this.currentTime / this.duration) * 100;
            progressBar.style.width = percent + "%";
        }
    }

    updateTimeDisplay() {
        const currentTimeEl = this.container.querySelector(".current-time");
        const totalTimeEl = this.container.querySelector(".total-time");

        if (currentTimeEl) {
            currentTimeEl.textContent = this.formatTime(this.currentTime);
        }
        if (totalTimeEl) {
            totalTimeEl.textContent = this.formatTime(this.duration);
        }
    }

    updateVolumeDisplay() {
        const volumeProgress = this.container.querySelector(".volume-progress");
        const volumeIcon = this.container.querySelector(".volume-btn i");

        if (volumeProgress) {
            volumeProgress.style.width =
                (this.isMuted ? 0 : this.volume * 100) + "%";
        }

        if (volumeIcon) {
            if (this.isMuted || this.volume === 0) {
                volumeIcon.className = "fas fa-volume-mute";
            } else if (this.volume < 0.5) {
                volumeIcon.className = "fas fa-volume-down";
            } else {
                volumeIcon.className = "fas fa-volume-up";
            }
        }
    }

    updateFullscreenButton() {
        const fullscreenIcon = this.container.querySelector(".fullscreen i");
        if (fullscreenIcon) {
            fullscreenIcon.className = this.isFullscreen
                ? "fas fa-compress"
                : "fas fa-expand";
        }
    }

    updatePipButton(inPip) {
        const pipIcon = this.container.querySelector(".pip-btn i");
        if (pipIcon) {
            pipIcon.className = inPip
                ? "fas fa-compress-arrows-alt"
                : "fas fa-external-link-alt";
        }
    }

    updateSpeedDisplay() {
        const speedText = this.container.querySelector(".speed-text");
        const speedOptions = this.container.querySelectorAll(".speed-option");

        if (speedText) {
            speedText.textContent = this.playbackRate + "x";
        }

        speedOptions.forEach((option) => {
            const speed = parseFloat(option.dataset.speed);
            option.classList.toggle("active", speed === this.playbackRate);
        });
    }

    formatTime(seconds) {
        if (!seconds || isNaN(seconds)) return "0:00";

        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return mins + ":" + (secs < 10 ? "0" : "") + secs;
    }

    initKeyboardControls() {
        document.addEventListener("keydown", (e) => {
            const videoContainer =
                this.container.querySelector(".video-container");
            if (
                !videoContainer ||
                !this.container.contains(document.activeElement)
            ) {
                return;
            }

            switch (e.code) {
                case "Space":
                    e.preventDefault();
                    this.togglePlay();
                    break;
                case "ArrowLeft":
                    e.preventDefault();
                    this.seek(this.currentTime - 10);
                    break;
                case "ArrowRight":
                    e.preventDefault();
                    this.seek(this.currentTime + 10);
                    break;
                case "ArrowUp":
                    e.preventDefault();
                    this.setVolume(this.volume + 0.1);
                    break;
                case "ArrowDown":
                    e.preventDefault();
                    this.setVolume(this.volume - 0.1);
                    break;
                case "KeyF":
                    e.preventDefault();
                    this.toggleFullscreen();
                    break;
                case "KeyM":
                    e.preventDefault();
                    this.toggleMute();
                    break;
                case "KeyP":
                    e.preventDefault();
                    this.togglePictureInPicture();
                    break;
                case "Comma":
                    e.preventDefault();
                    this.changeSpeed(-0.25);
                    break;
                case "Period":
                    e.preventDefault();
                    this.changeSpeed(0.25);
                    break;
            }
        });

        const videoContainer = this.container.querySelector(".video-container");
        if (videoContainer) {
            videoContainer.setAttribute("tabindex", "0");
            videoContainer.addEventListener("click", () => {
                videoContainer.focus();
            });
        }
    }

    changeSpeed(delta) {
        const newSpeed = Math.max(0.25, Math.min(2, this.playbackRate + delta));
        this.setPlaybackRate(newSpeed);
    }

    handleFileSelect(e) {
        const file = e.target.files[0];
        if (file && this.isValidVideoFile(file)) {
            this.selectedFile = file;
            this.render();
            this.bindEvents();
            this.initKeyboardControls();
        } else {
            this.showAlert("Fichier vidéo invalide", "danger");
        }
    }

    isValidVideoFile(file) {
        const validTypes = [
            "video/mp4",
            "video/webm",
            "video/avi",
            "video/mov",
            "video/quicktime",
            "video/mkv",
        ];
        return validTypes.includes(file.type);
    }

    handleDragOver(e) {
        e.preventDefault();
        this.container
            .querySelector(".dropzone-empty")
            .classList.add("drag-over");
    }

    handleDragLeave(e) {
        e.preventDefault();
        this.container
            .querySelector(".dropzone-empty")
            .classList.remove("drag-over");
    }

    handleDrop(e) {
        e.preventDefault();
        this.container
            .querySelector(".dropzone-empty")
            .classList.remove("drag-over");

        const files = e.dataTransfer.files;
        if (files.length > 0 && this.isValidVideoFile(files[0])) {
            this.selectedFile = files[0];
            this.render();
            this.bindEvents();
            this.initKeyboardControls();
        } else {
            this.showAlert("Fichier vidéo invalide", "danger");
        }
    }

    showAlert(message, type = "info") {
        const alert = document.createElement("div");
        alert.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
        alert.style.zIndex = "9999";
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);

        setTimeout(() => {
            alert.remove();
        }, 3000);
    }

    // Callbacks pour les événements
    onUploadSuccess(result) {
        console.log("Upload réussi:", result);
        // Callback personnalisable
    }

    onUploadError(error) {
        console.error("Erreur upload:", error);
        // Callback personnalisable
    }

    // Méthodes publiques
    play() {
        if (!this.isPlaying) this.togglePlay();
    }

    pause() {
        if (this.isPlaying) this.togglePlay();
    }

    getCurrentTime() {
        return this.currentTime;
    }

    getDuration() {
        return this.duration;
    }

    setCurrentTime(time) {
        this.seek(time);
    }

    getPlaybackRate() {
        return this.playbackRate;
    }

    setApiUrl(url) {
        this.apiUrl = url;
    }

    setUploadHeaders(headers) {
        this.uploadHeaders = headers;
    }
}

function createVideoDropzoneWithControls(options) {
    return new VideoDropzoneWithControls(options);
}

// // Exemples d'initialisation
// const player1 = new VideoDropzoneWithControls({
//     containerId: "video-player-1",
//     videoUrl: "https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4",
//     title: "Big Buck Bunny",
//     mode: "player",
//     theme: "primary",
// });

// const player2 = new VideoDropzoneWithControls({
//     containerId: "video-player-2",
//     mode: "upload",
//     theme: "success",
//     apiUrl: "/api/upload-video",
//     uploadHeaders: {
//         'Authorization': 'Bearer your-token-here',
//         'X-Custom-Header': 'value'
//     }
// });

// // Callbacks personnalisés
// player2.onUploadSuccess = function(result) {
//     console.log("Vidéo uploadée avec succès:", result);
//     // Rediriger ou actualiser l'interface
// };

// player2.onUploadError = function(error) {
//     console.log("Erreur lors de l'upload:", error);
//     // Gérer l'erreur
// };

function loadJS() {
    // Créer un élément style
    const styleElement = document.createElement("style");
    styleElement.id = "video-player-styles";

    // Ajouter le CSS dans l'élément style
    styleElement.textContent = `
    .video-dropzone {
        position: relative;
    }

    .video-container {
        position: relative;
        background: #000;
        border-radius: 12px;
        overflow: hidden;
        aspect-ratio: 16/9;
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    }

    .video-player {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .video-controls {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.8));
        padding: 20px 15px 15px;
        transform: translateY(0);
        transition: transform 0.3s ease;
        z-index: 10;
    }

    .video-container:hover .video-controls {
        transform: translateY(0);
    }

    .progress-container {
        position: relative;
        height: 6px;
        background: rgba(255,255,255,0.3);
        border-radius: 3px;
        margin-bottom: 10px;
        cursor: pointer;
    }

    .progress-bar {
        height: 100%;
        background: var(--bs-primary);
        border-radius: 3px;
        position: relative;
        transition: width 0.1s ease;
    }

    .progress-handle {
        position: absolute;
        right: -8px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        background: var(--bs-primary);
        border-radius: 50%;
        cursor: grab;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .progress-container:hover .progress-handle {
        opacity: 1;
    }

    .progress-handle:active {
        cursor: grabbing;
    }

    .control-buttons {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .control-btn {
        background: none;
        border: none;
        color: white;
        font-size: 18px;
        cursor: pointer;
        padding: 8px;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s ease;
    }

    .control-btn:hover {
        background: rgba(255,255,255,0.2);
    }

    .control-btn.play-pause {
        font-size: 20px;
    }

    /* Speed Control Styles */
    .speed-container {
        position: relative;
        display: flex;
        align-items: center;
    }

    .speed-btn {
        font-size: 14px !important;
        font-weight: bold;
    }

    .speed-text {
        font-size: 12px;
        min-width: 20px;
        text-align: center;
    }

    .speed-menu {
        position: absolute;
        bottom: 50px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.9);
        border-radius: 8px;
        padding: 5px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        z-index: 100;
        min-width: 80px;
    }

    .speed-option {
        display: block;
        width: 100%;
        background: none;
        border: none;
        color: white;
        padding: 8px 12px;
        font-size: 14px;
        cursor: pointer;
        border-radius: 4px;
        transition: background 0.2s ease;
        text-align: center;
    }

    .speed-option:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .speed-option.active {
        background: var(--bs-primary);
        color: white;
    }

    /* Volume Container */
    .volume-container {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .volume-slider {
        width: 80px;
        height: 4px;
        background: rgba(255,255,255,0.3);
        border-radius: 2px;
        position: relative;
        cursor: pointer;
    }

    .volume-progress {
        height: 100%;
        background: white;
        border-radius: 2px;
        transition: width 0.1s ease;
    }

    .time-display {
        color: white;
        font-size: 14px;
        font-family: monospace;
        margin-left: auto;
        margin-right: 10px;
    }

    /* Loading Overlay */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 20;
    }

    /* Upload Progress Overlay */
    .upload-progress-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 25;
    }

    .upload-progress-container {
        text-align: center;
        color: white;
    }

    .upload-progress-bar {
        width: 300px;
        height: 8px;
        background: rgba(255,255,255,0.3);
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 10px;
    }

    .upload-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--bs-success), var(--bs-info));
        border-radius: 4px;
        transition: width 0.3s ease;
    }

    .upload-progress-text {
        font-size: 16px;
        font-weight: 500;
    }

    /* Play Overlay */
    .play-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 5;
    }

    .play-overlay button {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: none;
        background: rgba(255,255,255,0.9);
        color: #333;
        font-size: 30px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }

    .play-overlay button:hover {
        transform: scale(1.1);
        background: white;
    }

    /* Upload Zone Styles */
    .dropzone-empty {
        border: 2px dashed #ccc;
        border-radius: 12px;
        background: #f8f9fa;
        aspect-ratio: 16/9;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .dropzone-empty:hover {
        border-color: var(--bs-primary);
        background: #e3f2fd;
        transform: scale(1.02);
    }

    .dropzone-empty.drag-over {
        border-color: var(--bs-success);
        background: #e8f5e8;
    }

    /* Upload Button Styles */
    .upload-btn {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .upload-btn.uploading {
        pointer-events: none;
        opacity: 0.8;
    }

    .upload-btn.uploading::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: shimmer 1.5s infinite;
    }

    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }

    /* Picture-in-Picture Button */
    .pip-btn {
        font-size: 16px;
    }

    .pip-btn:hover {
        transform: scale(1.1);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .video-controls {
            padding: 15px 10px 10px;
        }
        
        .control-buttons {
            gap: 8px;
        }
        
        .control-btn {
            width: 35px;
            height: 35px;
            font-size: 16px;
        }
        
        .time-display {
            font-size: 12px;
            margin-right: 8px;
        }
        
        .volume-slider {
            width: 60px;
        }
        
        .speed-menu {
            min-width: 70px;
        }
        
        .upload-progress-bar {
            width: 250px;
        }
    }

    @media (max-width: 480px) {
        .control-buttons {
            gap: 5px;
        }
        
        .control-btn {
            width: 32px;
            height: 32px;
            font-size: 14px;
        }
        
        .volume-container {
            gap: 5px;
        }
        
        .volume-slider {
            width: 50px;
        }
        
        .time-display {
            display: none;
        }
        
        .upload-progress-bar {
            width: 200px;
        }
    }

    /* Accessibility Styles */
    .control-btn:focus {
        outline: 2px solid var(--bs-primary);
        outline-offset: 2px;
    }

    .speed-option:focus {
        outline: 2px solid var(--bs-primary);
        outline-offset: -2px;
    }

    .progress-container:focus {
        outline: 2px solid var(--bs-primary);
        outline-offset: 2px;
    }

    /* Animation for control visibility */
    .video-container:not(:hover) .video-controls {
        transform: translateY(100%);
    }

    .video-container.playing:not(:hover) .video-controls {
        opacity: 0;
        pointer-events: none;
    }

    .video-container.playing:hover .video-controls {
        opacity: 1;
        pointer-events: all;
    }

    /* Custom scrollbar for speed menu */
    .speed-menu::-webkit-scrollbar {
        width: 4px;
    }

    .speed-menu::-webkit-scrollbar-track {
        background: transparent;
    }

    .speed-menu::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.3);
        border-radius: 2px;
    }

    /* Body and demo styles */
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 2rem 0;
    }

    .demo-card {
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }

    /* Thumbnail Styles */
.video-thumbnail,
.video-thumbnail-default {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 1;
    transition: opacity 0.3s ease;
}

.video-thumbnail-default {
    background: linear-gradient(135deg, var(--bs-primary, #007bff) 0%, var(--bs-secondary, #6c757d) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.thumbnail-text {
    font-size: 1.5rem;
    font-weight: 600;
    text-align: center;
    padding: 1rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    max-width: 80%;
    word-wrap: break-word;
}

/* Masquer le thumbnail quand la vidéo joue */
.video-container.playing .video-thumbnail,
.video-container.playing .video-thumbnail-default {
    opacity: 0;
    pointer-events: none;
}

/* Responsive pour le texte du thumbnail */
@media (max-width: 768px) {
    .thumbnail-text {
        font-size: 1.2rem;
    }
}

@media (max-width: 480px) {
    .thumbnail-text {
        font-size: 1rem;
    }
}

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .demo-card {
            background: rgba(0,0,0,0.8);
            border: 1px solid rgba(255,255,255,0.1);
            color: white;
        }
        
        .dropzone-empty {
            background: #2d2d2d;
            border-color: #555;
            color: white;
        }
        
        .dropzone-empty:hover {
            background: #3d3d3d;
            border-color: var(--bs-primary);
        }
    }

    /* High contrast mode support */
    @media (prefers-contrast: high) {
        .video-controls {
            background: rgba(0,0,0,0.95);
        }
        
        .control-btn {
            border: 1px solid white;
        }
        
        .progress-bar {
            border: 1px solid white;
        }
    }
    `;

    // Ajouter l'élément style au head du document
    document.head.appendChild(styleElement);

    console.log("Styles du lecteur vidéo chargés avec succès!");
}

// Version avec gestion des erreurs et callback
function loadJSWithCallback(callback) {
    try {
        // Vérifier si les styles sont déjà chargés
        if (document.getElementById("video-player-styles")) {
            console.log("Les styles sont déjà chargés");
            if (callback) callback(true);
            return;
        }

        loadJS();

        if (callback) {
            callback(true);
        }
    } catch (error) {
        console.error("Erreur lors du chargement des styles:", error);
        if (callback) {
            callback(false, error);
        }
    }
}

// Version asynchrone
async function loadJSAsync() {
    return new Promise((resolve, reject) => {
        try {
            loadJSWithCallback((success, error) => {
                if (success) {
                    resolve();
                } else {
                    reject(error);
                }
            });
        } catch (error) {
            reject(error);
        }
    });
}
