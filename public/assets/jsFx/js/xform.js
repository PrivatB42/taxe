/**
 * Remplit un formulaire avec des valeurs spécifiques
 * @param {string} formId - ID du formulaire
 * @param {Array} inputsId - Tableau des IDs/names des champs
 * @param {Object} inputsValue - Objet contenant les valeurs {field: value}
 * @param {Object} config - Configuration supplémentaire
 * @param {string} config.form_action - Action du formulaire
 * @param {string} config.form_method - Méthode du formulaire (POST par défaut)
 * @param {function} callback - Fonction à exécuter après remplissage
 */
function x_form_edit(formId, inputsId, inputsValue, config, callback = null) {
    try {
        const form = document.getElementById(formId);
        if (!form) {
            throw new Error(`Formulaire avec l'ID ${formId} non trouvé`);
        }

        // Applique la configuration du formulaire
        if (config.form_action) form.action = config.form_action;
        if (config.form_method) form.method = config.form_method;

        // Traite chaque champ
        inputsId.forEach((inputId) => {
            const field = form.elements[inputId];
            if (!field) {
                console.warn(`Champ ${inputId} non trouvé dans le formulaire`);
                return;
            }

            const value =
                inputsValue[inputId] ??
                inputsValue[inputId.replace(/\[\]$/, "")]; // Gère les names avec []

            // Gestion spéciale pour les selects
            if (field.tagName === "SELECT") {
                const option = field.querySelector(`option[value="${value}"]`);
                if (option) option.selected = true;
                return;
            }

            // Gestion des checkboxes/radios
            if (field.type === "checkbox" || field.type === "radio") {
                if (field.value === value.toString()) {
                    field.checked = true;
                } else {
                    field.checked = false;
                }
                return;
            }

            // Gestion des inputs normaux
            field.value = value !== undefined ? value : "";
        });

        // Exécute le callback si fourni
        callback?.(form);
    } catch (error) {
        console.error("Erreur dans x_form_edit:", error);
        throw error; // Propage l'erreur pour un traitement externe
    }
}

/**
 * Réinitialise un formulaire et optionnellement met à jour son action/méthode
 * @param {string} formId - ID du formulaire à réinitialiser
 * @param {Object} [config={}] - Configuration optionnelle
 * @param {string} [config.form_action=''] - Nouvelle action du formulaire
 * @param {string} [config.form_method='POST'] - Nouvelle méthode du formulaire
 * @param {function} [callback=null] - Callback à exécuter après réinitialisation
 * @throws {Error} Si le formulaire n'est pas trouvé
 */
function x_reset_form(formId, config, callback = null) {
    try {
        // Validation des entrées
        if (!formId || typeof formId !== "string") {
            throw new Error("ID de formulaire invalide");
        }

        const form = document.getElementById(formId);

        if (!form || form.tagName !== "FORM") {
            throw new Error(
                `Formulaire avec l'ID "${formId}" non trouvé ou élément non formulaire`
            );
        }

        // Réinitialisation du formulaire
        form.reset();

        // Mise à jour de la configuration
        if (config.form_action !== undefined) {
            form.action = config.form_action;
        }

        if (config.form_method !== undefined) {
            form.method = config.form_method;
        }

        // Désélectionne explicitement les checkboxes/radios (solution pour certains navigateurs)
        const checkables = form.querySelectorAll(
            'input[type="checkbox"], input[type="radio"]'
        );
        checkables.forEach((el) => {
            el.checked = false;
        });

        // Réinitialise les selects (solution pour Edge/IE)
        const selects = form.querySelectorAll("select");
        selects.forEach((select) => {
            if (select.options.length > 0) {
                select.selectedIndex = 0;
            }
        });

        // Exécution du callback si fourni
        if (typeof callback === "function") {
            callback(form); // On passe le formulaire en paramètre pour plus de flexibilité
        }

        return true; // Retourne true si tout s'est bien passé
    } catch (error) {
        console.error(`Erreur dans x_reset_form (${formId}):`, error);
        throw error; // Propage l'erreur pour permettre la gestion externe
    }
}

/**
 * Gère la soumission asynchrone d'un formulaire avec feedback visuel
 * @param {string} formId - ID du formulaire
 * @param {string} btnId - ID du bouton de soumission
 * @param {object} options - Options supplémentaires
 * @param {string} [options.successCallback] - Nom de la fonction de callback après succès
 * @param {boolean} [options.resetForm=true] - Si le formulaire doit être réinitialisé après succès
 * @param {string} [options.loadingText='Envoi en cours...'] - Texte à afficher pendant l'envoi
 * @param {string} [options.formResetCallback] - Nom de la fonction de callback pour la réinitialisation du formulaire
 */
async function x_form_fetch(formId, btnId, options = {}) {
    const form = document.getElementById(formId);
    const btn = document.getElementById(btnId);
    const originalBtnText = btn.value || btn.textContent;
    const defaultOptions = {
        resetForm: true,
        loadingText: "Traitement en cours...",
        successMessage: "Opération effectué avec succès !",
        errorMessage: "Une erreur s’est produite lors de l’opération.",
        successCallback: null,
        successCallbackArgs: "x_successNotification",
        errorCallbackArgs: "x_errorAlert",
        isLogged: true,
        reload: false,
        headers: {
            Accept: "application/json",
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
        },
    };
    const config = {
        ...defaultOptions,
        ...options,
    };

    if ((!form || !btn) && config.isLogged) {
        console.error("Formulaire ou bouton non trouvé");
        return;
    }

    btn.addEventListener("click", async function (event) {
        event.preventDefault();

        // Feedback visuel
        btn.disabled = true;
        if (btn.tagName === "INPUT") {
            btn.value = config.loadingText;
        } else {
            btn.textContent = config.loadingText;
        }

        try {
            if (form.checkValidity()) {
                const formData = new FormData(form);

                const response = await fetch(form.action, {
                    method: form.method,
                    headers: config.headers,
                    body: formData,
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || "Erreur serveur");
                }

                const result = await response.json();

                if (
                    config.successCallbackArgs &&
                    typeof window[config.successCallbackArgs] === "function"
                ) {
                    window[config.successCallbackArgs](
                        result.message || config.successMessage
                    );
                } else {
                    alert("sucess" + result.message || config.successMessage);
                }

                if (config.isLogged) {
                    console.log("Succès:", result);
                }

                if (config.resetForm) {
                    // Réinitialisation du formulaire
                    if (
                        config.formResetCallback &&
                        typeof window[config.formResetCallback] === "function"
                    ) {
                        window[config.formResetCallback]();
                    } else {
                        form.reset();
                    }
                }

                

                // Callback personnalisé si défini
                if (
                    config.successCallback &&
                    typeof window[config.successCallback] === "function"
                ) { 
                    window[config.successCallback](result);
                } else if (
                    window.DataTableUtils &&
                    window.DataTableUtils[
                        `table-${formId.replace("form-", "")}`
                    ]
                ) {
                    // Rafraîchissement automatique DataTable si disponible
                    window.DataTableUtils[
                        `table-${formId.replace("form-", "")}`
                    ].refreshTable();
                } else {
                    if (config.reload) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                    }
                }
            } else {
                form.reportValidity();
            }
        } catch (error) {
            if (
                config.errorCallbackArgs &&
                typeof window[config.errorCallbackArgs] === "function"
            ) {
                window[config.errorCallbackArgs](
                    error.message || config.errorMessage,
                    0
                );
            } else {
                alert("error " + error.message || config.errorMessage);
            }

            if (config.isLogged) {
                console.error(error);
            }
        } finally {
            // Réinitialisation du bouton
            btn.disabled = false;
            if (btn.tagName === "INPUT") {
                btn.value = originalBtnText;
            } else {
                btn.textContent = originalBtnText;
            }
        }
    });
}
