class FormValidator {
    constructor() {
        this.fieldLabels = {
            'nom': 'le nom',
            'prenom': 'le prénom',
            'email': 'l\'email',
            'password': 'le mot de passe',
            'confirm_password': 'la confirmation du mot de passe',
            'telephone': 'le téléphone',
            'adresse': 'l\'adresse',
            'ville': 'la ville',
            'code_postal': 'le code postal',
            'pays': 'le pays',
            'date_naissance': 'la date de naissance'
        };

        this.messages = {
            'required': "Le champ {field} est obligatoire.",
            'min': "Le champ {field} doit contenir au moins {value} caractères.",
            'max': "Le champ {field} ne peut pas dépasser {value} caractères.",
            'email': "Le champ {field} doit être une adresse email valide.",
            'numeric': "Le champ {field} doit être un nombre.",
            'integer': "Le champ {field} doit être un entier.",
            'float': "Le champ {field} doit être un nombre décimal.",
            'url': "Le champ {field} doit être une URL valide.",
            'date': "Le champ {field} doit être une date valide.",
            'start': "Le champ {field} doit être postérieur au {value}.",
            'end': "Le champ {field} doit être antérieur au {value}.",
            'regex': "Le format du champ {field} est invalide.",
            'in': "Le champ {field} doit être parmi les valeurs : {value}.",
            'not_in': "Le champ {field} ne doit pas être parmi les valeurs : {value}.",
            'same': "Le champ {field} doit correspondre au champ {value}.",
            'different': "Le champ {field} doit être différent du champ {value}.",
            'between': "Le champ {field} doit être entre {value}.",
            'digits': "Le champ {field} doit contenir exactement {value} chiffres.",
            'digits_between': "Le champ {field} doit contenir entre {value} chiffres."
        };
    }

    validate(stepData, stepRules) {
        const errors = {};
        
        for (const [field, rules] of Object.entries(stepRules)) {
            const value = stepData[field] || '';
            const rulesArray = Array.isArray(rules) ? rules : rules.split('|');
            
            for (const rule of rulesArray) {
                const [ruleName, ruleValue] = rule.split(':');
                
                if (!this.validateRule(value, ruleName, ruleValue, stepData)) {
                    errors[field] = this.getMessage(field, ruleName, ruleValue);
                    break; // Stop à la première erreur
                }
            }
        }
        
        return errors;
    }

    validateRule(value, ruleName, ruleValue, allData = {}) {
        switch (ruleName) {
            case 'required':
                return value !== null && value !== undefined && value.toString().trim() !== '';
            
            case 'min':
                return value.toString().length >= parseInt(ruleValue);
            
            case 'max':
                return value.toString().length <= parseInt(ruleValue);
            
            case 'email':
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(value);
            
            case 'numeric':
                return !isNaN(parseFloat(value)) && isFinite(value);
            
            case 'integer':
                return Number.isInteger(Number(value));
            
            case 'float':
                return !isNaN(parseFloat(value));
            
            case 'url':
                try {
                    new URL(value);
                    return true;
                } catch {
                    return false;
                }
            
            case 'date':
                return !isNaN(Date.parse(value));
            
            case 'start':
                return new Date(value) >= new Date(ruleValue);
            
            case 'end':
                return new Date(value) <= new Date(ruleValue);
            
            case 'regex':
                return new RegExp(ruleValue).test(value);
            
            case 'in':
                return ruleValue.split(',').includes(value.toString());
            
            case 'not_in':
                return !ruleValue.split(',').includes(value.toString());
            
            case 'same':
                return value === allData[ruleValue];
            
            case 'different':
                return value !== allData[ruleValue];
            
            case 'between':
                const [min, max] = ruleValue.split(',').map(Number);
                const numValue = Number(value);
                return numValue >= min && numValue <= max;
            
            case 'digits':
                return /^\d+$/.test(value) && value.length === parseInt(ruleValue);
            
            case 'digits_between':
                const [minDigits, maxDigits] = ruleValue.split(',').map(Number);
                return /^\d+$/.test(value) && value.length >= minDigits && value.length <= maxDigits;
            
            default:
                return true;
        }
    }

    getMessage(field, ruleName, ruleValue) {
        const fieldLabel = this.fieldLabels[field] || field;
        let message = this.messages[ruleName] || "Erreur de validation pour le champ {field}.";
        
        return message
            .replace('{field}', fieldLabel)
            .replace('{value}', ruleValue);
    }

    setCustomMessages(messages) {
        this.messages = { ...this.messages, ...messages };
    }

    setFieldLabels(labels) {
        this.fieldLabels = { ...this.fieldLabels, ...labels };
    }

    displayErrors(errors, container) {
        // Vider les erreurs précédentes
        container.innerHTML = '';
        
        // Afficher les nouvelles erreurs
        for (const [field, message] of Object.entries(errors)) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-danger';
            errorDiv.textContent = message;
            errorDiv.setAttribute('data-field', field);
            container.appendChild(errorDiv);
        }
    }

    markInvalidFields(errors) {
        // Réinitialiser tous les champs
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        
        // Marquer les champs invalides
        for (const field of Object.keys(errors)) {
            const input = document.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');
            }
        }
    }
}

// // Utilisation dans votre formulaire multi-step
// const validator = new FormValidator();

// function validateCurrentStep() {
//     const currentStepElement = document.getElementById('step_' + currentStep);
//     const inputs = currentStepElement.querySelectorAll('input, select, textarea');
//     const stepData = {};
    
//     // Récupérer les données du step
//     inputs.forEach(input => {
//         stepData[input.name] = input.value;
//     });
    
//     // Définir les règles de validation (exemple)
//     const stepRules = {
//         'nom': 'required|min:2|max:50',
//         'email': 'required|email',
//         'password': 'required|min:8|max:20',
//         'age': 'required|numeric|between:18,99'
//     };
    
//     // Valider
//     const errors = validator.validate(stepData, stepRules);
    
//     if (Object.keys(errors).length > 0) {
//         // Afficher les erreurs
//         const errorContainer = document.getElementById('error-container');
//         validator.displayErrors(errors, errorContainer);
//         validator.markInvalidFields(errors);
//         return false;
//     }
    
//     return true;
// }

// // Exemple d'utilisation avec votre formulaire
// document.querySelectorAll('.next-step').forEach(button => {
//     button.addEventListener('click', function() {
//         if (validateCurrentStep()) {
//             // Passer à l'étape suivante
//             currentStep++;
//             showStep(currentStep);
//             updateProgress();
//             updateButtons();
//         }
//     });
// });