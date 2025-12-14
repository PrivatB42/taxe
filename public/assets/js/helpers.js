
function optionsDelete() {
    const options = {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
    };

    return options;
}

function optionsPost(data, callback = null) {
    const options = {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            "Content-Type": "application/json"
        },
        body: JSON.stringify(data),
    };

    callback?.(options);

    return options;
}

function optionsPut(data, callback = null) {
    const options = {
        method: "PUT",
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            "Content-Type": "application/json"
        },
        body: JSON.stringify(data),
    };

    callback?.(options);

    return options;
}

function optionsGet() {
    const options = {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
    };

    return options;
}

function configModal(message, data = null, niveau = "center", callback = null) {
    const configModal = {
        title: '<i class="fas fa-check text-success"></i> Confirmation',
        message: message || "",
        buttonAction: {
            color: "success",
            text: "Confirmer",
        },
        niveau: niveau,
        data: data,
    };

    callback?.(configModal);

    return configModal;
}

function configModalDelete(message, data = null, niveau = "center") {
    const configModal = {
        title: '<i class="fas fa-exclamation-triangle text-danger"></i> Confirmer la suppression',
        message: message || "Êtes-vous sûr de vouloir supprimer cet élément ?",
        buttonAction: {
            color: "danger",
            text: "Supprimer",
        },
        niveau: niveau,
        data: data,
    };

    return configModal;
}

function configModalChangeStatut(
    message,
    data = null,
    callback = null,
    niveau = "center"
) {
    const configModal = {
        title: '<i class="fas fa-exclamation-triangle text-danger"></i> Confirmer le changement de statut',
        message:
            message ||
            "Êtes-vous sûr de vouloir changer le statut de cet élément ?",
        buttonAction: {
            color: "primary",
            text: "Confirmer",
        },
        niveau: niveau,
        data: data,
    };

    callback?.(configModal);

    return configModal;
}

function configModalActive(message, data = null, niveau = "center") {
    const configModal = {
        title: '<i class="fas fa-exclamation-triangle text-danger"></i> Confirmer le changement de statut',
        message: message || "Êtes-vous sûr de vouloir activer cet élément ?",
        buttonAction: {
            color: "success",
            text: "Confirmer",
        },
        niveau: niveau,
        data: data,
    };

    return configModal;
}

function configModalDesactive(message, data = null, niveau = "center") {
    const configModal = {
        title: '<i class="fas fa-exclamation-triangle text-danger"></i> Confirmer le changement de statut',
        message: message || "Êtes-vous sûr de vouloir desactiver cet élément ?",
        buttonAction: {
            color: "danger",
            text: "Confirmer",
        },
        niveau: niveau,
        data: data,
    };

    return configModal;
}

function resetImageUpload(name) {
    // Réinitialisation de base
    const elements = {
        fileInput: document.querySelector(`input[name="${name}"]`),
        preview: document.getElementById(`preview_${name}`),
        dropZone: document.getElementById(`drop_${name}`),
        imgPreview: document.getElementById(`img_preview_${name}`),
    };

    // Réinitialiser les éléments visibles
    if (elements.fileInput) elements.fileInput.value = "";
    if (elements.preview) elements.preview.style.display = "none";
    if (elements.dropZone) elements.dropZone.style.display = "block";
    if (elements.imgPreview) {
        elements.imgPreview.innerHTML = "";
        elements.imgPreview.style.backgroundImage = "";
    }

    // Nettoyage avancé
    cleanupAdvanced(name);
}

function cleanupAdvanced(name) {
    // Cropper.js
    if (window.croppers && window.croppers[name]) {
        window.croppers[name].destroy();
        delete window.croppers[name];
    }

    // Supprimer les éléments temporaires
    ["tempCanvas", "cropResult", "originalImage"].forEach((prefix) => {
        const el = document.getElementById(`${prefix}_${name}`);
        if (el) el.remove();
    });

    // Réinitialiser les données
    if (window.uploadData) {
        delete window.uploadData[name];
    }
}

function confirmModal(config, callback) {
    const defaultConfig = {
        title: "Confirmation",
        message: "Etes-vous sur de vouloir continuer ?",
        buttonAction: { color: "primary", text: "Confirmer" },
        niveau: "top",
        data: null,
    };

    const { title, message, buttonAction, niveau, data } = {
        ...defaultConfig,
        ...config,
    };
    const modalId = "confirm-modal-" + Date.now();
    const modal = $(`
            <div class="modal fade" id="${modalId}" tabindex="-1">
                <div class="modal-dialog ${
                    niveau === "center" ? "modal-dialog-centered" : ""
                }">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body"><p>${message}</p></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="button" class="btn btn-${
                                buttonAction.color
                            } confirm-action">${buttonAction.text}</button>
                        </div>
                    </div>
                </div>
            </div>
        `);

    $("body").append(modal);
    const modalInstance = new bootstrap.Modal(modal[0]);
    modalInstance.show();

    modal.on("click", ".confirm-action", function () {
        if (typeof callback === "function") {
            callback(data);
        }
        modalInstance.hide();
    });

    modal.on("hidden.bs.modal", function () {
        modal.remove();
    });
}

/*function FormData(form) {
    const formData = new FormData(form);
    formData.append("_method", "PUT");
    return formData;
}*/

function type_reduction(type) {
    if (type === "pourcentage") {
        return "%";
    }
    return null;
}

function renderColor(colors, body = false) {
    if (!colors || !Array.isArray(colors) || colors.length === 0) {
        return "";
    }

    // Si une seule couleur
    if (colors.length === 1) {
        const color = colors[0];
        if (body) {
            return `<div style="background: ${color}; width: 25px; height: 25px; border-radius: 50%; margin-right: 8px; border: 1px solid #808080ff"></div>`;
        }
        return color;
    }

    // Si plusieurs couleurs - créer une palette dégradé
    const gradient = colors
        .map((color, index) => {
            const percentage = (index / (colors.length - 1)) * 100;
            return `${color} ${percentage}%`;
        })
        .join(", ");

    if (body) {
        return `<div style="background: linear-gradient(90deg, ${gradient}); width: 25px; height: 25px; border-radius: 50%; margin-right: 8px; border: 1px solid #808080ff"></div>`;
    }

    return `linear-gradient(90deg, ${gradient})`;
}

function traiteVolume() {
    const longueur = document.getElementById("longueur").value;
    const largeur = document.getElementById("largeur").value;
    const hauteur = document.getElementById("hauteur").value;

    const volumeInput = document.getElementById("volume");

    console.log(longueur, largeur, hauteur);

    if (longueur && largeur && hauteur) {
        const volume = longueur * largeur * hauteur;
        volumeInput.value = volume;
        volumeInput.readOnly = true;
        volumeInput.style.color = "green";
    } else {
        volumeInput.value = "";
        volumeInput.readOnly = false;
        volumeInput.style.color = "black";
    }
}

function badgeColoStatut(statut) {
    let color = "";
    switch (statut) {
        case "actif":
            color = "success";
            break;
        case "inactif":
            color = "secondary";
            break;
        case "pending":
            color = "warning";
            break;
        case "blocked":
            color = "danger";
            break;
        case "brouillon":
            color = "info";
            break;
        case "valider":
            color = "success";
            break;
        case "refuser":
            color = "danger";
            break;
        case "en_attente":
            color = "secondary";
            break;
        case "en_cours":
            color = "info";
            break;
        case "termine":
            color = "success";
            break;
        case "annuler":
            color = "danger";
            break;
        case "a_la_cnps":
            color = "warning";
            break;
        case "cloturer":
            color = "success";
            break;
        case "rejeter":
            color = "danger";
            break;
        default:
            color = "secondary";
            break;
    }
    return `<span class="badge bg-${color}">${statut}</span>`;
}


function image_profile(url, name = "", width = "50px", height = "50px") {
    return `<span class="d-flex align-items-center"><img src="${url}" alt="${name}" width="${width}" height="${height}" class="card ms-2"> <span class="ms-2">${name}</span> </span>`;
}

function array_number_range(min, max) {
  return Array.from({ length: max - min + 1 }, (_, i) => min + i);
}