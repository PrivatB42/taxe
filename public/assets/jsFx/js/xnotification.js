function x_notification({
    message = "Opération réussie !",
    type = "success",
    duration = 3000,
}) {
    // Créer le conteneur s'il n'existe pas
    let toastContainer = document.querySelector(".toast-container");
    if (!toastContainer) {
        toastContainer = document.createElement("div");
        toastContainer.className = "toast-container";
        document.body.appendChild(toastContainer);
    }

    // Configuration des types avec Font Awesome
    const config = {
        success: {
            icon: "fas fa-check-circle text-success",
            progressClass: "bg-success",
            toastClass: "toast-success",
        },
        error: {
            icon: "fas fa-times-circle text-danger",
            progressClass: "bg-danger",
            toastClass: "toast-error",
        },
        warning: {
            icon: "fas fa-exclamation-triangle text-warning",
            progressClass: "bg-warning",
            toastClass: "toast-warning",
        },
        info: {
            icon: "fas fa-info-circle text-info",
            progressClass: "bg-info",
            toastClass: "toast-info",
        },
    };

    // Vérifier si le type existe, sinon utiliser 'success' par défaut
    const currentConfig = config[type] || config.success;

    // Créer le toast
    const toast = document.createElement("div");
    toast.className = `custom-toast toast align-items-center border-0 shadow-lg ${currentConfig.toastClass}`;
    toast.innerHTML = `
        <div class="d-flex align-items-center p-3">
            <div class="toast-icon me-3">
                <i class="${currentConfig.icon}"></i>
            </div>
            <div class="toast-body p-0">
                ${message}
            </div>
        </div>
        <div class="progress" style="height: 3px;">
            <div class="progress-bar ${currentConfig.progressClass}" role="progressbar" style="width: 100%"></div>
        </div>
    `;

    // Ajouter le toast au conteneur
    toastContainer.appendChild(toast);

    // Animation d'entrée
    setTimeout(() => {
        toast.classList.add("show");
    }, 10);

    // Animation de la barre de progression
    const progressBar = toast.querySelector(".progress-bar");
    setTimeout(() => {
        progressBar.style.width = "0%";
    }, 50);

    // Fermeture automatique après la durée spécifiée
    setTimeout(() => {
        toast.classList.remove("show");
        setTimeout(() => {
            toast.remove();
        }, 500); // Correspond à la durée de l'animation de sortie
    }, duration);
}

function x_successNotification(message, duration) {
    x_notification({ message, type: "success", duration });
}

function x_errorNotification(message, duration) {
    x_notification({ message, type: "error", duration });
}

/**
 * Affiche une alerte Bootstrap dans un conteneur spécifique
 * @param {string|Array} content - Message(s) à afficher
 * @param {string} type - Type d'alerte (success, danger, warning, info)
 * @param {boolean} dismissible - Si l'alerte peut être fermée
 * @param {number} autoClose - Temps avant fermeture automatique (ms), 0 = pas de fermeture
 * @param {string} [containerId='alerts-container'] - ID du conteneur d'alertes
 */
function x_alert(
    content,
    type = "success",
    containerId = "x-alerts-container",
    dismissible = true,
    autoClose = 5000
) {
    const container = document.getElementById(containerId);
    if (!container) {
        console.error(
            "Le conteneur d'alertes (#alerts-container) n'existe pas"
        );
        return;
    }

    // Configuration des types
    const config = {
        success: {
            icon: "fas fa-check-circle",
            class: "alert-success",
        },
        danger: {
            icon: "fas fa-times-circle",
            class: "alert-danger",
        },
        warning: {
            icon: "fas fa-exclamation-triangle",
            class: "alert-warning",
        },
        info: {
            icon: "fas fa-info-circle",
            class: "alert-info",
        },
    };

    // Création de l'alerte
    const alert = document.createElement("div");
    alert.className = `alert ${config[type].class} fade show`;
    alert.role = "alert";

    // Contenu de l'alerte
    let contentHtml = "";
    if (Array.isArray(content)) {
        content.forEach((msg) => {
            contentHtml += `<div>${msg}</div>`;
        });
    } else {
        contentHtml = content;
    }

    alert.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="${config[type].icon} me-2 fs-4"></i>
            <div class="flex-grow-1">${contentHtml}</div>
            ${
                dismissible
                    ? '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
                    : ""
            }
        </div>
    `;

    // Ajout au conteneur
    container.appendChild(alert);

    // Fermeture automatique
    if (autoClose > 0) {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, autoClose);
    }
}

function x_successAlert(
    content,
    autoClose = 5000,
    containerId = "x-alerts-container"
) {
    x_alert(content, "success", containerId, true, autoClose);
}

function x_errorAlert(
    content,
    autoClose = 5000,
    containerId = "x-alerts-container"
) {
    x_alert(content, "danger", containerId, true, autoClose);
}

function x_warningAlert(
    content,
    autoClose = 5000,
    containerId = "x-alerts-container"
) {
    x_alert(content, "warning", containerId, true, autoClose);
}

function x_infoAlert(
    content,
    autoClose = 5000,
    containerId = "x-alerts-container"
) {
    x_alert(content, "info", containerId, true, autoClose);
}
