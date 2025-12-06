<?php

namespace App\PhpFx\Facades\Form;

use function Laravel\Prompts\alert;

/**
 * FormFx - Classe statique pour des composants de formulaires avancés et modernes
 * Complément à XForm avec des fonctionnalités interactives et des templates réutilisables
 */
class AdvanceForm
{
    private static $defaultConfig = [
        'bootstrap' => true,
        'theme' => 'modern',
        'ajax' => true,
        'cdn' => [
            'bootstrap' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0',
            'jquery' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js',
            'sweetalert' => 'https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.0',
            'croppie' => 'https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5',
            'fontawesome' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'
        ]
    ];

    // ============ MÉTHODES PRINCIPALES ============

    /**
     * Génère les ressources CSS/JS nécessaires
     */
    public static function assets($config = [])
    {
        $config = array_merge(self::$defaultConfig, $config);
        $cdn = $config['cdn'];
        
        $html = "\n<!-- FormFx Assets -->\n";
        
        if ($config['bootstrap']) {
            $html .= '<link href="' . $cdn['bootstrap'] . '/css/bootstrap.min.css" rel="stylesheet">' . "\n";
            $html .= '<script src="' . $cdn['bootstrap'] . '/js/bootstrap.bundle.min.js"></script>' . "\n";
        }

    
        $html .= '<link href="' . $cdn['fontawesome'] . '" rel="stylesheet">' . "\n";
       // $html .= '<script src="' . $cdn['jquery'] . '"></script>' . "\n";
        $html .= '<link href="' . $cdn['sweetalert'] . '/sweetalert2.min.css" rel="stylesheet">' . "\n";
        $html .= '<script src="' . $cdn['sweetalert'] . '/sweetalert2.all.min.js"></script>' . "\n";
        $html .= '<link href="' . $cdn['croppie'] . '/croppie.css" rel="stylesheet">' . "\n";
        $html .= '<script src="' . $cdn['croppie'] . '/croppie.min.js"></script>' . "\n";


        // jQuery (version plus récente)
    // $html .= '<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>' . "\n";
    
    // Bootstrap 5
    $html .= '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">' . "\n";
    $html .= '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>' . "\n";

            // Ajouts pour Summernote (éditeur de texte)
        $html .= '<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs5.min.css" rel="stylesheet">' . "\n";
        $html .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs5.min.js" defer></script>' . "\n";
        $html .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/lang/summernote-fr-FR.min.js" defer></script>' . "\n";


        // Flatpickr (Datepicker)
    $html .= '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">' . "\n";
    $html .= '<script src="https://cdn.jsdelivr.net/npm/flatpickr" defer></script>' . "\n";
    $html .= '<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js" defer></script>' . "\n";


    // Bootstrap Switch
    $html .= '<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/css/bootstrap3/bootstrap-switch.min.css" rel="stylesheet">' . "\n";
    $html .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/js/bootstrap-switch.min.js" defer></script>' . "\n";
    

   
        
        $html .= self::getCustomStyles();
        $html .= self::getCustomScripts();
        
        return $html;
    }

    /**
     * Input text avec fonctionnalités avancées
     */
    public static function text($name, $label, $options = [])
    {
        $options = array_merge([
            'class' => 'form-control',
            'placeholder' => '',
            'icon' => null,
            'help' => null,
            'validation' => [],
            'wrapper_class' => 'mb-3',
            'type' => 'text'
        ], $options);

        $id = $options['id'] ?? $name;
        $value = $options['value'] ?? '';
        $required = in_array('required', $options['validation']) ? 'required' : '';
        
        $html = '<div class="' . $options['wrapper_class'] . '">';
        $html .= '<label for="' . $id . '" class="form-label">' . $label . '</label>';
        
        if ($options['icon']) {
            $html .= '<div class="input-group">';
            $html .= '<span class="input-group-text"><i class="' . $options['icon'] . '"></i></span>';
        }
        
        $html .= '<input type="' . $options['type'] . '" name="' . $name . '" id="' . $id . '" class="' . $options['class'] . '"';
        $html .= ' placeholder="' . htmlspecialchars($options['placeholder']) . '"';
        $html .= ' value="' . htmlspecialchars($value) . '" ' . $required . '>';
        
        if ($options['icon']) {
            $html .= '</div>';
        }
        
        if ($options['help']) {
            $html .= '<div class="form-text">' . $options['help'] . '</div>';
        }
        
        $html .= '<div class="invalid-feedback"></div>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Select multiple avec badges
     */
    public static function selectMultipleBadges($name, $label, $options = [], $config = [])
    {
        $config = array_merge([
            'placeholder' => 'Sélectionnez...',
            'max_items' => null,
            'badge_class' => 'bg-primary',
            'wrapper_class' => 'mb-3',
            'search' => true
        ], $config);

        $id = $name . '_select';
        
        $html = '<div class="' . $config['wrapper_class'] . '">';
        $html .= '<label class="form-label">' . $label . '</label>';
        $html .= '<div class="select-multiple-badges" data-name="' . $name . '" data-max="' . $config['max_items'] . '">';
        
        // Zone des badges sélectionnés
        $html .= '<div class="selected-badges mb-2" id="badges_' . $name . '"></div>';
        
        // Select pour choisir
        $html .= '<select class="form-select badges-select" id="' . $id . '">';
        $html .= '<option value="">' . $config['placeholder'] . '</option>';
        foreach ($options as $value => $text) {
            $html .= '<option value="' . htmlspecialchars($value) . '">' . htmlspecialchars($text) . '</option>';
        }
        $html .= '</select>';
        
        // Input hidden pour stocker les valeurs
        $html .= '<input type="hidden" name="' . $name . '" id="hidden_' . $name . '">';
        
        $html .= '</div>';
        $html .= '</div>';
        
        // Script pour la fonctionnalité
        $html .= self::getSelectMultipleBadgesScript($name);
        
        return $html;
    }

    /**
     * Upload d'image avec preview et crop
     */
    public static function imageUpload($name, $label, $options = [])
    {
        $options = array_merge([
            'max_size' => '5MB',
            'allowed_types' => ['jpg', 'jpeg', 'png', 'gif'],
            'crop' => false,
            'crop_ratio' => 1, // 1:1 carré
            'preview_size' => 200,
            'wrapper_class' => 'mb-3',
            'required' => false
        ], $options);

        $id = $name . '_upload';
        
        $html = '<div class="' . $options['wrapper_class'] . '">';
        $html .= '<label class="form-label">' . $label . '</label>';
        $html .= '<div class="image-upload-container" data-name="' . $name . '">';
        
        // Zone de drop
        $html .= '<div class="image-drop-zone" id="drop_' . $name . '">';
        $html .= '<div class="drop-zone-content">';
        $html .= '<i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>';
        $html .= '<p class="mb-0">Glissez votre image ici ou cliquez pour sélectionner</p>';
        $html .= '<small class="text-muted">Formats: ' . implode(', ', $options['allowed_types']) . ' - Max: ' . $options['max_size'] . '</small>';
        $html .= '</div>';
        $html .= '<input type="file" id="' . $id . '" name="' . $name . '"  accept="image/*" style="display:none;" ' . ($options['required'] ? 'required' : '') . '>';
        $html .= '</div>';
        
        // Preview et contrôles
        $html .= '<div class="image-preview-container" id="preview_' . $name . '" style="display:none;">';
        $html .= '<div class="image-preview" id="img_preview_' . $name . '"></div>';
        
       
            $html .= '<div class="crop-controls mt-3">';
             if ($options['crop']) {
            $html .= '<button type="button" class="btn btn-sm btn-primary crop-btn" data-target="' . $name . '">Recadrer</button>';
             }
            $html .= '<button type="button" class="btn btn-sm btn-danger remove-btn" data-target="' . $name . '">Supprimer</button>';
            $html .= '</div>';
       
        
        $html .= '</div>';
        
        // Modal de crop si activé
        if ($options['crop']) {
            $html .= self::getCropModal($name, $options['crop_ratio']);
        }
        
        $html .= '</div>';
        $html .= '</div>';
        
        // Script pour la fonctionnalité
        $html .= self::getImageUploadScript($name, $options);
        
        return $html;
    }

    /**
     * Upload de fichiers avec preview
     */
    public static function fileUpload($name, $label, $options = [])
    {
        $options = array_merge([
            'max_size' => '10MB',
            'allowed_types' => ['pdf', 'mp4', 'avi', 'mov'],
            'multiple' => false,
            'preview' => true,
            'wrapper_class' => 'mb-3'
        ], $options);

        $id = $name . '_file_upload';
        $multiple = $options['multiple'] ? 'multiple' : '';
        $nameAttr = $options['multiple'] ? $name . '[]' : $name;
        
        $html = '<div class="' . $options['wrapper_class'] . '">';
        $html .= '<label class="form-label">' . $label . '</label>';
        $html .= '<div class="file-upload-container" data-name="' . $name . '">';
        
        // Zone de drop
        $html .= '<div class="file-drop-zone" id="file_drop_' . $name . '">';
        $html .= '<div class="drop-zone-content">';
        $html .= '<i class="fas fa-file-upload fa-3x text-muted mb-3"></i>';
        $html .= '<p class="mb-0">Glissez vos fichiers ici ou cliquez pour sélectionner</p>';
        $html .= '<small class="text-muted">Formats: ' . implode(', ', $options['allowed_types']) . ' - Max: ' . $options['max_size'] . '</small>';
        $html .= '</div>';
        $html .= '<input type="file" id="' . $id . '" name="' . $nameAttr . '" ' . $multiple;
        $html .= ' accept="' . self::getAcceptTypes($options['allowed_types']) . '" style="display:none;">';
        $html .= '</div>';
        
        // Liste des fichiers
        $html .= '<div class="file-list mt-3" id="file_list_' . $name . '"></div>';
        
        $html .= '</div>';
        $html .= '</div>';
        
        // Script pour la fonctionnalité
        $html .= self::getFileUploadScript($name, $options);
        
        return $html;
    }

    /**
     * Formulaire multi-étapes avec steps
     */
    public static function multiStepForm($formId, $steps, $options = [])
    {
        $options = array_merge([
            'show_progress' => true,
            'validate_step' => true,
            'ajax_submit' => true,
            'success_redirect' => null,
            'wrapper_class' => 'multi-step-form',
            'action' => '',
            'method' => 'POST'
        ], $options);

        $html = '<div class="' . $options['wrapper_class'] . '" id="' . $formId . '">';
        
        // Barre de progression
        if ($options['show_progress']) {
            $html .= '<div class="step-progress mb-4">';
            $html .= '<div class="progress">';
            $html .= '<div class="progress-bar" role="progressbar" style="width: ' . (100/count($steps)) . '%"></div>';
            $html .= '</div>';
            $html .= '<div class="step-labels mt-2">';
            foreach ($steps as $index => $step) {
                $active = $index === 0 ? 'active' : '';
                $html .= '<span class="step-label ' . $active . '">' . $step['title'] . '</span>';
            }
            $html .= '</div>';
            $html .= '</div>';
        }
        
        $html .= '<form id="form_' . $formId . '" class="multi-step-form-content" action="' . $options['action'] . '" method="' . $options['method'] . '">';
        
        // Génération des steps
        foreach ($steps as $index => $step) {
            $display = $index === 0 ? 'block' : 'none';
            $html .= '<div class="step" id="step_' . $index . '" style="display: ' . $display . ';">';
            $html .= '<h4>' . $step['title'] . '</h4>';
            if (isset($step['description'])) {
                $html .= '<p class="text-muted">' . $step['description'] . '</p>';
            }
            $html .= $step['content'];
            $html .= '</div>';
        }
        
        // Boutons de navigation
        $html .= '<div class="step-navigation mt-4">';
        $html .= '<button type="button" class="btn btn-secondary prev-step" style="display:none;">Précédent</button>';
        $html .= '<button type="button" class="btn btn-primary next-step">Suivant</button>';
        $html .= '<button type="submit" class="btn btn-success submit-form" style="display:none;">Envoyer</button>';
        $html .= '</div>';
        
        $html .= '</form>';
        $html .= '</div>';
        
        // Script pour la fonctionnalité multi-steps
        $html .= self::getMultiStepScript($formId, count($steps), $options);
        
        return $html;
    }

    /**
     * Input avec autocomplétion
     */
    public static function inputList($name, $label, $dataSource, $options = [])
    {
        $options = array_merge([
            'placeholder' => 'Tapez pour rechercher...',
            'min_chars' => 2,
            'max_results' => 10,
            'ajax_url' => null,
            'wrapper_class' => 'mb-3'
        ], $options);

        $id = $name . '_input_list';
        
        $html = '<div class="' . $options['wrapper_class'] . '">';
        $html .= '<label for="' . $id . '" class="form-label">' . $label . '</label>';
        $html .= '<div class="input-list-container position-relative">';
        
        $html .= '<input type="text" name="' . $name . '" id="' . $id . '" class="form-control input-list"';
        $html .= ' placeholder="' . htmlspecialchars($options['placeholder']) . '"';
        $html .= ' autocomplete="off">';
        
        // Liste déroulante
        $html .= '<div class="input-list-dropdown" id="dropdown_' . $name . '"></div>';
        
        $html .= '</div>';
        $html .= '</div>';
        
        // Script pour l'autocomplétion
        $html .= self::getInputListScript($name, $dataSource, $options);
        
        return $html;
    }

    /**
     * Widget de notation par étoiles
     */
    public static function starRating($name, $label, $options = [])
    {
        $options = array_merge([
            'max_stars' => 5,
            'value' => 0,
            'readonly' => false,
            'wrapper_class' => 'mb-3',
            'size' => 'normal' // small, normal, large
        ], $options);

        $sizeClass = $options['size'] === 'large' ? 'fa-2x' : ($options['size'] === 'small' ? 'fa-sm' : '');
        
        $html = '<div class="' . $options['wrapper_class'] . '">';
        $html .= '<label class="form-label">' . $label . '</label>';
        $html .= '<div class="star-rating" data-name="' . $name . '" data-readonly="' . ($options['readonly'] ? 'true' : 'false') . '">';
        
        for ($i = 1; $i <= $options['max_stars']; $i++) {
            $active = $i <= $options['value'] ? 'active' : '';
            $html .= '<i class="fas fa-star star ' . $sizeClass . ' ' . $active . '" data-value="' . $i . '"></i>';
        }
        
        $html .= '<input type="hidden" name="' . $name . '" value="' . $options['value'] . '">';
        $html .= '</div>';
        $html .= '</div>';
        
        // Script pour la fonctionnalité
        $html .= self::getStarRatingScript($name);
        
        return $html;
    }

    /**
     * Composant de notation et avis
     */
    public static function reviewForm($name, $config = [])
    {
        $config = array_merge([
            'title' => 'Laisser un avis',
            'show_rating' => true,
            'show_photos' => true,
            'form_id' => $name . '_review_form'
        ], $config);

        $html = '<div class="review-form-container">';
        $html .= '<h5>' . $config['title'] . '</h5>';
        $html .= '<form id="' . $config['form_id'] . '">';
        
        if ($config['show_rating']) {
            $html .= self::starRating($name . '_rating', 'Note globale', [
                'max_stars' => 5,
                'value' => 0
            ]);
        }
        
        $html .= self::text($name . '_title', 'Titre de l\'avis', [
            'placeholder' => 'Résumez votre expérience...',
            'validation' => ['required']
        ]);
        
        $html .= '<div class="mb-3">';
        $html .= '<label class="form-label">Votre avis</label>';
        $html .= '<textarea name="' . $name . '_comment" class="form-control" rows="5" placeholder="Décrivez votre expérience..." required></textarea>';
        $html .= '<div class="invalid-feedback"></div>';
        $html .= '</div>';
        
        if ($config['show_photos']) {
            $html .= self::fileUpload($name . '_photos', 'Photos (optionnelles)', [
                'allowed_types' => ['jpg', 'jpeg', 'png'],
                'multiple' => true,
                'max_size' => '5MB'
            ]);
        }
        
        $html .= self::text($name . '_author', 'Votre nom', [
            'placeholder' => 'Nom affiché publiquement',
            'validation' => ['required']
        ]);
        
        $html .= '<button type="submit" class="btn btn-primary">';
        $html .= '<i class="fas fa-star me-2"></i>Publier l\'avis';
        $html .= '</button>';
        
        $html .= '</form>';
        $html .= '</div>';
        
        return $html;
    }

    // ============ TEMPLATES DE FORMULAIRES ============

    /**
     * Template de formulaire de contact
     */
    public static function contactTemplate($config = [])
    {
        $config = array_merge([
            'title' => 'Nous contacter',
            'show_subject' => true,
            'show_phone' => true,
            'form_id' => 'contact_form',
            'action' => '/contact',
            'method' => 'POST'
        ], $config);

        $html = '<div class="contact-form-container">';
        $html .= '<h3>' . $config['title'] . '</h3>';
        $html .= '<form id="' . $config['form_id'] . '" action="' . $config['action'] . '" method="' . $config['method'] . '">';
        
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">' . self::text('first_name', 'Prénom', [
            'icon' => 'fas fa-user',
            'validation' => ['required']
        ]) . '</div>';
        $html .= '<div class="col-md-6">' . self::text('last_name', 'Nom', [
            'icon' => 'fas fa-user',
            'validation' => ['required']
        ]) . '</div>';
        $html .= '</div>';
        
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">' . self::text('email', 'Email', [
            'type' => 'email',
            'icon' => 'fas fa-envelope',
            'validation' => ['required']
        ]) . '</div>';
        
        if ($config['show_phone']) {
            $html .= '<div class="col-md-6">' . self::text('phone', 'Téléphone', [
                'type' => 'tel',
                'icon' => 'fas fa-phone'
            ]) . '</div>';
        }
        $html .= '</div>';
        
        if ($config['show_subject']) {
            $html .= self::text('subject', 'Sujet', [
                'icon' => 'fas fa-tag',
                'validation' => ['required']
            ]);
        }
        
        $html .= '<div class="mb-3">';
        $html .= '<label class="form-label">Message</label>';
        $html .= '<textarea name="message" class="form-control" rows="5" placeholder="Votre message..." required></textarea>';
        $html .= '<div class="invalid-feedback"></div>';
        $html .= '</div>';
        
        $html .= '<button type="submit" class="btn btn-primary">';
        $html .= '<i class="fas fa-paper-plane me-2"></i>Envoyer le message';
        $html .= '</button>';
        
        $html .= '</form>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Template de formulaire d'inscription
     */
    public static function registrationTemplate($config = [])
    {
        $config = array_merge([
            'title' => 'Créer un compte',
            'show_terms' => true,
            'show_newsletter' => true,
            'form_id' => 'registration_form',
            'action' => '/register',
            'method' => 'POST'
        ], $config);

        $html = '<div class="registration-form-container">';
        $html .= '<h3>' . $config['title'] . '</h3>';
        $html .= '<form id="' . $config['form_id'] . '" action="' . $config['action'] . '" method="' . $config['method'] . '">';
        
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">' . self::text('first_name', 'Prénom', [
            'icon' => 'fas fa-user',
            'validation' => ['required']
        ]) . '</div>';
        $html .= '<div class="col-md-6">' . self::text('last_name', 'Nom', [
            'icon' => 'fas fa-user',
            'validation' => ['required']
        ]) . '</div>';
        $html .= '</div>';
        
        $html .= self::text('email', 'Email', [
            'type' => 'email',
            'icon' => 'fas fa-envelope',
            'validation' => ['required']
        ]);
        
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">' . self::text('password', 'Mot de passe', [
            'type' => 'password',
            'icon' => 'fas fa-lock',
            'validation' => ['required']
        ]) . '</div>';
        $html .= '<div class="col-md-6">' . self::text('password_confirmation', 'Confirmer mot de passe', [
            'type' => 'password',
            'icon' => 'fas fa-lock',
            'validation' => ['required']
        ]) . '</div>';
        $html .= '</div>';
        
        if ($config['show_terms']) {
            $html .= '<div class="form-check mb-3">';
            $html .= '<input type="checkbox" name="terms" class="form-check-input" required>';
            $html .= '<label class="form-check-label">J\'accepte les conditions d\'utilisation</label>';
            $html .= '</div>';
        }
        
        if ($config['show_newsletter']) {
            $html .= '<div class="form-check mb-3">';
            $html .= '<input type="checkbox" name="newsletter" class="form-check-input">';
            $html .= '<label class="form-check-label">Je souhaite recevoir la newsletter</label>';
            $html .= '</div>';
        }
        
        $html .= '<button type="submit" class="btn btn-primary w-100">';
        $html .= '<i class="fas fa-user-plus me-2"></i>Créer mon compte';
        $html .= '</button>';
        
        $html .= '</form>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Template de formulaire de profil
     */
    public static function profileTemplate($config = [])
    {
        $config = array_merge([
            'title' => 'Mon Profil',
            'show_avatar' => true,
            'show_social' => true,
            'form_id' => 'profile_form',
            'action' => '/profile/update',
            'method' => 'POST'
        ], $config);

        $html = '<div class="profile-form-container">';
        $html .= '<h3>' . $config['title'] . '</h3>';
        $html .= '<form id="' . $config['form_id'] . '" action="' . $config['action'] . '" method="' . $config['method'] . '">';
        
        if ($config['show_avatar']) {
            $html .= '<div class="text-center mb-4">';
            $html .= self::imageUpload('avatar', 'Photo de profil', [
                'crop' => true,
                'crop_ratio' => 1,
                'preview_size' => 150
            ]);
            $html .= '</div>';
        }
        
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">' . self::text('first_name', 'Prénom', [
            'icon' => 'fas fa-user',
            'validation' => ['required']
        ]) . '</div>';
        $html .= '<div class="col-md-6">' . self::text('last_name', 'Nom', [
            'icon' => 'fas fa-user',
            'validation' => ['required']
        ]) . '</div>';
        $html .= '</div>';
        
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">' . self::text('email', 'Email', [
            'type' => 'email',
            'icon' => 'fas fa-envelope',
            'validation' => ['required']
        ]) . '</div>';
        $html .= '<div class="col-md-6">' . self::text('phone', 'Téléphone', [
            'type' => 'tel',
            'icon' => 'fas fa-phone'
        ]) . '</div>';
        $html .= '</div>';
        
        $html .= '<div class="mb-3">';
        $html .= '<label class="form-label">Date de naissance</label>';
        $html .= '<div class="input-group">';
        $html .= '<span class="input-group-text"><i class="fas fa-calendar"></i></span>';
        $html .= '<input type="date" name="birthdate" class="form-control">';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '<div class="mb-3">';
        $html .= '<label class="form-label">Biographie</label>';
        $html .= '<textarea name="bio" class="form-control" rows="4" placeholder="Parlez-nous de vous..."></textarea>';
        $html .= '</div>';
        
        if ($config['show_social']) {
            $html .= '<h5 class="mt-4 mb-3"><i class="fas fa-share-alt me-2"></i>Réseaux sociaux</h5>';
            $html .= '<div class="row">';
            $html .= '<div class="col-md-6">' . self::text('github', 'GitHub', [
                'icon' => 'fab fa-github',
                'placeholder' => 'https://github.com/votrenom'
            ]) . '</div>';
            $html .= '<div class="col-md-6">' . self::text('website', 'Site web', [
                'icon' => 'fas fa-globe',
                'placeholder' => 'https://votre-site.com'
            ]) . '</div>';
            $html .= '</div>';
            $html .= '<div class="row">';
            $html .= '<div class="col-md-6">' . self::text('linkedin', 'LinkedIn', [
                'icon' => 'fab fa-linkedin',
                'placeholder' => 'https://linkedin.com/in/votreprofil'
            ]) . '</div>';
            $html .= '<div class="col-md-6">' . self::text('twitter', 'Twitter', [
                'icon' => 'fab fa-twitter',
                'placeholder' => 'https://twitter.com/votrenom'
            ]) . '</div>';
            $html .= '</div>';
        }
        
        $html .= '<div class="d-flex gap-2 mt-4">';
        $html .= '<button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Sauvegarder</button>';
        $html .= '<button type="button" class="btn btn-secondary" onclick="history.back()"><i class="fas fa-arrow-left me-2"></i>Retour</button>';
        $html .= '</div>';
        
        $html .= '</form>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Template de formulaire de produit
     */
    public static function productTemplate($config = [])
    {
        $config = array_merge([
            'title' => 'Ajouter un produit',
            'show_gallery' => true,
            'show_variations' => true,
            'form_id' => 'product_form',
            'action' => '/products',
            'method' => 'POST'
        ], $config);

        $html = '<div class="product-form-container">';
        $html .= '<h3>' . $config['title'] . '</h3>';
        $html .= '<form id="' . $config['form_id'] . '" action="' . $config['action'] . '" method="' . $config['method'] . '">';
        
        $html .= '<div class="row">';
        $html .= '<div class="col-md-8">';
        
        $html .= self::text('name', 'Nom du produit', [
            'validation' => ['required'],
            'icon' => 'fas fa-box',
            'placeholder' => 'Nom de votre produit'
        ]);
        
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">' . self::text('sku', 'SKU/Référence', [
            'icon' => 'fas fa-barcode',
            'placeholder' => 'REF-001'
        ]) . '</div>';
        $html .= '<div class="col-md-6">' . self::text('brand', 'Marque', [
            'icon' => 'fas fa-tag',
            'placeholder' => 'Nom de la marque'
        ]) . '</div>';
        $html .= '</div>';
        
        $html .= '<div class="mb-3">';
        $html .= '<label class="form-label">Description courte</label>';
        $html .= '<textarea name="short_description" class="form-control" rows="2" placeholder="Description courte du produit..."></textarea>';
        $html .= '</div>';
        
        $html .= '<div class="mb-3">';
        $html .= '<label class="form-label">Description détaillée</label>';
        $html .= '<textarea name="description" class="form-control" rows="5" placeholder="Description complète du produit..." required></textarea>';
        $html .= '<div class="invalid-feedback"></div>';
        $html .= '</div>';
        
        $html .= '</div>';
        $html .= '<div class="col-md-4">';
        
        $html .= '<div class="card">';
        $html .= '<div class="card-header"><h6 class="mb-0"><i class="fas fa-euro-sign me-2"></i>Prix et stock</h6></div>';
        $html .= '<div class="card-body">';
        $html .= self::text('price', 'Prix (€)', [
            'type' => 'number',
            'icon' => 'fas fa-euro-sign',
            'validation' => ['required'],
            'placeholder' => '0.00'
        ]);
        $html .= self::text('compare_price', 'Prix barré (€)', [
            'type' => 'number',
            'icon' => 'fas fa-percentage',
            'placeholder' => '0.00'
        ]);
        $html .= self::text('stock', 'Stock', [
            'type' => 'number',
            'icon' => 'fas fa-boxes',
            'validation' => ['required'],
            'placeholder' => '0'
        ]);
        $html .= '<div class="form-check">';
        $html .= '<input type="checkbox" name="manage_stock" class="form-check-input" checked>';
        $html .= '<label class="form-check-label">Gérer le stock</label>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '<div class="card mt-3">';
        $html .= '<div class="card-header"><h6 class="mb-0"><i class="fas fa-cog me-2"></i>Paramètres</h6></div>';
        $html .= '<div class="card-body">';
        $html .= '<div class="mb-3">';
        $html .= '<label class="form-label">Statut</label>';
        $html .= '<select name="status" class="form-select">';
        $html .= '<option value="draft">Brouillon</option>';
        $html .= '<option value="active">Actif</option>';
        $html .= '<option value="inactive">Inactif</option>';
        $html .= '</select>';
        $html .= '</div>';
        $html .= '<div class="form-check">';
        $html .= '<input type="checkbox" name="featured" class="form-check-input">';
        $html .= '<label class="form-check-label">Produit vedette</label>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '</div>';
        $html .= '</div>';
        
        if ($config['show_gallery']) {
            $html .= '<h5 class="mt-4"><i class="fas fa-images me-2"></i>Galerie d\'images</h5>';
            $html .= self::fileUpload('gallery', 'Images du produit', [
                'allowed_types' => ['jpg', 'jpeg', 'png', 'webp'],
                'multiple' => true,
                'max_size' => '5MB'
            ]);
        }
        
        $html .= '<h5 class="mt-4"><i class="fas fa-list me-2"></i>Catégories</h5>';
        $html .= self::selectMultipleBadges('categories', 'Catégories', [
            'electronics' => 'Électronique',
            'clothing' => 'Vêtements',
            'home' => 'Maison & Jardin',
            'sports' => 'Sport & Loisirs',
            'books' => 'Livres',
            'beauty' => 'Beauté & Santé',
            'automotive' => 'Auto & Moto',
            'toys' => 'Jouets & Enfants'
        ], [
            'placeholder' => 'Sélectionnez les catégories...'
        ]);
        
        $html .= self::selectMultipleBadges('tags', 'Tags', [
            'nouveau' => 'Nouveau',
            'promo' => 'En promotion',
            'bestseller' => 'Best-seller',
            'eco' => 'Écologique',
            'made_in_france' => 'Made in France',
            'premium' => 'Premium'
        ], [
            'placeholder' => 'Ajoutez des tags...'
        ]);
        
        if ($config['show_variations']) {
            $html .= '<h5 class="mt-4"><i class="fas fa-sliders-h me-2"></i>Variations</h5>';
            $html .= '<div class="card">';
            $html .= '<div class="card-body">';
            $html .= '<p class="text-muted">Ajoutez des variations pour ce produit (couleur, taille, etc.)</p>';
            $html .= '<div id="variations-container">';
            $html .= '<button type="button" class="btn btn-outline-primary btn-sm" id="add-variation">';
            $html .= '<i class="fas fa-plus me-1"></i>Ajouter une variation';
            $html .= '</button>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }
        
        $html .= '<div class="mt-4 d-flex gap-2">';
        $html .= '<button type="submit" class="btn btn-primary">';
        $html .= '<i class="fas fa-save me-2"></i>Enregistrer le produit';
        $html .= '</button>';
        $html .= '<button type="button" class="btn btn-secondary" onclick="history.back()">';
        $html .= '<i class="fas fa-arrow-left me-2"></i>Annuler';
        $html .= '</button>';
        $html .= '</div>';
        
        $html .= '</form>';
        $html .= '</div>';
        
        if ($config['show_variations']) {
            $html .= self::getVariationsScript();
        }
        
        return $html;
    }

    // ============ MÉTHODES UTILITAIRES ============

    /**
     * Générateur de template de formulaire complet
     */
    public static function template($type, $config = [])
    {
        switch ($type) {
            case 'contact':
                return self::contactTemplate($config);
            case 'registration':
                return self::registrationTemplate($config);
            case 'profile':
                return self::profileTemplate($config);
            case 'product':
                return self::productTemplate($config);
            default:
                throw new \InvalidArgumentException("Type de template inconnu: " . $type);
        }
    }

    /**
     * Générateur de formulaire de recherche avancée
     */
    public static function advancedSearch($config = [])
    {
        $config = array_merge([
            'fields' => [],
            'collapsible' => true,
            'form_id' => 'advanced_search',
            'title' => 'Recherche avancée'
        ], $config);

        $html = '<div class="advanced-search-container">';
        
        if ($config['collapsible']) {
            $html .= '<button class="btn btn-outline-secondary mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_' . $config['form_id'] . '">';
            $html .= '<i class="fas fa-search me-2"></i>' . $config['title'];
            $html .= '</button>';
            $html .= '<div class="collapse" id="collapse_' . $config['form_id'] . '">';
        }
        
        $html .= '<div class="card">';
        $html .= '<div class="card-body">';
        $html .= '<form id="' . $config['form_id'] . '">';
        
        foreach ($config['fields'] as $field) {
            switch ($field['type']) {
                case 'text':
                    $html .= self::text($field['name'], $field['label'], $field['options'] ?? []);
                    break;
                case 'select':
                    $html .= self::selectMultipleBadges($field['name'], $field['label'], $field['options'] ?? []);
                    break;
                case 'date_range':
                    $html .= '<div class="row">';
                    $html .= '<div class="col-md-6">' . self::text($field['name'] . '_from', $field['label'] . ' (De)', [
                        'type' => 'date'
                    ]) . '</div>';
                    $html .= '<div class="col-md-6">' . self::text($field['name'] . '_to', $field['label'] . ' (À)', [
                        'type' => 'date'
                    ]) . '</div>';
                    $html .= '</div>';
                    break;
                case 'price_range':
                    $html .= '<div class="row">';
                    $html .= '<div class="col-md-6">' . self::text($field['name'] . '_min', 'Prix minimum', [
                        'type' => 'number',
                        'icon' => 'fas fa-euro-sign'
                    ]) . '</div>';
                    $html .= '<div class="col-md-6">' . self::text($field['name'] . '_max', 'Prix maximum', [
                        'type' => 'number',
                        'icon' => 'fas fa-euro-sign'
                    ]) . '</div>';
                    $html .= '</div>';
                    break;
            }
        }
        
        $html .= '<div class="d-flex gap-2">';
        $html .= '<button type="submit" class="btn btn-primary">';
        $html .= '<i class="fas fa-search me-2"></i>Rechercher';
        $html .= '</button>';
        $html .= '<button type="reset" class="btn btn-secondary">';
        $html .= '<i class="fas fa-undo me-2"></i>Réinitialiser';
        $html .= '</button>';
        $html .= '</div>';
        
        $html .= '</form>';
        $html .= '</div>';
        $html .= '</div>';
        
        if ($config['collapsible']) {
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Soumission AJAX avec feedback
     */
    public static function ajaxSubmit($formId, $options = [])
    {
        $options = array_merge([
            'url' => '',
            'method' => 'POST',
            'success_message' => 'Données sauvegardées avec succès!',
            'error_message' => 'Une erreur est survenue',
            'loading_text' => 'Envoi en cours...',
            'redirect' => null,
            'reset_form' => false,
            'before_submit' => null
        ], $options);

        return self::getAjaxSubmitScript($formId, $options);
    }

    /**
     * Validation en temps réel
     */
    public static function addRealTimeValidation($formId, $rules = [])
    {
        return self::getRealTimeValidationScript($formId, $rules);
    }

    // ============ MÉTHODES UTILITAIRES PRIVÉES ============

    /**
     * Obtient les types de fichiers acceptés pour l'attribut accept
     */
    private static function getAcceptTypes($types)
    {
        $acceptTypes = [];
        foreach ($types as $type) {
            switch (strtolower($type)) {
                case 'pdf':
                    $acceptTypes[] = 'application/pdf';
                    break;
                case 'mp4':
                    $acceptTypes[] = 'video/mp4';
                    break;
                case 'avi':
                    $acceptTypes[] = 'video/avi';
                    break;
                case 'mov':
                    $acceptTypes[] = 'video/mov';
                    break;
                case 'jpg':
                case 'jpeg':
                    $acceptTypes[] = 'image/jpeg';
                    break;
                case 'png':
                    $acceptTypes[] = 'image/png';
                    break;
                case 'gif':
                    $acceptTypes[] = 'image/gif';
                    break;
                case 'webp':
                    $acceptTypes[] = 'image/webp';
                    break;
            }
        }
        return implode(',', $acceptTypes);
    }

    /**
     * Modal de crop d'image
     */
    private static function getCropModal($name, $ratio)
    {
        return '
        <div class="modal fade" id="cropModal_' . $name . '" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Recadrer l\'image</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="crop-container" id="cropContainer_' . $name . '"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-primary" id="applyCrop_' . $name . '">Appliquer</button>
                    </div>
                </div>
            </div>
        </div>';
    }

    // ============ SCRIPTS JAVASCRIPT ============

    /**
     * Styles CSS personnalisés
     */
    private static function getCustomStyles()
    {
        return '
<style>
/* Drop zones */
.image-drop-zone, .file-drop-zone {
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s;
    cursor: pointer;
    background: #fafafa;
}
.image-drop-zone:hover, .file-drop-zone:hover,
.image-drop-zone.dragover, .file-drop-zone.dragover {
    border-color: #0d6efd;
    background-color: #f8f9ff;
}

/* Image preview */
.image-preview {
    max-width: 200px;
    margin: 0 auto;
    text-align: center;
}
.image-preview img {
    border-radius: 0.375rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Badges */
.selected-badges .badge {
    margin: 2px;
    cursor: pointer;
    position: relative;
}
.selected-badges .badge:hover {
    opacity: 0.8;
}
.selected-badges .badge .fa-times {
    margin-left: 5px;
    cursor: pointer;
}

/* Multi-step progress */
.step-progress .step-labels {
    display: flex;
    justify-content: space-between;
}
.step-label {
    font-size: 0.875rem;
    color: #6c757d;
    flex: 1;
    text-align: center;
}
.step-label.active {
    color: #0d6efd;
    font-weight: bold;
}

/* Input list dropdown */
.input-list-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 0.375rem 0.375rem;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.input-list-item {
    padding: 0.5rem 0.75rem;
    cursor: pointer;
    border-bottom: 1px solid #f8f9fa;
}
.input-list-item:hover, .input-list-item.active {
    background-color: #f8f9fa;
}
.input-list-item:last-child {
    border-bottom: none;
}

/* File items */
.file-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    margin-bottom: 0.5rem;
    background: #fff;
    transition: all 0.2s;
}
.file-item:hover {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.file-item .file-icon {
    margin-right: 0.75rem;
    font-size: 1.5rem;
    color: #6c757d;
}
.file-item .file-info {
    flex: 1;
}
.file-item .file-name {
    font-weight: 500;
    margin-bottom: 0.25rem;
}
.file-item .file-size {
    font-size: 0.875rem;
}
.file-item .file-actions {
    margin-left: auto;
}

/* Star rating */
.star-rating {
    display: flex;
    gap: 0.25rem;
}
.star-rating .star {
    color: #dee2e6;
    cursor: pointer;
    transition: color 0.2s;
}
.star-rating .star.active,
.star-rating .star:hover {
    color: #ffc107;
}
.star-rating[data-readonly="true"] .star {
    cursor: default;
}

/* Form validation */
.is-valid {
    border-color: #198754;
}
.is-invalid {
    border-color: #dc3545;
}
.invalid-feedback {
    display: none;
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}
.is-invalid ~ .invalid-feedback {
    display: block;
}

/* Loading state */
.btn.loading {
    pointer-events: none;
    opacity: 0.6;
}

/* Crop modal */
.crop-container {
    text-align: center;
}

/* Utilities */
.cursor-pointer {
    cursor: pointer !important;
}






/* Combo select badges */
    .combo-select-badges .combo-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #dee2e6;
        border-top: none;
        border-radius: 0 0 0.375rem 0.375rem;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .combo-item {
        padding: 0.5rem 0.75rem;
        cursor: pointer;
        border-bottom: 1px solid #f8f9fa;
    }
    
    .combo-item:hover, .combo-item.active {
        background-color: #f8f9fa;
    }
    
    .combo-new-item {
        color: #0d6efd;
        font-style: italic;
    }
    
    /* Toggle switch */
    .toggle-container .bootstrap-switch {
        margin-left: 10px;
    }
    
    /* Range slider */
    .range-slider-container {
        padding: 20px 10px;
    }
    
    .slider .tooltip.in {
        opacity: 1;
    }
    
    .slider .tooltip.top .tooltip-inner {
        background: #0d6efd;
    }
    
    .slider .tooltip-arrow {
        border-top-color: #0d6efd;
    }
    
    .slider-handle {
        background: #0d6efd;
    }
    
    .slider-track {
        background: #dee2e6;
    }
    
    /* Color picker */
    .color-picker {
        max-width: 120px;
    }
    
    /* Datepicker */
    .flatpickr-input {
        background-color: white;
        cursor: pointer;
    }
    
    /* Summernote editor */
    .note-editor.note-frame {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }
    
    .note-editor.note-frame .note-toolbar {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    /* Select2 */
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #dee2e6;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #dee2e6;
    }

</style>';
    }

    /**
     * Scripts JavaScript personnalisés
     */
    private static function getCustomScripts()
    {
        return '
<script>
// Variables globales FormFx
window.FormFx = {
    selectedBadges: {},
    croppers: {},
    multiStepForms: {},
    uploadedFiles: {}
};

// Helper functions
window.FormFx.helpers = {
    formatFileSize: function(bytes) {
        if (bytes === 0) return "0 B";
        const k = 1024;
        const sizes = ["B", "KB", "MB", "GB"];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
    },
    
    getFileIcon: function(file) {
        if (file.type.startsWith("video/")) return "video";
        if (file.name.toLowerCase().endsWith(".pdf")) return "pdf";
        if (file.type.startsWith("image/")) return "image";
        return "file-alt";
    },
    
    validateEmail: function(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
};
</script>';
    }

    /**
     * Script pour les badges multiples
     */
    private static function getSelectMultipleBadgesScript($name)
    {
        return "
<script>
$(document).ready(function() {
    const selectElement = $('#{$name}_select');
    const badgesContainer = $('#badges_{$name}');
    const hiddenInput = $('#hidden_{$name}');
    
    if (!window.FormFx.selectedBadges['{$name}']) {
        window.FormFx.selectedBadges['{$name}'] = [];
    }
    
    selectElement.on('change', function() {
        const value = $(this).val();
        const text = $(this).find('option:selected').text();
        
        if (value && !window.FormFx.selectedBadges['{$name}'].includes(value)) {
            window.FormFx.selectedBadges['{$name}'].push(value);
            
            const badge = $('<span class=\"badge bg-primary me-1 mb-1\">' + text + ' <i class=\"fas fa-times ms-1\" data-value=\"' + value + '\"></i></span>');
            badgesContainer.append(badge);
            
            updateHiddenInput();
            $(this).val('');
        }
    });
    
    badgesContainer.on('click', '.fa-times', function() {
        const value = $(this).data('value');
        const index = window.FormFx.selectedBadges['{$name}'].indexOf(value);
        
        if (index > -1) {
            window.FormFx.selectedBadges['{$name}'].splice(index, 1);
            $(this).parent().remove();
            updateHiddenInput();
        }
    });
    
    function updateHiddenInput() {
        hiddenInput.val(JSON.stringify(window.FormFx.selectedBadges['{$name}']));
    }
});
</script>";
    }



    /**
     * Script pour l'upload de fichiers
     */

    private static function getImageUploadScript($name, $options)
{
    $cropEnabled = $options['crop'] ? 'true' : 'false';
    $cropRatio = $options['crop_ratio'];

    return "
<script>
$(document).ready(function() {
    // Sûr si l'objet n'existe pas encore
    window.FormFx = window.FormFx || {};
    window.FormFx.croppers = window.FormFx.croppers || {};

    const dropZone = $('#drop_{$name}');
    const fileInput = $('#{$name}_upload');
    const previewContainer = $('#preview_{$name}');
    const imgPreview = $('#img_preview_{$name}');
    const cropBtn = $('.crop-btn[data-target=\"{$name}\"]');
    const removeBtn = $('.remove-btn[data-target=\"{$name}\"]');
    const applyCropBtn = $('#applyCrop_{$name}');

    let currentFile = null;

    // CLICK sur la dropzone -> ouvrir le file input SANS boucle
    // - on namespace les events pour éviter les doublons
    // - on empêche la récursion en ignorant les clics qui proviennent du file input
    dropZone.off('click.formfx').on('click.formfx', function(e) {
        // si l'événement provient du file input (bubbling), on ignore
        if (e.target === fileInput[0] || $.contains(fileInput[0], e.target)) return;
        e.preventDefault();
        e.stopPropagation();
        // Utiliser le click natif plutôt que jQuery.trigger pour éviter certaines boucles
        fileInput[0].click();
    });

    // Évite que le clic du file input rebondisse vers la dropzone
    fileInput.off('click.formfx').on('click.formfx', function(e) {
        e.stopPropagation();
    });

    // Drag & drop
    dropZone
        .off('dragover.formfx dragenter.formfx dragleave.formfx dragend.formfx drop.formfx')
        .on('dragover.formfx dragenter.formfx', function(e) {
            e.preventDefault(); e.stopPropagation();
            $(this).addClass('dragover');
        })
        .on('dragleave.formfx dragend.formfx drop.formfx', function(e) {
            e.preventDefault(); e.stopPropagation();
            $(this).removeClass('dragover');
        })
        .on('drop.formfx', function(e) {
            const files = e.originalEvent.dataTransfer.files;
            if (files && files.length > 0) handleFile(files[0]);
        });

    // Sélection par le file input
    fileInput.off('change.formfx').on('change.formfx', function() {
        if (!this.files || this.files.length === 0) return; // évite toute boucle
        handleFile(this.files[0]);
    });

    function handleFile(file) {
        if (!file || !file.type || !file.type.startsWith('image/')) {
            Swal.fire('Erreur', 'Veuillez sélectionner une image valide', 'error');
            return;
        }
        currentFile = file;

        const reader = new FileReader();
        reader.onload = function(e) {
            dropZone.hide();
            previewContainer.show();
            imgPreview.html('<img src=\"' + e.target.result + '\" class=\"img-fluid\" style=\"max-width: 200px;\">');
        };
        reader.readAsDataURL(file);
    }

    // Recadrage
    cropBtn.off('click.formfx').on('click.formfx', function() {
        if (!currentFile) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            initCropper(e.target.result);
            $('#cropModal_{$name}').modal('show');
        };
        reader.readAsDataURL(currentFile);
    });

    // Suppression
    removeBtn.off('click.formfx').on('click.formfx', function() {
        resetUploader();
    });

    function initCropper(imageSrc) {
        if (window.FormFx.croppers['{$name}']) {
            window.FormFx.croppers['{$name}'].destroy();
        }

        const cropContainer = $('#cropContainer_{$name}');
        cropContainer.empty().html('<div id=\"crop_image_{$name}\"></div>');

        window.FormFx.croppers['{$name}'] = new Croppie(document.getElementById('crop_image_{$name}'), {
            viewport: {
                width: 200,
                height: Math.round(200/{$cropRatio}),
                type: 'square'
            },
            boundary: {
                width: 300,
                height: Math.round(300/{$cropRatio})
            },
            showZoomer: true,
            enableResize: true,
            enableOrientation: true
        });

        window.FormFx.croppers['{$name}'].bind({ url: imageSrc });
    }

    // Application du recadrage
    applyCropBtn.off('click.formfx').on('click.formfx', function() {
        const cr = window.FormFx.croppers['{$name}'];
        if (!cr) return;

        cr.result({
            type: 'canvas',
            size: 'viewport',
            format: 'jpeg',
            quality: 0.9
        }).then(function(dataUrl) {
            imgPreview.html('<img src=\"' + dataUrl + '\" class=\"img-fluid\" style=\"max-width: 200px;\">');
            $('#cropModal_{$name}').modal('hide');

            // Convertit le dataURL en Blob pour reconstruire un File
            const blob = (function dataURLtoBlob(u){
                const arr = u.split(','), mime = arr[0].match(/:(.*?);/)[1];
                const bstr = atob(arr[1]); let n = bstr.length;
                const u8 = new Uint8Array(n); while(n--) u8[n] = bstr.charCodeAt(n);
                return new Blob([u8], {type: mime});
            })(dataUrl);

            currentFile = new File([blob], currentFile ? currentFile.name : 'image.jpg', {
                type: 'image/jpeg',
                lastModified: Date.now()
            });
        });
    });

    function resetUploader() {
        dropZone.show();
        previewContainer.hide();
        fileInput.val(null); // ne déclenche pas 'change'
        currentFile = null;

        if (window.FormFx.croppers['{$name}']) {
            window.FormFx.croppers['{$name}'].destroy();
            delete window.FormFx.croppers['{$name}'];
        }
        $('#cropContainer_{$name}').empty();
    }
});
</script>";
}

    private static function getFileUploadScript($name, $options)
    {
        $multiple = $options['multiple'] ? 'true' : 'false';
        
        return "
<script>
$(document).ready(function() {
    const dropZone = $('#file_drop_{$name}');
    const fileInput = $('#{$name}_file_upload');
    const fileList = $('#file_list_{$name}');
    let selectedFiles = [];
    
    // dropZone.on('click', function() {
    //     fileInput.click();
    // });

    // CLICK sur la dropzone -> ouvrir le file input SANS boucle
    // - on namespace les events pour éviter les doublons
    // - on empêche la récursion en ignorant les clics qui proviennent du file input
    dropZone.off('click.formfx').on('click.formfx', function(e) {
        // si l'événement provient du file input (bubbling), on ignore
        if (e.target === fileInput[0] || $.contains(fileInput[0], e.target)) return;
        e.preventDefault();
        e.stopPropagation();
        // Utiliser le click natif plutôt que jQuery.trigger pour éviter certaines boucles
        fileInput[0].click();
    });

    // Évite que le clic du file input rebondisse vers la dropzone
    fileInput.off('click.formfx').on('click.formfx', function(e) {
        e.stopPropagation();
    });
    
    dropZone.on('dragover dragenter', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('dragover');
    });
    
    dropZone.on('dragleave dragend', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');
    });
    
    dropZone.on('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');
        
        const files = e.originalEvent.dataTransfer.files;
        handleFiles(files);
    });
    
    fileInput.on('change', function() {
        handleFiles(this.files);
    });
    
    function handleFiles(files) {
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            // Vérification du type de fichier
            const allowedTypes = " . json_encode($options['allowed_types']) . ";
            const fileExt = file.name.split('.').pop().toLowerCase();
            
            if (!allowedTypes.includes(fileExt)) {
                Swal.fire('Erreur', 'Type de fichier non autorisé: ' + fileExt, 'error');
                continue;
            }
            
            // Vérification de la taille
            const maxSize = " . self::parseFileSize($options['max_size']) . ";
            if (file.size > maxSize) {
                Swal.fire('Erreur', 'Fichier trop volumineux: ' + file.name, 'error');
                continue;
            }
            
            selectedFiles.push(file);
            addFileToList(file, selectedFiles.length - 1);
        }
    }
    
    function addFileToList(file, index) {
        const fileIcon = window.FormFx.helpers.getFileIcon(file);
        const fileSize = window.FormFx.helpers.formatFileSize(file.size);
        
        const fileItem = $(`
            <div class=\"file-item\" data-index=\"\${index}\">
                <div class=\"file-icon\">
                    <i class=\"fas fa-\${fileIcon}\"></i>
                </div>
                <div class=\"file-info\">
                    <div class=\"file-name\">\${file.name}</div>
                    <div class=\"file-size text-muted\">\${fileSize}</div>
                </div>
                <div class=\"file-actions\">
                    <button type=\"button\" class=\"btn btn-sm btn-danger remove-file\" data-index=\"\${index}\">
                        <i class=\"fas fa-trash\"></i>
                    </button>
                </div>
            </div>
        `);
        
        fileList.append(fileItem);
    }
    
    fileList.on('click', '.remove-file', function() {
        const index = $(this).data('index');
        selectedFiles.splice(index, 1);
        $(this).closest('.file-item').remove();
    });
});
</script>";
    }

    /**
     * Convertit une taille de fichier lisible (comme '5MB') en octets
     */
    private static function parseFileSize($size)
    {
        $units = ['B' => 1, 'KB' => 1024, 'MB' => 1024 * 1024, 'GB' => 1024 * 1024 * 1024];
        $matches = [];
        preg_match('/(\d+)\s*([KMG]?B)/i', $size, $matches);
        
        if (count($matches)) {
            return $matches[1] * $units[strtoupper($matches[2])];
        }
        
        return 5 * 1024 * 1024; // 5MB par défaut
    }

    /**
     * Script pour les formulaires multi-étapes
     */
    private static function getMultiStepScript($formId, $stepCount, $options)
    {
        return "
<script>
$(document).ready(function() {
    let currentStep = 0;
    const totalSteps = {$stepCount};
    const form = $('#form_{$formId}');
    
    window.FormFx.multiStepForms['{$formId}'] = {
        currentStep: currentStep,
        totalSteps: totalSteps
    };
    
    $('.next-step').on('click', function() {
        if (validateCurrentStep()) {
            if (currentStep < totalSteps - 1) {
                currentStep++;
                showStep(currentStep);
                updateProgress();
                updateButtons();
            }
        }
    });
    
    $('.prev-step').on('click', function() {
        if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
            updateProgress();
            updateButtons();
        }
    });
    
    function showStep(step) {
        $('.step').hide();
        $('#step_' + step).show();
        
        // Mettre à jour les labels
        $('.step-label').removeClass('active');
        $('.step-label').eq(step).addClass('active');
    }
    
    function updateProgress() {
        const progress = ((currentStep + 1) / totalSteps) * 100;
        $('.progress-bar').css('width', progress + '%');
    }
    
    function updateButtons() {
        $('.prev-step').toggle(currentStep > 0);
        $('.next-step').toggle(currentStep < totalSteps - 1);
        $('.submit-form').toggle(currentStep === totalSteps - 1);
    }
    
    function validateCurrentStep() {
        const currentStepElement = $('#step_' + currentStep);
        let isValid = true;
        
        // Validation basique des champs requis
        currentStepElement.find('input[required], textarea[required], select[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        return isValid;
    }
});
</script>";
    }

    /**
     * Script pour l'autocomplétion
     */
    private static function getInputListScript($name, $dataSource, $options)
    {
        $minChars = $options['min_chars'];
        $maxResults = $options['max_results'];
        $dataJson = is_array($dataSource) ? json_encode($dataSource) : '[]';
        
        return "
<script>
$(document).ready(function() {
    const input = $('#{$name}_input_list');
    const dropdown = $('#dropdown_{$name}');
    const data = {$dataJson};
    
    input.on('keyup', function() {
        const query = $(this).val();
        
        if (query.length >= {$minChars}) {
            const results = data.filter(item => 
                item.toLowerCase().includes(query.toLowerCase())
            ).slice(0, {$maxResults});
            
            showResults(results);
        } else {
            dropdown.hide();
        }
    });
    
    function showResults(results) {
        dropdown.empty();
        
        if (results.length > 0) {
            results.forEach(item => {
                const listItem = $('<div class=\"input-list-item\">' + item + '</div>');
                listItem.on('click', function() {
                    input.val(item);
                    dropdown.hide();
                });
                dropdown.append(listItem);
            });
            dropdown.show();
        } else {
            dropdown.hide();
        }
    }
    
    // Cacher le dropdown quand on clique ailleurs
    $(document).on('click', function(e) {
        if (!input.is(e.target) && !dropdown.is(e.target) && dropdown.has(e.target).length === 0) {
            dropdown.hide();
        }
    });
});
</script>";
    }

    /**
     * Script pour la notation par étoiles
     */
    private static function getStarRatingScript($name)
    {
        return "
<script>
$(document).ready(function() {
    const container = $('.star-rating[data-name=\"{$name}\"]');
    const hiddenInput = $('input[name=\"{$name}\"]');
    const isReadonly = container.data('readonly');
    
    if (!isReadonly) {
        container.find('.star').on('click', function() {
            const value = $(this).data('value');
            hiddenInput.val(value);
            
            // Mettre à jour l'affichage
            container.find('.star').each(function(index) {
                if (index < value) {
                    $(this).addClass('active');
                } else {
                    $(this).removeClass('active');
                }
            });
        });
        
        container.find('.star').on('mouseenter', function() {
            const value = $(this).data('value');
            
            container.find('.star').each(function(index) {
                if (index < value) {
                    $(this).addClass('active');
                } else {
                    $(this).removeClass('active');
                }
            });
        });
        
        container.on('mouseleave', function() {
            const currentValue = hiddenInput.val();
            
            container.find('.star').each(function(index) {
                if (index < currentValue) {
                    $(this).addClass('active');
                } else {
                    $(this).removeClass('active');
                }
            });
        });
    }
});
</script>";
    }

    /**
     * Script pour les variations de produits
     */
    private static function getVariationsScript()
    {
        return "
<script>
$(document).ready(function() {
    let variationCount = 0;
    
    $('#add-variation').on('click', function() {
        variationCount++;
        const variationHtml = `
            <div class=\"variation-item card mt-2\" id=\"variation_\${variationCount}\">
                <div class=\"card-body\">
                    <div class=\"d-flex justify-content-between align-items-center mb-2\">
                        <h6 class=\"mb-0\">Variation #\${variationCount}</h6>
                        <button type=\"button\" class=\"btn btn-sm btn-danger remove-variation\" data-variation=\"\${variationCount}\">
                            <i class=\"fas fa-trash\"></i>
                        </button>
                    </div>
                    <div class=\"row\">
                        <div class=\"col-md-4\">
                            <label class=\"form-label\">Type</label>
                            <select name=\"variations[\${variationCount}][type]\" class=\"form-select\">
                                <option value=\"color\">Couleur</option>
                                <option value=\"size\">Taille</option>
                                <option value=\"material\">Matériau</option>
                                <option value=\"style\">Style</option>
                            </select>
                        </div>
                        <div class=\"col-md-4\">
                            <label class=\"form-label\">Valeur</label>
                            <input type=\"text\" name=\"variations[\${variationCount}][value]\" class=\"form-control\" placeholder=\"Rouge, XL, etc.\">
                        </div>
                        <div class=\"col-md-4\">
                            <label class=\"form-label\">Prix ajustement</label>
                            <div class=\"input-group\">
                                <span class=\"input-group-text\">€</span>
                                <input type=\"number\" name=\"variations[\${variationCount}][price_adjustment]\" class=\"form-control\" placeholder=\"0.00\" step=\"0.01\">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#variations-container').append(variationHtml);
    });
    
    $(document).on('click', '.remove-variation', function() {
        const variationId = $(this).data('variation');
        $('#variation_' + variationId).remove();
    });
});
</script>";
    }

    /**
     * Script pour la soumission AJAX
     */
    private static function getAjaxSubmitScript($formId, $options)
    {
        $optionsJson = json_encode($options);
        
        return "
<script>
$(document).ready(function() {
    const options = {$optionsJson};
    
    $('#{$formId}').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type=\"submit\"]');
        const originalText = submitBtn.html();
        
        // Validation avant soumission
        if (options.before_submit && typeof options.before_submit === 'function') {
            if (!options.before_submit()) {
                return false;
            }
        }
        
        // État de chargement
        submitBtn.addClass('loading').html('<i class=\"fas fa-spinner fa-spin me-2\"></i>' + options.loading_text);
        
        // Préparation des données
        const formData = new FormData(this);
        
        $.ajax({
            url: options.url || form.attr('action'),
            method: options.method || 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Succès!',
                    text: options.success_message
                });
                
                if (options.reset_form) {
                    form[0].reset();
                }
                
                if (options.redirect) {
                    setTimeout(() => {
                        window.location.href = options.redirect;
                    }, 1500);
                }
            },
            error: function(xhr) {
                let errorMessage = options.error_message;
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur!',
                    text: errorMessage
                });
            },
            complete: function() {
                // Restaurer l'état du bouton
                submitBtn.removeClass('loading').html(originalText);
            }
        });
    });
});
</script>";
    }

    /**
     * Script pour la validation en temps réel
     */
    private static function getRealTimeValidationScript($formId, $rules)
    {
        $rulesJson = json_encode($rules);
        
        return "
<script>
$(document).ready(function() {
    const rules = {$rulesJson};
    
    $('#{$formId} input, #{$formId} select, #{$formId} textarea').on('blur change keyup', function() {
        const field = $(this);
        const name = field.attr('name');
        const value = field.val();
        
        if (rules[name]) {
            validateField(field, value, rules[name]);
        }
    });
    
    function validateField(field, value, fieldRules) {
        let isValid = true;
        let errorMessage = '';
        
        fieldRules.forEach(rule => {
            if (typeof rule === 'string') {
                if (rule === 'required' && (!value || value.trim() === '')) {
                    isValid = false;
                    errorMessage = 'Ce champ est requis';
                } else if (rule.startsWith('min:')) {
                    const min = parseInt(rule.split(':')[1]);
                    if (value && value.length < min) {
                        isValid = false;
                        errorMessage = 'Minimum ' + min + ' caractères requis';
                    }
                } else if (rule.startsWith('max:')) {
                    const max = parseInt(rule.split(':')[1]);
                    if (value && value.length > max) {
                        isValid = false;
                        errorMessage = 'Maximum ' + max + ' caractères autorisés';
                    }
                } else if (rule === 'email') {
                    if (value && !window.FormFx.helpers.validateEmail(value)) {
                        isValid = false;
                        errorMessage = 'Format email invalide';
                    }
                } else if (rule === 'numeric') {
                    if (value && isNaN(value)) {
                        isValid = false;
                        errorMessage = 'Ce champ doit être numérique';
                    }
                } else if (rule.startsWith('regex:')) {
                    const pattern = rule.split(':')[1];
                    const regex = new RegExp(pattern);
                    if (value && !regex.test(value)) {
                        isValid = false;
                        errorMessage = 'Format invalide';
                    }
                }
            } else if (typeof rule === 'object') {
                // Règle personnalisée
                if (rule.validator && typeof rule.validator === 'function') {
                    const result = rule.validator(value, field);
                    if (!result) {
                        isValid = false;
                        errorMessage = rule.message || 'Valeur invalide';
                    }
                }
            }
        });
        
        // Appliquer le style de validation
        if (isValid || !value) {
            field.removeClass('is-invalid').addClass('is-valid');
            field.siblings('.invalid-feedback').text('');
        } else {
            field.removeClass('is-valid').addClass('is-invalid');
            field.siblings('.invalid-feedback').text(errorMessage);
        }
        
        return isValid;
    }
});
</script>";
    }




    /**
     * Tableau éditable en ligne
     */
    public static function editableTable($config = [])
    {
        $config = array_merge([
            'data' => [],
            'columns' => [],
            'editable_columns' => [],
            'table_id' => 'editable_table'
        ], $config);

        $html = '<div class="table-responsive">';
        $html .= '<table class="table table-bordered" id="' . $config['table_id'] . '">';
        
        // En-têtes
        $html .= '<thead><tr>';
        foreach ($config['columns'] as $col) {
            $html .= '<th>' . $col['label'] . '</th>';
        }
        $html .= '<th>Actions</th></tr></thead>';
        
        // Corps du tableau
        $html .= '<tbody>';
        foreach ($config['data'] as $row) {
            $html .= '<tr>';
            foreach ($config['columns'] as $col) {
                $isEditable = in_array($col['name'], $config['editable_columns']);
                if ($isEditable) {
                    $html .= '<td class="editable" data-field="' . $col['name'] . '">' . $row[$col['name']] . '</td>';
                } else {
                    $html .= '<td>' . $row[$col['name']] . '</td>';
                }
            }
            $html .= '<td>';
            $html .= '<button class="btn btn-sm btn-primary edit-row">Modifier</button> ';
            $html .= '<button class="btn btn-sm btn-danger delete-row">Supprimer</button>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        
        // Script pour l'édition
        $html .= self::getEditableTableScript($config['table_id']);
        
        return $html;
    }

    private static function getEditableTableScript($tableId)
    {
        return "
<script>
$(document).ready(function() {
    $('#{$tableId}').on('click', '.edit-row', function() {
        const row = $(this).closest('tr');
        const editableCells = row.find('.editable');
        
        editableCells.each(function() {
            const cell = $(this);
            const value = cell.text();
            const field = cell.data('field');
            
            cell.html('<input type=\"text\" class=\"form-control form-control-sm\" value=\"' + value + '\" data-original=\"' + value + '\">');
        });
        
        $(this).text('Sauvegarder').removeClass('btn-primary').addClass('btn-success').addClass('save-row').removeClass('edit-row');
        row.find('.delete-row').text('Annuler').removeClass('btn-danger').addClass('btn-secondary').addClass('cancel-row').removeClass('delete-row');
    });
    
    $('#{$tableId}').on('click', '.save-row', function() {
        const row = $(this).closest('tr');
        const inputs = row.find('input');
        
        inputs.each(function() {
            const input = $(this);
            const cell = input.parent();
            cell.text(input.val());
        });
        
        $(this).text('Modifier').removeClass('btn-success').addClass('btn-primary').addClass('edit-row').removeClass('save-row');
        row.find('.cancel-row').text('Supprimer').removeClass('btn-secondary').addClass('btn-danger').addClass('delete-row').removeClass('cancel-row');
        
        // Ici, vous pouvez ajouter un appel AJAX pour sauvegarder
    });
    
    $('#{$tableId}').on('click', '.cancel-row', function() {
        const row = $(this).closest('tr');
        const inputs = row.find('input');
        
        inputs.each(function() {
            const input = $(this);
            const cell = input.parent();
            const original = input.data('original');
            cell.text(original);
        });
        
        row.find('.save-row').text('Modifier').removeClass('btn-success').addClass('btn-primary').addClass('edit-row').removeClass('save-row');
        $(this).text('Supprimer').removeClass('btn-secondary').addClass('btn-danger').addClass('delete-row').removeClass('cancel-row');
    });
});
</script>";
    }




/**
 * Checkbox moderne avec options avancées
 */
public static function checkbox($name, $label, $options = [])
{
    $options = array_merge([
        'checked' => false,
        'value' => '1',
        'inline' => false,
        'switch' => false, // Style switch bootstrap
        'wrapper_class' => 'mb-3',
        'help' => null
    ], $options);

    $id = $name . '_' . uniqid();
    $checked = $options['checked'] ? 'checked' : '';
    $switchClass = $options['switch'] ? 'form-switch' : '';
    $inlineClass = $options['inline'] ? 'form-check-inline' : '';
    
    $html = '<div class="' . $options['wrapper_class'] . ' ' . $switchClass . '">';
    $html .= '<div class="form-check ' . $inlineClass . '">';
    $html .= '<input type="checkbox" name="' . $name . '" id="' . $id . '" class="form-check-input"';
    $html .= ' value="' . htmlspecialchars($options['value']) . '" ' . $checked . '>';
    $html .= '<label for="' . $id . '" class="form-check-label">' . $label . '</label>';
    
    if ($options['help']) {
        $html .= '<div class="form-text">' . $options['help'] . '</div>';
    }
    
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}

/**
 * Groupe de checkboxes
 */
public static function checkboxGroup($name, $label, $items, $options = [])
{
    $options = array_merge([
        'selected' => [],
        'inline' => false,
        'wrapper_class' => 'mb-3'
    ], $options);

    $html = '<div class="' . $options['wrapper_class'] . '">';
    $html .= '<label class="form-label">' . $label . '</label>';
    $html .= '<div class="checkbox-group">';
    
    foreach ($items as $value => $itemLabel) {
        $checked = in_array($value, $options['selected']) ? 'checked' : '';
        $inlineClass = $options['inline'] ? 'form-check-inline' : '';
        $id = $name . '_' . $value . '_' . uniqid();
        
        $html .= '<div class="form-check ' . $inlineClass . '">';
        $html .= '<input type="checkbox" name="' . $name . '[]" id="' . $id . '"';
        $html .= ' class="form-check-input" value="' . htmlspecialchars($value) . '" ' . $checked . '>';
        $html .= '<label for="' . $id . '" class="form-check-label">' . $itemLabel . '</label>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}

/**
 * Groupe de radios
 */
public static function radioGroup($name, $label, $items, $options = [])
{
    $options = array_merge([
        'selected' => null,
        'inline' => false,
        'wrapper_class' => 'mb-3'
    ], $options);

    $html = '<div class="' . $options['wrapper_class'] . '">';
    $html .= '<label class="form-label">' . $label . '</label>';
    $html .= '<div class="radio-group">';
    
    foreach ($items as $value => $itemLabel) {
        $checked = ($value === $options['selected']) ? 'checked' : '';
        $inlineClass = $options['inline'] ? 'form-check-inline' : '';
        $id = $name . '_' . $value . '_' . uniqid();
        
        $html .= '<div class="form-check ' . $inlineClass . '">';
        $html .= '<input type="radio" name="' . $name . '" id="' . $id . '"';
        $html .= ' class="form-check-input" value="' . htmlspecialchars($value) . '" ' . $checked . '>';
        $html .= '<label for="' . $id . '" class="form-check-label">' . $itemLabel . '</label>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}

/**
 * Select avec recherche et options avancées
 */
public static function select($name, $label, $options, $config = [])
{
    $config = array_merge([
        'selected' => null,
        'placeholder' => 'Sélectionnez...',
        'search' => true,
        'allow_clear' => true,
        'wrapper_class' => 'mb-3',
        'multiple' => false
    ], $config);

    $id = $name . '_select';
    $multiple = $config['multiple'] ? 'multiple' : '';
    $nameAttr = $config['multiple'] ? $name . '[]' : $name;
    
    $html = '<div class="' . $config['wrapper_class'] . '">';
    $html .= '<label for="' . $id . '" class="form-label">' . $label . '</label>';
    $html .= '<select name="' . $nameAttr . '" id="' . $id . '" class="form-select select-advanced" ' . $multiple . '>';
    
    if ($config['placeholder'] && !$config['multiple']) {
        $html .= '<option value="">' . $config['placeholder'] . '</option>';
    }
    
    foreach ($options as $value => $text) {
        $selected = ($config['multiple'] && in_array($value, (array)$config['selected'])) || 
                   (!$config['multiple'] && $value === $config['selected']) ? 'selected' : '';
        $html .= '<option value="' . htmlspecialchars($value) . '" ' . $selected . '>' . htmlspecialchars($text) . '</option>';
    }
    
    $html .= '</select>';
    $html .= '</div>';
    
    // Script pour initialiser Select2
    $html .= self::getSelectScript($id, $config);
    
    return $html;
}

private static function getSelectScript($id, $config)
{
    return "
<script>
$(document).ready(function() {
    $('#{$id}').select2({
        placeholder: '" . addslashes($config['placeholder']) . "',
        allowClear: " . ($config['allow_clear'] ? 'true' : 'false') . ",
        language: 'fr'
    });
});
</script>";
}

/**
 * Textarea avancé avec options de mise en forme
 */
public static function textarea($name, $label, $options = [])
{
    $options = array_merge([
        'value' => '',
        'rows' => 3,
        'placeholder' => '',
        'editor' => false, // Activer l'éditeur WYSIWYG
        'editor_toolbar' => 'basic', // basic, full
        'wrapper_class' => 'mb-3'
    ], $options);

    $id = $name . '_textarea';
    
    $html = '<div class="' . $options['wrapper_class'] . '">';
    $html .= '<label for="' . $id . '" class="form-label">' . $label . '</label>';
    
    if ($options['editor']) {
        $html .= '<div class="editor-container">';
    }
    
    $html .= '<textarea name="' . $name . '" id="' . $id . '" class="form-control"';
    $html .= ' rows="' . $options['rows'] . '" placeholder="' . htmlspecialchars($options['placeholder']) . '">';
    $html .= htmlspecialchars($options['value']);
    $html .= '</textarea>';
    
    if ($options['editor']) {
        $html .= '</div>';
        $html .= self::getEditorScript($id, $options['editor_toolbar']);
    }
    
    $html .= '</div>';
    
    return $html;
}

private static function getEditorScript($id, $toolbar)
{
    $toolbarConfig = $toolbar === 'full' ? 
        "['bold', 'italic', 'underline', 'strike', '|', 'h1', 'h2', 'h3', '|', 'link', 'image', 'blockquote', 'code', '|', 'ul', 'ol', '|', 'undo', 'redo']" :
        "['bold', 'italic', 'underline', '|', 'ul', 'ol', '|', 'link']";
    
    return "
<script>
$(document).ready(function() {
    $('#{$id}').summernote({
        toolbar: {$toolbarConfig},
        height: 200,
        lang: 'fr-FR'
    });
});
</script>";
}

/**
 * Datepicker moderne avec différentes options
 */
public static function datepicker($name, $label, $options = [])
{
    $options = array_merge([
        'value' => '',
        'placeholder' => 'Choisir une date',
        'mode' => 'single', // single, range, time, datetime
        'time_24hr' => true,
        'min_date' => null,
        'max_date' => null,
        'disabled_dates' => [],
        'inline' => false,
        'wrapper_class' => 'mb-3'
    ], $options);

    $id = $name . '_datepicker';
    $value = $options['value'] ? date('Y-m-d', strtotime($options['value'])) : '';
    
    $html = '<div class="' . $options['wrapper_class'] . '">';
    $html .= '<label for="' . $id . '" class="form-label">' . $label . '</label>';
    
    if ($options['inline']) {
        $html .= '<div id="' . $id . '_container"></div>';
        $html .= '<input type="hidden" name="' . $name . '" id="' . $id . '">';
    } else {
        $html .= '<input type="text" name="' . $name . '" id="' . $id . '" class="form-control datepicker"';
        $html .= ' placeholder="' . htmlspecialchars($options['placeholder']) . '"';
        $html .= ' value="' . htmlspecialchars($value) . '" autocomplete="off">';
    }
    
    $html .= '</div>';
    
    // Script pour initialiser Flatpickr
    $html .= self::getDatepickerScript($id, $options);
    
    return $html;
}

private static function getDatepickerScript($id, $options)
{
    $config = [
        'mode' => $options['mode'],
        'dateFormat' => 'Y-m-d',
        'enableTime' => in_array($options['mode'], ['time', 'datetime']),
        'time_24hr' => $options['time_24hr'],
        'inline' => $options['inline']
    ];
    
    if ($options['min_date']) {
        $config['minDate'] = $options['min_date'];
    }
    
    if ($options['max_date']) {
        $config['maxDate'] = $options['max_date'];
    }
    
    if ($options['disabled_dates']) {
        $config['disable'] = array_map(function($date) {
            return date('Y-m-d', strtotime($date));
        }, $options['disabled_dates']);
    }
    
    return "
<script>
$(document).ready(function() {
    const config = " . json_encode($config) . ";
    
    if (config.inline) {
        config.onChange = function(selectedDates, dateStr) {
            $('#{$id}').val(dateStr);
        };
        
        $('#{$id}_container').flatpickr(config);
    } else {
        $('#{$id}').flatpickr(config);
    }
});
</script>";
}




/**
 * Toggle switch moderne
 */
public static function toggle($name, $label, $options = [])
{
    $options = array_merge([
        'checked' => false,
        'value_on' => '1',
        'value_off' => '0',
        'size' => 'normal', // sm, normal, lg
        'wrapper_class' => 'mb-3'
    ], $options);

    $id = $name . '_toggle';
    $checked = $options['checked'] ? 'checked' : '';
    $sizeClass = 'btn-' . $options['size'];
    
    $html = '<div class="' . $options['wrapper_class'] . '">';
    $html .= '<label class="form-label">' . $label . '</label>';
    $html .= '<div class="toggle-container">';
    $html .= '<input type="checkbox" name="' . $name . '" id="' . $id . '"';
    $html .= ' value="' . $options['value_on'] . '" ' . $checked . ' data-value-off="' . $options['value_off'] . '">';
    $html .= '</div>';
    $html .= '</div>';
    
    // Script pour initialiser Bootstrap Switch
    $html .= self::getToggleScript($id, $sizeClass);
    
    return $html;
}

private static function getToggleScript($id, $sizeClass)
{
    return "
<script>
$(document).ready(function() {
    $('#{$id}').bootstrapSwitch({
        size: '{$sizeClass}',
        onText: 'ON',
        offText: 'OFF',
        onSwitchChange: function(event, state) {
            $(this).val(state ? $(this).val() : $(this).data('value-off'));
        }
    });
});
</script>";
}















    // ============ NOUVEAUX COMPOSANTS FORMFX ============

    /**
     * Input avec autocomplétion et badges (combinaison inputList + selectMultipleBadges)
     */
    public static function inputListWithBadges($name, $label, $dataSource, $options = [])
    {
        $options = array_merge([
            'placeholder' => 'Tapez pour rechercher et sélectionner...',
            'min_chars' => 1,
            'max_results' => 10,
            'ajax_url' => null,
            'wrapper_class' => 'mb-3',
            'badge_class' => 'bg-primary',
            'max_items' => null,
            'allow_create' => false,
            'create_label' => 'Créer "%s"',
            'help' => null
        ], $options);

        $id = $name . '_input_badges';
        
        $html = '<div class="' . $options['wrapper_class'] . '">';
        $html .= '<label for="' . $id . '" class="form-label">' . $label . '</label>';
        $html .= '<div class="selected-badges mb-2" id="badges_' . $name . '"></div>';
        $html .= '<div class="input-badges-container position-relative">';
        $html .= '<input type="text" name="' . $name . '_search" id="' . $id . '" class="form-control input-badges"';
        $html .= ' placeholder="' . htmlspecialchars($options['placeholder']) . '" autocomplete="off">';
        $html .= '<div class="input-badges-dropdown" id="dropdown_' . $name . '"></div>';
        $html .= '<input type="hidden" name="' . $name . '" id="hidden_' . $name . '">';
        $html .= '</div>';
        
        if ($options['help']) {
            $html .= '<div class="form-text">' . $options['help'] . '</div>';
        }
        
        $html .= '</div>';
        
        $html .= self::getInputListWithBadgesScript($name, $dataSource, $options);
        
        return $html;
    }

    /**
     * Tags input avec autocomplétition
     */
    public static function tagsInput($name, $label, $options = [])
    {
        $options = array_merge([
            'wrapper_class' => 'mb-3',
            'suggestions' => [],
            'ajax_url' => null,
            'max_tags' => null,
            'min_length' => 2,
            'placeholder' => 'Tapez et appuyez sur Entrée...',
            'allow_duplicates' => false,
            'tag_class' => 'bg-primary',
            'validation' => [],
            'help' => null
        ], $options);

        $id = $options['id'] ?? $name;
        $required = in_array('required', $options['validation']) ? 'required' : '';
        
        $html = '<div class="' . $options['wrapper_class'] . '">';
        $html .= '<label for="' . $id . '" class="form-label">' . $label . '</label>';
        $html .= '<div class="tags-input-container" id="container_' . $name . '">';
        $html .= '<div class="tags-display" id="tags_' . $name . '"></div>';
        $html .= '<input type="text" id="' . $id . '" class="form-control tags-input" placeholder="' . $options['placeholder'] . '" autocomplete="off">';
        $html .= '<div class="tags-suggestions" id="suggestions_' . $name . '"></div>';
        $html .= '<input type="hidden" name="' . $name . '" id="hidden_' . $name . '" ' . $required . '>';
        $html .= '</div>';
        
        if ($options['help']) {
            $html .= '<div class="form-text">' . $options['help'] . '</div>';
        }
        
        $html .= '<div class="invalid-feedback"></div>';
        $html .= '</div>';
        
        $html .= self::getTagsInputScript($name, $options);
        
        return $html;
    }

    /**
     * File manager avec preview et organisation
     */
    // public static function fileManager($name, $label, $options = [])
    // {
    //     $options = array_merge([
    //         'wrapper_class' => 'mb-3',
    //         'max_files' => 10,
    //         'max_size' => '10MB',
    //         'allowed_types' => ['*'],
    //         'show_preview' => true,
    //         'sortable' => true,
    //         'folders' => false,
    //         'grid_view' => true,
    //         'upload_url' => null,
    //         'delete_url' => null
    //     ], $options);

    //     $id = $name . '_file_manager';
        
    //     $html = '<div class="' . $options['wrapper_class'] . '">';
    //     $html .= '<label class="form-label">' . $label . '</label>';
    //     $html .= '<div class="file-manager-container" id="' . $id . '">';
    //     $html .= '<div class="file-manager-toolbar mb-3">';
    //     $html .= '<div class="btn-group me-2">';
    //     $html .= '<button type="button" class="btn btn-sm btn-primary upload-btn">';
    //     $html .= '<i class="fas fa-plus me-1"></i>Ajouter</button>';
        
    //     if ($options['folders']) {
    //         $html .= '<button type="button" class="btn btn-sm btn-secondary new-folder-btn">';
    //         $html .= '<i class="fas fa-folder-plus me-1"></i>Dossier</button>';
    //     }
        
    //     $html .= '</div>';
    //     $html .= '<div class="btn-group">';
    //     $html .= '<button type="button" class="btn btn-sm btn-outline-secondary view-grid active" data-view="grid">';
    //     $html .= '<i class="fas fa-th"></i></button>';
    //     $html .= '<button type="button" class="btn btn-sm btn-outline-secondary view-list" data-view="list">';
    //     $html .= '<i class="fas fa-list"></i></button>';
    //     $html .= '</div>';
    //     $html .= '</div>';
    //     $html .= '<div class="file-manager-dropzone mb-3">';
    //     $html .= '<div class="dropzone-content">';
    //     $html .= '<i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>';
    //     $html .= '<p>Glissez vos fichiers ici ou cliquez pour sélectionner</p>';
    //     $html .= '</div>';
    //     $html .= '<input type="file" class="d-none" id="file_input_' . $name . '" multiple>';
    //     $html .= '</div>';
    //     $html .= '<div class="file-manager-content" id="content_' . $name . '">';
    //     $html .= '<div class="file-grid" id="grid_' . $name . '"></div>';
    //     $html .= '</div>';
    //     $html .= '<input type="hidden" name="' . $name . '" id="selected_' . $name . '">';
    //     $html .= '</div>';
    //     $html .= '</div>';
        
    //     $html .= self::getFileManagerScript($name, $options);
        
    //     return $html;
    // }

    /**
     * Signature pad pour signatures manuscrites
     */
    public static function signaturePad($name, $label, $options = [])
    {
        $options = array_merge([
            'wrapper_class' => 'mb-3',
            'width' => 400,
            'height' => 200,
            'background_color' => '#ffffff',
            'pen_color' => '#000000',
            'pen_width' => 2,
            'help' => null
        ], $options);

        $id = $name . '_signature';
        
        $html = '<div class="' . $options['wrapper_class'] . '">';
        $html .= '<label class="form-label">' . $label . '</label>';
        $html .= '<div class="signature-pad-container">';
        $html .= '<canvas id="' . $id . '" width="' . $options['width'] . '" height="' . $options['height'] . '"';
        $html .= ' style="border: 1px solid #dee2e6; border-radius: 0.375rem; background: ' . $options['background_color'] . ';"></canvas>';
        $html .= '<div class="signature-controls mt-2">';
        $html .= '<button type="button" class="btn btn-sm btn-secondary clear-signature" data-target="' . $id . '">';
        $html .= '<i class="fas fa-eraser me-1"></i>Effacer</button>';
        $html .= '<button type="button" class="btn btn-sm btn-primary save-signature" data-target="' . $id . '">';
        $html .= '<i class="fas fa-save me-1"></i>Sauvegarder</button>';
        $html .= '</div>';
        $html .= '<input type="hidden" name="' . $name . '" id="data_' . $name . '">';
        $html .= '</div>';
        
        if ($options['help']) {
            $html .= '<div class="form-text">' . $options['help'] . '</div>';
        }
        
        $html .= '</div>';
        
        $html .= self::getSignaturePadScript($name, $options);
        
        return $html;
    }

    // ============ SCRIPTS JAVASCRIPT POUR LES NOUVEAUX COMPOSANTS ============

    /**
     * Script pour InputList avec Badges
     */
    private static function getInputListWithBadgesScript($name, $dataSource, $options)
    {
        $dataJson = is_array($dataSource) ? json_encode($dataSource) : '[]';
        $optionsJson = json_encode($options);
        
        return <<<HTML
<script>
$(document).ready(function() {
    const input = $('#{$name}_input_badges');
    const dropdown = $('#dropdown_{$name}');
    const badgesContainer = $('#badges_{$name}');
    const hiddenInput = $('#hidden_{$name}');
    const options = {$optionsJson};
    const localData = {$dataJson};
    let selectedItems = [];
    
    input.on('keyup', function(e) {
        const query = $(this).val();
        
        if (e.keyCode === 13 && query.length > 0) {
            e.preventDefault();
            if (options.allow_create) {
                addItem(query, query);
                $(this).val('');
                dropdown.hide();
            }
            return;
        }
        
        if (query.length >= options.min_chars) {
            if (options.ajax_url) {
                searchAjax(query);
            } else {
                searchLocal(query);
            }
        } else {
            dropdown.hide();
        }
    });
    
    function searchLocal(query) {
        const results = [];
        
        if (Array.isArray(localData)) {
            localData.forEach(item => {
                if (typeof item === 'string' && item.toLowerCase().includes(query.toLowerCase())) {
                    results.push({ value: item, text: item });
                }
            });
        } else {
            Object.keys(localData).forEach(key => {
                if (localData[key].toLowerCase().includes(query.toLowerCase())) {
                    results.push({ value: key, text: localData[key] });
                }
            });
        }
        
        showResults(results.slice(0, options.max_results));
    }
    
    function searchAjax(query) {
        $.ajax({
            url: options.ajax_url,
            method: 'GET',
            data: { q: query, limit: options.max_results },
            success: function(data) {
                showResults(data.results || data);
            }
        });
    }
    
    function showResults(results) {
        dropdown.empty();
        
        if (results.length > 0) {
            results.forEach(item => {
                if (selectedItems.find(selected => selected.value === item.value)) return;
                
                const listItem = $('<div class="input-list-item">' + item.text + '</div>');
                listItem.on('click', function() {
                    addItem(item.value, item.text);
                    input.val('');
                    dropdown.hide();
                });
                dropdown.append(listItem);
            });
            
            if (options.allow_create && results.length === 0) {
                const createItem = $('<div class="input-list-item text-muted"><i class="fas fa-plus me-2"></i>' + 
                    options.create_label.replace('%s', input.val()) + '</div>');
                createItem.on('click', function() {
                    const value = input.val();
                    addItem(value, value);
                    input.val('');
                    dropdown.hide();
                });
                dropdown.append(createItem);
            }
            
            dropdown.show();
        } else {
            dropdown.hide();
        }
    }
    
    function addItem(value, text) {
        if (options.max_items && selectedItems.length >= options.max_items) return;
        if (!options.allow_duplicates && selectedItems.find(item => item.value === value)) return;
        
        selectedItems.push({ value: value, text: text });
        
        const badge = $('<span class="badge ' + options.badge_class + ' me-1 mb-1">' + text + 
            ' <i class="fas fa-times ms-1 cursor-pointer" data-value="' + value + '"></i></span>');
        
        badgesContainer.append(badge);
        updateHiddenInput();
    }
    
    badgesContainer.on('click', '.fa-times', function() {
        const value = $(this).data('value');
        selectedItems = selectedItems.filter(item => item.value !== value);
        $(this).parent().remove();
        updateHiddenInput();
    });
    
    function updateHiddenInput() {
        hiddenInput.val(JSON.stringify(selectedItems.map(item => item.value)));
    }
    
    $(document).on('click', function(e) {
        if (!input.is(e.target) && !dropdown.is(e.target) && dropdown.has(e.target).length === 0) {
            dropdown.hide();
        }
    });
});
</script>
HTML;
    }

    /**
     * Script pour Tags Input
     */
    private static function getTagsInputScript($name, $options)
    {
        $optionsJson = json_encode($options);
        
        return <<<HTML
<script>
$(document).ready(function() {
    const input = $('#{$name}');
    const tagsDisplay = $('#tags_{$name}');
    const suggestions = $('#suggestions_{$name}');
    const hiddenInput = $('#hidden_{$name}');
    const options = {$optionsJson};
    let tags = [];
    
    input.on('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            addTag($(this).val().trim());
            $(this).val('');
            suggestions.hide();
        } else if (e.key === 'Backspace' && $(this).val() === '') {
            removeLastTag();
        }
    });
    
    input.on('keyup', function() {
        const query = $(this).val();
        if (query.length >= options.min_length) {
            showSuggestions(query);
        } else {
            suggestions.hide();
        }
    });
    
    function addTag(tag) {
        if (!tag || tags.includes(tag)) return;
        if (options.max_tags && tags.length >= options.max_tags) return;
        
        tags.push(tag);
        
        const tagElement = $('<span class="badge ' + options.tag_class + ' me-1 mb-1">' + 
            tag + ' <i class="fas fa-times ms-1 cursor-pointer"></i></span>');
        
        tagElement.find('.fa-times').on('click', function() {
            removeTag(tag);
        });
        
        tagsDisplay.append(tagElement);
        updateHiddenInput();
    }
    
    function removeTag(tag) {
        tags = tags.filter(t => t !== tag);
        tagsDisplay.find('.badge:contains("' + tag + '")').remove();
        updateHiddenInput();
    }
    
    function removeLastTag() {
        if (tags.length > 0) {
            const lastTag = tags[tags.length - 1];
            removeTag(lastTag);
        }
    }
    
    function showSuggestions(query) {
        suggestions.empty();
        const filtered = options.suggestions.filter(item => 
            item.toLowerCase().includes(query.toLowerCase()) && !tags.includes(item)
        );
        
        if (filtered.length > 0) {
            filtered.slice(0, 5).forEach(item => {
                const suggestion = $('<div class="suggestion-item">' + item + '</div>');
                suggestion.on('click', function() {
                    addTag(item);
                    input.val('');
                    suggestions.hide();
                });
                suggestions.append(suggestion);
            });
            suggestions.show();
        } else {
            suggestions.hide();
        }
    }
    
    function updateHiddenInput() {
        hiddenInput.val(tags.join(','));
    }
});
</script>
HTML;
    }

    /**
     * Script pour File Manager
     */
//     private static function getFileManagerScript($name, $options)
//     {
//         $optionsJson = json_encode($options);
        
//         return <<<HTML
// <script>
// $(document).ready(function() {
//     const container = $('#{$name}_file_manager');
//     const options = {$optionsJson};
//     let files = [];
    
//     // Initialisation du dropzone
//     const dropzone = container.find('.file-manager-dropzone');
    
//     dropzone.on('dragover', function(e) {
//         e.preventDefault();
//         $(this).addClass('dropzone-active');
//     });
    
//     dropzone.on('dragleave', function() {
//         $(this).removeClass('dropzone-active');
//     });
    
//     dropzone.on('drop', function(e) {
//         e.preventDefault();
//         $(this).removeClass('dropzone-active');
//         handleFiles(e.originalEvent.dataTransfer.files);
//     });
    
//     dropzone.on('click', function() {
//         $('#file_input_{$name}').trigger('click');
//     });
    
//     $('#file_input_{$name}').on('change', function() {
//         handleFiles(this.files);
//     });
    
//     function handleFiles(fileList) {
//         Array.from(fileList).forEach(file => {
//             if (options.max_files && files.length >= options.max_files) return;
            
//             const reader = new FileReader();
//             reader.onload = function(e) {
//                 files.push({
//                     name: file.name,
//                     size: file.size,
//                     type: file.type,
//                     data: e.target.result
//                 });
                
//                 updateFileDisplay();
//                 updateHiddenInput();
//             };
//             reader.readAsDataURL(file);
//         });
//     }
    
//     function updateFileDisplay() {
//         const grid = container.find('.file-grid');
//         grid.empty();
        
//         files.forEach((file, index) => {
//             const fileItem = $('<div class="file-item" data-index="' + index + '"></div>');
            
//             if (file.type.startsWith('image/')) {
//                 fileItem.append('<img src="' + file.data + '" class="file-thumbnail">');
//             } else {
//                 fileItem.append('<i class="far fa-file-alt fa-3x text-muted"></i>');
//             }
            
//             fileItem.append('<div class="file-name">' + file.name + '</div>');
//             fileItem.append('<div class="file-size">' + formatFileSize(file.size) + '</div>');
            
//             const deleteBtn = $('<button class="btn btn-sm btn-danger file-delete"><i class="fas fa-trash"></i></button>');
//             deleteBtn.on('click', function(e) {
//                 e.stopPropagation();
//                 files.splice(index, 1);
//                 updateFileDisplay();
//                 updateHiddenInput();
//             });
            
//             fileItem.append(deleteBtn);
//             grid.append(fileItem);
//         });
//     }
    
//     function formatFileSize(bytes) {
//         if (bytes === 0) return '0 Bytes';
//         const k = 1024;
//         const sizes = ['Bytes', 'KB', 'MB', 'GB'];
//         const i = Math.floor(Math.log(bytes) / Math.log(k));
//         return parseFloat((bytes / Math.pow(k, i)).toFixed(2) + ' ' + sizes[i];
//     }
    
//     function updateHiddenInput() {
//         $('#selected_{$name}').val(JSON.stringify(files));
//     }
    
//     // Gestion des vues
//     container.find('.view-grid').on('click', function() {
//         container.find('.file-manager-content').removeClass('list-view').addClass('grid-view');
//         $(this).addClass('active').siblings().removeClass('active');
//     });
    
//     container.find('.view-list').on('click', function() {
//         container.find('.file-manager-content').removeClass('grid-view').addClass('list-view');
//         $(this).addClass('active').siblings().removeClass('active');
//     });
// });
// </script>
// HTML;
//     }

    /**
     * Script pour Signature Pad
     */
    private static function getSignaturePadScript($name, $options)
    {
        $optionsJson = json_encode($options);
        
        return <<<HTML
<script>
$(document).ready(function() {
    const canvas = $('#{$name}_signature')[0];
    const ctx = canvas.getContext('2d');
    const hiddenInput = $('#data_{$name}');
    const options = {$optionsJson};
    let isDrawing = false;
    let lastX = 0;
    let lastY = 0;
    
    // Initialisation
    ctx.fillStyle = options.background_color;
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.strokeStyle = options.pen_color;
    ctx.lineWidth = options.pen_width;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    
    // Dessin
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);
    
    // Pour les écrans tactiles
    canvas.addEventListener('touchstart', handleTouch);
    canvas.addEventListener('touchmove', handleTouch);
    canvas.addEventListener('touchend', stopDrawing);
    
    function handleTouch(e) {
        e.preventDefault();
        const touch = e.touches[0];
        const mouseEvent = new MouseEvent(
            e.type === 'touchstart' ? 'mousedown' : 'mousemove',
            {
                clientX: touch.clientX,
                clientY: touch.clientY
            }
        );
        canvas.dispatchEvent(mouseEvent);
    }
    
    function startDrawing(e) {
        isDrawing = true;
        [lastX, lastY] = [e.offsetX, e.offsetY];
    }
    
    function draw(e) {
        if (!isDrawing) return;
        ctx.beginPath();
        ctx.moveTo(lastX, lastY);
        ctx.lineTo(e.offsetX, e.offsetY);
        ctx.stroke();
        [lastX, lastY] = [e.offsetX, e.offsetY];
    }
    
    function stopDrawing() {
        isDrawing = false;
        updateHiddenInput();
    }
    
    // Bouton Effacer
    $('.clear-signature[data-target="{$name}_signature"]').on('click', function() {
        ctx.fillStyle = options.background_color;
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        hiddenInput.val('');
    });
    
    // Bouton Sauvegarder
    $('.save-signature[data-target="{$name}_signature"]').on('click', function() {
        updateHiddenInput();
    });
    
    function updateHiddenInput() {
        hiddenInput.val(canvas.toDataURL('image/png'));
    }
});
</script>
HTML;
    }


  


}