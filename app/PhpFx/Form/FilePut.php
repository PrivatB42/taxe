<?php

namespace App\PhpFx\Form;

class FilePut {
    private $cssEnabled = false;
    private static $instanceCount = 0;
    
    public function __construct() {
        self::$instanceCount++;

        if (self::$instanceCount === 1) {
        echo $this->getGlobalScript();
        }
    }

    public function pushGlobalScript() {
        echo $this->getGlobalScript();
    }
    
    /**
     * Active le CSS par défaut
     */
    public function pushCss() {
        $this->cssEnabled = true;
        return $this;
    }

        private function buildAttributes($attributes)
    {
        $html = '';
        foreach ($attributes as $name => $value) {
            if ($name === 'id') continue;

            if (is_bool($value)) {
                if ($value) {
                    $html .= ' ' . $name;
                }
            } else {
                $html .= ' ' . $name . '="' . htmlspecialchars($value) . '"';
            }
        }
        return $html;
    }

    private function tagRequired($attributes = []) {
        if (in_array('required', $attributes)) {
            return '<span class="text-danger"> *</span>';
        }
        return '';
    }
    
    /**
     * Génère le CSS par défaut
     */
    private function getDefaultCss() {
        if (!$this->cssEnabled) return '';
        
        return '
        <style>
        .fileput-container {
            position: relative;
           /* margin: 20px 0;*/
        }
        
       /* .form-label {
            display: block;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
            font-size: 16px;
        }*/
        
        .fileput-dropzone {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            background: #fafafa;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .fileput-dropzone:hover {
            border-color: #007cba;
            background: #f0f8ff;
        }
        
        .fileput-dropzone.dragover {
            border-color: #007cba;
            background: #e6f3ff;
        }
        
        .fileput-input {
            display: none;
        }
        
        .fileput-preview {
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .fileput-item {
            position: relative;
            display: inline-block;
            margin: 5px;
        }
        
        .fileput-image {
            width: 10rem;
            height: 10rem;
            object-fit: cover;
            border-radius: 6px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            border: 2px solid #e0e0e0;
        }
        
        .fileput-file-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            background: #f8f9fa;
            border-radius: 6px;
            margin-bottom: 8px;
            justify-content: space-between;
            border: 1px solid #dee2e6;
        }
        
        .fileput-file-name {
            flex: 1;
            margin-right: 15px;
            font-weight: 500;
        }
        
        .fileput-remove {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 6px 12px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.2s;
        }
        
        .fileput-remove:hover {
            background: #c82333;
        }
        
        .fileput-image-remove {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            transition: background 0.2s;
        }
        
        .fileput-image-remove:hover {
            background: #c82333;
            transform: scale(1.1);
        }
        
        .fileput-cropper-wrapper {
            display: flex;
            gap: 30px;
            margin: 25px 0;
            flex-wrap: wrap;
            align-items: flex-start;
        }
        
        .fileput-cropper-container {
            flex: 1;
            min-width: 300px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        
        .fileput-cropper-canvas {
            border: 2px solid #007cba;
            border-radius: 6px;
            max-width: 100%;
            cursor: crosshair;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .fileput-crop-preview-container {
            flex: 1;
            min-width: 300px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            display: none;
        }
        
        .fileput-cropper-controls {
            margin-top: 15px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .fileput-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .fileput-btn-primary {
            background: #007cba;
            color: white;
        }
        
        .fileput-btn-primary:hover {
            background: #005a87;
        }
        
        .fileput-btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .fileput-btn-secondary:hover {
            background: #545b62;
        }
        
        .fileput-upload-text {
            color: #666;
            font-size: 14px;
        }
        
        .fileput-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        
        .fileput-table th,
        .fileput-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .fileput-table th {
            background-color: #007cba;
            color: white;
            font-weight: 600;
        }
        
        .fileput-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .fileput-crop-preview {
            text-align: center;
        }
        
        .fileput-crop-preview img {
            max-width: 100%;
            max-height: 300px;
            border: 2px solid #28a745;
            border-radius: 6px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.2);
        }
        
        .fileput-crop-success {
            color: #28a745;
            font-weight: bold;
            margin-top: 10px;
            text-align: center;
        }
        
        .fileput-crop-dimensions {
            text-align: center;
            margin: 10px 0;
            font-weight: bold;
            color: #007cba;
        }
        
        @media (max-width: 768px) {
            .fileput-cropper-wrapper {
                flex-direction: column;
            }
            
            .fileput-cropper-container,
            .fileput-crop-preview-container {
                min-width: 100%;
            }
        }
        </style>';
    }
    
    /**
     * Génère un input pour images avec prévisualisation en galerie
     */
    public function inputImage(string $inputId, $options = []) {
        $defaults = [
            'name' => 'images',
            'label' => 'Images',
            'attributes' => [],
            'multi' => true,
            'existingImage' => null
        ];
        
        $options = array_merge($defaults, $options);
         $id = 'fileput_image_' . $inputId;
         $attributes = $this->buildAttributes($options['attributes']);
        $multiple = $options['multi'] ? 'multiple' : '';
        $accept = 'accept="image/*"';
        
        $html = $this->getDefaultCss();
        
        $html .= '<div class="fileput-container">';
        $html .= '<label class="form-label" for="' . $id . '">' . htmlspecialchars($options['label']) . $this->tagRequired($options['attributes']) . '</label>';
        
        if (!$options['multi']) {
            // Mode single - l'image s'affiche dans la dropzone
            $html .= '<div class="fileput-dropzone mb-3" id="dropzone_' . $id . '" onclick="document.getElementById(\'' . $id . '\').click()">';
            $html .= '<input type="file" id="' . $id . '" name="' . $options['name'] . '" class="fileput-input" ' . $accept . ' ' . $attributes . '>';
            $html .= '<div class="fileput-upload-text" id="upload_text_' . $id . '">Cliquez ou glissez-déposez votre image ici</div>';
            $html .= '</div>';
        } else {
            // Mode multi - zone de drop classique avec galerie
            $html .= '<div class="fileput-dropzone mb-3" onclick="document.getElementById(\'' . $id . '\').click()">';
            $html .= '<input type="file" id="' . $id . '" name="' . $options['name'] . '[]" class="fileput-input" ' . $multiple . ' ' . $accept . ' ' . $attributes . '>';
            $html .= '<div class="fileput-upload-text">  Cliquez ou glissez-déposez vos images ici</div>';
            $html .= '</div>';
            $html .= '<div id="preview_' . $id . '" class="fileput-preview"></div>';

        }
        
        $html .= '</div>';

        $html .= $this->getImageScript($id, $options['multi'], $options['existingImage']);
        
        return $html;
    }
    
    /**
     * Génère un input pour fichiers génériques
     */
    public function inputFile(string $inputId, $options = []) {
        $defaults = [
            'name' => 'files',
            'label' => 'Fichiers',
            'attributes' => [],
            'multi' => true,
        ];
        
        $options = array_merge($defaults, $options);
       $id = 'fileput_file_' . $inputId;
       $attributes = $this->buildAttributes($options['attributes']);
        $multiple = $options['multi'] ? 'multiple' : '';
        
        $html = $this->getDefaultCss();
        
        $html .= '<div class="fileput-container mb-3">';
        $html .= '<label class="form-label" for="' . $id . '">' . htmlspecialchars($options['label']) . $this->tagRequired($options['attributes']) . '</label>';
        $html .= '<div class="fileput-dropzone" onclick="document.getElementById(\'' . $id . '\').click()">';
        $html .= '<input type="file" id="' . $id . '" name="' . $options['name'] . ($options['multi'] ? '[]' : '') . '" class="fileput-input" ' . $multiple . ' ' . $attributes . '>';
        $html .= '<div class="fileput-upload-text">Cliquez ou glissez-déposez vos fichiers ici</div>';
        $html .= '</div>';
        $html .= '<table id="preview_' . $id . '" class="fileput-table" style="display: none;">';
        $html .= '<thead><tr><th>Nom du fichier</th><th>Taille</th><th>Type</th><th>Action</th></tr></thead>';
        $html .= '<tbody id="preview_body_' . $id . '"></tbody>';
        $html .= '</table>';
        $html .= '</div>';
        
        $html .= $this->getFileScript($id, $options['multi']);
        
        return $html;
    }


    /**
 * Génère un input avec crop d'image (version améliorée)
 */
public function inputCropper(string $inputId, $options = []) {
    $defaults = [
        'name' => 'cropped_image',
        'label' => 'Image à recadrer',
        'attributes' => [],
        'width' => 400,
        'height' => 300,
        'aspectRatio' => null,
        'output' => 'base64', // 'base64' ou 'file'
    ];
    
    $options = array_merge($defaults, $options);
    $attributes = $this->buildAttributes($options['attributes']);
     $id = 'fileput_cropper_' . $inputId;
    
    $html = $this->getDefaultCss();
    
    $html .= '<div class="fileput-container mb-3">';
    $html .= '<label class="form-label" for="' . $id . '">' . htmlspecialchars($options['label']) . $this->tagRequired($options['attributes']) . '</label>';
    $html .= '<div class="fileput-dropzone" onclick="document.getElementById(\'' . $id . '\').click()">';
    $html .= '<input type="file" id="' . $id . '" name="temp_image" class="fileput-input" accept="image/*" ' . $attributes . '>';
    $html .= '<div class="fileput-upload-text">Choisissez une image à recadrer</div>';
    $html .= '</div>';
    
    $html .= '<div id="cropper_wrapper_' . $id . '" class="fileput-cropper-wrapper" style="display:none;">';
    
    // Container pour le canvas de recadrage
    $html .= '<div class="fileput-cropper-container">';
    $html .= '<div style="text-align: center; margin-bottom: 15px; font-weight: bold; color: #007cba;">Sélectionnez la zone à recadrer</div>';
    $html .= '<canvas id="canvas_' . $id . '" class="fileput-cropper-canvas"></canvas>';
    $html .= '<div class="fileput-cropper-controls">';
    $html .= '<button type="button" class="fileput-btn fileput-btn-primary" onclick="cropImage_' . $id . '()">Recadrer</button>';
    $html .= '<button type="button" class="fileput-btn fileput-btn-secondary" onclick="resetCropper_' . $id . '()">Annuler</button>';
    $html .= '</div>';
    $html .= '</div>';
    
    // Container pour le preview du résultat
    $html .= '<div id="crop_preview_' . $id . '" class="fileput-crop-preview-container">';
    $html .= '<div style="text-align: center; margin-bottom: 15px; font-weight: bold; color: #28a745;">Aperçu du résultat</div>';
    $html .= '<div id="preview_content_' . $id . '" class="fileput-crop-preview"></div>';
    $html .= '<div id="crop_dimensions_' . $id . '" class="fileput-crop-dimensions"></div>';
    $html .= '</div>';
    
    $html .= '</div>'; // fin du wrapper
    
    // Champ différent selon le type de output
    if ($options['output'] === 'file') {
        $html .= '<input type="file" id="cropped_' . $id . '" name="' . $options['name'] . '" style="display: none;">';
        $html .= '<input type="hidden" id="cropped_filename_' . $id . '" name="' . $options['name'] . '_filename" value="">';
    } else {
        $html .= '<input type="hidden" id="cropped_' . $id . '" name="' . $options['name'] . '">';
    }
    
    $html .= '</div>';
    
    $html .= $this->getCropperScript($id, $options['width'], $options['height'], $options['aspectRatio'], $options['output']);
    
    return $html;
}

    /**
 * Génère le JavaScript pour les images avec support des images existantes
 */
private function getImageScript($id, $multi, $existingImage = null) {
   // $existingImagesJson = json_encode($existingImages);
    
    if (!$multi) {
        // Mode single
        return '
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const input = document.getElementById("' . $id . '");
            const dropzone = document.getElementById("dropzone_' . $id . '");
            const uploadText = document.getElementById("upload_text_' . $id . '");
            let currentFile = "'. $existingImage .'";
            
           if (currentFile) {
                updatePreview();
            }
            
            dropzone.addEventListener("dragover", function(e) {
                e.preventDefault();
                dropzone.classList.add("dragover");
            });
            
            dropzone.addEventListener("dragleave", function(e) {
                e.preventDefault();
                dropzone.classList.remove("dragover");
            });
            
            dropzone.addEventListener("drop", function(e) {
                e.preventDefault();
                dropzone.classList.remove("dragover");
                const droppedFiles = Array.from(e.dataTransfer.files);
                handleFiles(droppedFiles);
            });
            
            input.addEventListener("change", function(e) {
                const selectedFiles = Array.from(e.target.files);
                handleFiles(selectedFiles);
            });
            
            function handleFiles(newFiles) {
                const imageFiles = newFiles.filter(file => file.type.startsWith("image/"));
                if (imageFiles.length > 0) {
                    currentFile = imageFiles[0];
                    updatePreview();
                    updateInput();
                }
            }
            
            function updatePreview() {
                if (currentFile) {
                    if (typeof currentFile === "string") {
                        // Image existante
                        uploadText.innerHTML = \'<img src="\' + currentFile + \'" style="max-width: 100%; max-height: 200px; border-radius: 4px;"><br><small>Cliquez pour changer</small>\';
                    } else {
                        // Nouveau fichier
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            uploadText.innerHTML = \'<img src="\' + e.target.result + \'" style="max-width: 100%; max-height: 200px; border-radius: 4px;"><br><small>Cliquez pour changer</small>\';
                        };
                        reader.readAsDataURL(currentFile);
                    }
                }
            }
            
            function updateInput() {
                const dt = new DataTransfer();
                
                if (currentFile && currentFile instanceof File) {
                    dt.items.add(currentFile);
                }
                input.files = dt.files;
            }
        });
        </script>';
    } else {
        // Mode multi
        return '
          <script>
            document.addEventListener("DOMContentLoaded", function() {
                const input = document.getElementById("' . $id . '");
                const dropzone = input.parentElement;
                const preview = document.getElementById("preview_' . $id . '");
                let files = [];
                
                dropzone.addEventListener("dragover", function(e) {
                    e.preventDefault();
                    dropzone.classList.add("dragover");
                });
                
                dropzone.addEventListener("dragleave", function(e) {
                    e.preventDefault();
                    dropzone.classList.remove("dragover");
                });
                
                dropzone.addEventListener("drop", function(e) {
                    e.preventDefault();
                    dropzone.classList.remove("dragover");
                    const droppedFiles = Array.from(e.dataTransfer.files);
                    handleFiles(droppedFiles);
                });
                
                input.addEventListener("change", function(e) {
                    const selectedFiles = Array.from(e.target.files);
                    handleFiles(selectedFiles);
                });
                
                function handleFiles(newFiles) {
                    const imageFiles = newFiles.filter(file => file.type.startsWith("image/"));
                    files = files.concat(imageFiles);
                    updatePreview();
                    updateInput();
                }
                
                function updatePreview() {
                    preview.innerHTML = "";
                    files.forEach((file, index) => {
                        const div = document.createElement("div");
                        div.className = "fileput-item";
                        
                        const img = document.createElement("img");
                        img.className = "fileput-image";
                        img.src = URL.createObjectURL(file);
                        img.onload = function() {
                            URL.revokeObjectURL(this.src);
                        };
                        
                        const removeBtn = document.createElement("button");
                        removeBtn.className = "fileput-image-remove";
                        removeBtn.innerHTML = "×";
                        removeBtn.type = "button";
                        removeBtn.onclick = (e) => {
                            e.stopPropagation();
                            removeFile(index);
                        };
                        
                        div.appendChild(img);
                        div.appendChild(removeBtn);
                        preview.appendChild(div);
                    });
                }
                
                function removeFile(index) {
                    files.splice(index, 1);
                    updatePreview();
                    updateInput();
                }
                
                function updateInput() {
                    const dt = new DataTransfer();
                    files.forEach(file => dt.items.add(file));
                    input.files = dt.files;
                }
            });
            </script>';
    }
}
    


    /**
 * Génère le JavaScript pour les fichiers avec support des fichiers existants
 */
private function getFileScript($id, $multi) {
    return '
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const input = document.getElementById("' . $id . '");
            const dropzone = input.parentElement;
            const previewTable = document.getElementById("preview_' . $id . '");
            const previewBody = document.getElementById("preview_body_' . $id . '");
            let files = [];
            
            dropzone.addEventListener("dragover", function(e) {
                e.preventDefault();
                dropzone.classList.add("dragover");
            });
            
            dropzone.addEventListener("dragleave", function(e) {
                e.preventDefault();
                dropzone.classList.remove("dragover");
            });
            
            dropzone.addEventListener("drop", function(e) {
                e.preventDefault();
                dropzone.classList.remove("dragover");
                const droppedFiles = Array.from(e.dataTransfer.files);
                handleFiles(droppedFiles);
            });
            
            input.addEventListener("change", function(e) {
                const selectedFiles = Array.from(e.target.files);
                handleFiles(selectedFiles);
            });
            
            function handleFiles(newFiles) {
                if (' . ($multi ? 'true' : 'false') . ') {
                    files = files.concat(newFiles);
                } else {
                    files = newFiles.slice(0, 1);
                }
                
                updatePreview();
                updateInput();
            }
            
            function updatePreview() {
                previewBody.innerHTML = "";
                
                if (files.length > 0) {
                    previewTable.style.display = "table";
                } else {
                    previewTable.style.display = "none";
                    return;
                }
                
                files.forEach((file, index) => {
                    const row = document.createElement("tr");
                    
                    const nameCell = document.createElement("td");
                    nameCell.textContent = file.name;
                    
                    const sizeCell = document.createElement("td");
                    sizeCell.textContent = formatFileSize(file.size);
                    
                    const typeCell = document.createElement("td");
                    typeCell.textContent = file.type || "Inconnu";
                    
                    const actionCell = document.createElement("td");
                    const removeBtn = document.createElement("button");
                    removeBtn.className = "fileput-remove";
                    removeBtn.innerHTML = "Supprimer";
                    removeBtn.type = "button";
                    removeBtn.onclick = () => removeFile(index);
                    
                    actionCell.appendChild(removeBtn);
                    
                    row.appendChild(nameCell);
                    row.appendChild(sizeCell);
                    row.appendChild(typeCell);
                    row.appendChild(actionCell);
                    previewBody.appendChild(row);
                });
            }
            
            function removeFile(index) {
                files.splice(index, 1);
                updatePreview();
                updateInput();
            }
            
            function updateInput() {
                const dt = new DataTransfer();
                files.forEach(file => dt.items.add(file));
                input.files = dt.files;
            }
            
            function formatFileSize(bytes) {
                if (bytes === 0) return "0 B";
                const k = 1024;
                const sizes = ["B", "KB", "MB", "GB"];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
            }
        });
        </script>';
}
    
    /**
 * Génère le JavaScript pour le cropper (version améliorée)
 */
private function getCropperScript($id, $width, $height, $aspectRatio = null, $outputType = 'base64') {
    return '
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        let cropperData_' . $id . ' = {
            canvas: null,
            ctx: null,
            img: null,
            cropping: false,
            startX: 0,
            startY: 0,
            endX: 0,
            endY: 0,
            scaleX: 1,
            scaleY: 1,
            originalFile: null
        };
        
        const input = document.getElementById("' . $id . '");
        const wrapper = document.getElementById("cropper_wrapper_' . $id . '");
        const canvas = document.getElementById("canvas_' . $id . '");
        const previewContainer = document.getElementById("crop_preview_' . $id . '");
        const previewContent = document.getElementById("preview_content_' . $id . '");
        const dimensionsDisplay = document.getElementById("crop_dimensions_' . $id . '");
        const outputField = document.getElementById("cropped_' . $id . '");
        ' . ($outputType === 'file' ? 'const filenameField = document.getElementById("cropped_filename_' . $id . '");' : '') . '
        
        input.addEventListener("change", function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith("image/")) {
                cropperData_' . $id . '.originalFile = file;
                loadImageForCrop(file);
            }
        });
        
        function loadImageForCrop(file) {
            const data = cropperData_' . $id . ';
            const reader = new FileReader();
            
            reader.onload = function(e) {
                data.img = new Image();
                data.img.onload = function() {
                    setupCropper();
                };
                data.img.src = e.target.result;
            };
            
            reader.readAsDataURL(file);
        }
        
        function setupCropper() {
            const data = cropperData_' . $id . ';
            
            wrapper.style.display = "flex";
            previewContainer.style.display = "block";
            
            // Calculer la taille du canvas avec maintien des proportions
            const maxWidth = ' . $width . ';
            const maxHeight = ' . $height . ';
            
            let canvasWidth = data.img.width;
            let canvasHeight = data.img.height;
            
            // Calculer l\'échelle
            data.scaleX = data.img.width / maxWidth;
            data.scaleY = data.img.height / maxHeight;
            
            const scale = Math.min(maxWidth / data.img.width, maxHeight / data.img.height);
            canvasWidth = Math.floor(data.img.width * scale);
            canvasHeight = Math.floor(data.img.height * scale);
            
            canvas.width = canvasWidth;
            canvas.height = canvasHeight;
            
            data.canvas = canvas;
            data.ctx = canvas.getContext("2d");
            
            // Dessiner l\'image redimensionnée
            data.ctx.drawImage(data.img, 0, 0, canvasWidth, canvasHeight);
            
            // Gestion des événements de crop
            canvas.addEventListener("mousedown", startCrop);
            canvas.addEventListener("mousemove", updateCrop);
            canvas.addEventListener("mouseup", endCrop);
            canvas.addEventListener("mouseleave", endCrop);
            
            // Afficher le message d\'instructions
            previewContent.innerHTML = "<div style=\'text-align: center; padding: 40px; color: #666;\'>Sélectionnez une zone sur l\'image pour voir l\'aperçu ici</div>";
            dimensionsDisplay.innerHTML = "";
        }
        
        function startCrop(e) {
            const data = cropperData_' . $id . ';
            const rect = data.canvas.getBoundingClientRect();
            data.startX = e.clientX - rect.left;
            data.startY = e.clientY - rect.top;
            data.endX = data.startX;
            data.endY = data.startY;
            data.cropping = true;
            drawCropRect();
        }
        
        function updateCrop(e) {
            const data = cropperData_' . $id . ';
            if (!data.cropping) return;
            
            const rect = data.canvas.getBoundingClientRect();
            data.endX = Math.max(0, Math.min(e.clientX - rect.left, data.canvas.width));
            data.endY = Math.max(0, Math.min(e.clientY - rect.top, data.canvas.height));
            
            ' . ($aspectRatio ? '
            // Forcer le ratio d\'aspect
            const width = Math.abs(data.endX - data.startX);
            const desiredHeight = width / ' . $aspectRatio . ';
            
            if (data.endY > data.startY) {
                data.endY = Math.min(data.startY + desiredHeight, data.canvas.height);
            } else {
                data.endY = Math.max(data.startY - desiredHeight, 0);
            }
            ' : '') . '
            
            drawCropRect();
            updatePreviewInfo();
        }
        
        function endCrop() {
            const data = cropperData_' . $id . ';
            data.cropping = false;
        }
        
        function drawCropRect() {
            const data = cropperData_' . $id . ';
            const ctx = data.ctx;
            
            // Redessiner l\'image
            ctx.clearRect(0, 0, data.canvas.width, data.canvas.height);
            ctx.drawImage(data.img, 0, 0, data.canvas.width, data.canvas.height);
            
            // Dessiner le rectangle de crop
            if (data.startX !== data.endX || data.startY !== data.endY) {
                ctx.strokeStyle = "#007cba";
                ctx.lineWidth = 2;
                ctx.setLineDash([5, 5]);
                
                const x = Math.min(data.startX, data.endX);
                const y = Math.min(data.startY, data.endY);
                const width = Math.abs(data.endX - data.startX);
                const height = Math.abs(data.endY - data.startY);
                
                ctx.strokeRect(x, y, width, height);
                ctx.setLineDash([]);
            }
        }
        
        function updatePreviewInfo() {
            const data = cropperData_' . $id . ';
            if (data.startX !== data.endX && data.startY !== data.endY) {
                const width = Math.abs(data.endX - data.startX);
                const height = Math.abs(data.endY - data.startY);
                
                // Afficher les dimensions en pixels réels
                const realWidth = Math.round(width * data.scaleX);
                const realHeight = Math.round(height * data.scaleY);
                
                dimensionsDisplay.innerHTML = "Dimensions: " + realWidth + " × " + realHeight + " px";
            } else {
                dimensionsDisplay.innerHTML = "";
            }
        }
        
        window.cropImage_' . $id . ' = function() {
            const data = cropperData_' . $id . ';
            
            if (data.startX === data.endX && data.startY === data.endY) {
                alert("Veuillez sélectionner une zone à recadrer");
                return;
            }
            
            const x = Math.min(data.startX, data.endX);
            const y = Math.min(data.startY, data.endY);
            const width = Math.abs(data.endX - data.startX);
            const height = Math.abs(data.endY - data.startY);
            
            if (width < 10 || height < 10) {
                alert("La zone sélectionnée est trop petite");
                return;
            }
            
            // Créer un nouveau canvas pour le crop
            const cropCanvas = document.createElement("canvas");
            const cropCtx = cropCanvas.getContext("2d");
            cropCanvas.width = width;
            cropCanvas.height = height;
            
            // Calculer les proportions pour le crop de l\'image originale
            const scaleX = data.img.width / data.canvas.width;
            const scaleY = data.img.height / data.canvas.height;
            
            cropCtx.drawImage(
                data.img,
                x * scaleX,
                y * scaleY,
                width * scaleX,
                height * scaleY,
                0,
                0,
                width,
                height
            );
            
            if ("' . $outputType . '" === "file") {
                // Convertir en fichier
                cropCanvas.toBlob(function(blob) {
                    const fileName = "cropped_" + Date.now() + ".png";
                    const file = new File([blob], fileName, { type: "image/png" });
                    
                    // Créer un DataTransfer pour mettre le fichier dans l\'input
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    outputField.files = dt.files;
                    filenameField.value = fileName;
                    
                    // Afficher l\'aperçu
                    const previewUrl = URL.createObjectURL(blob);
                    previewContent.innerHTML = \'<img src="\' + previewUrl + \'" alt="Image recadrée">\';
                    dimensionsDisplay.innerHTML = \'<div class="fileput-crop-success">✓ Image recadrée avec succès! (Fichier: \' + fileName + \')</div>\';
                    
                    // Nettoyer l\'URL après affichage
                    setTimeout(() => URL.revokeObjectURL(previewUrl), 1000);
                }, "image/png");
            } else {
                // Convertir en base64
                const croppedData = cropCanvas.toDataURL("image/png");
                outputField.value = croppedData;
                
                // Afficher l\'aperçu
                previewContent.innerHTML = \'<img src="\' + croppedData + \'" alt="Image recadrée">\';
                dimensionsDisplay.innerHTML = \'<div class="fileput-crop-success">✓ Image recadrée avec succès! (Base64)</div>\';
            }
        };
        
        window.resetCropper_' . $id . ' = function() {
            wrapper.style.display = "none";
            previewContainer.style.display = "none";
            outputField.value = "";
            ' . ($outputType === 'file' ? '
            filenameField.value = ""; 
            outputField.files = new DataTransfer().files;
            ' : '') . '
            input.value = "";
            cropperData_' . $id . '.originalFile = null;
            
            cropperData_' . $id . ' = {
                canvas: null,
                ctx: null,
                img: null,
                cropping: false,
                startX: 0,
                startY: 0,
                endX: 0,
                endY: 0,
                scaleX: 1,
                scaleY: 1,
                originalFile: null
            };
        };
    });
    </script>';
 }
    
    // Les autres méthodes restent inchangées...
    /**
     * Traite les fichiers uploadés
     */
    public static function processUpload($inputName, $uploadDir = 'uploads/') {
        if (!isset($_FILES[$inputName])) {
            return false;
        }
        
        $files = $_FILES[$inputName];
        $uploadedFiles = [];
        
        // Créer le dossier s'il n'existe pas
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Gérer les uploads multiples
        if (is_array($files['name'])) {
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $filename = self::sanitizeFilename($files['name'][$i]);
                    $filepath = $uploadDir . $filename;
                    
                    if (move_uploaded_file($files['tmp_name'][$i], $filepath)) {
                        $uploadedFiles[] = $filepath;
                    }
                }
            }
        } else {
            // Upload simple
            if ($files['error'] === UPLOAD_ERR_OK) {
                $filename = self::sanitizeFilename($files['name']);
                $filepath = $uploadDir . $filename;
                
                if (move_uploaded_file($files['tmp_name'], $filepath)) {
                    $uploadedFiles[] = $filepath;
                }
            }
        }
        
        return $uploadedFiles;
    }
    
    /**
     * Traite une image croppée (base64)
     */
    public static function processCroppedImage($inputName, $uploadDir = 'uploads/') {
        if (!isset($_POST[$inputName]) || empty($_POST[$inputName])) {
            return false;
        }
        
        $base64Data = $_POST[$inputName];
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Data));
        
        if (!$imageData) {
            return false;
        }
        
        // Créer le dossier s'il n'existe pas
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $filename = 'cropped_' . uniqid() . '.png';
        $filepath = $uploadDir . $filename;
        
        if (file_put_contents($filepath, $imageData)) {
            return $filepath;
        }
        
        return false;
    }
    
    /**
     * Nettoie le nom de fichier
     */
    private static function sanitizeFilename($filename) {
        $info = pathinfo($filename);
        $name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $info['filename']);
        $extension = isset($info['extension']) ? '.' . $info['extension'] : '';
        return $name . '_' . uniqid() . $extension;
    }
    
    /**
     * Valide le type de fichier
     */
    public static function validateFileType($file, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx']) {
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        return in_array($extension, $allowedTypes);
    }
    
    /**
     * Valide la taille du fichier
     */
    public static function validateFileSize($file, $maxSize = 5242880) { // 5MB par défaut
        return $file['size'] <= $maxSize;
    }

    /**
 * Traite une image croppée selon le type de output
 */
public static function processCroppedOutput($inputName, $uploadDir = 'uploads/') {
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
        // Cas output: file
        $filename = self::sanitizeFilename($_FILES[$inputName]['name']);
        $filepath = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $filepath)) {
            return $filepath;
        }
    } 
    elseif (isset($_POST[$inputName]) && !empty($_POST[$inputName])) {
        // Cas output: base64
        $base64Data = $_POST[$inputName];
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Data));
        
        if (!$imageData) {
            return false;
        }
        
        // Créer le dossier s'il n'existe pas
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $filename = 'cropped_' . uniqid() . '.png';
        $filepath = $uploadDir . $filename;
        
        if (file_put_contents($filepath, $imageData)) {
            return $filepath;
        }
    }
    
    return false;
}



/**
 * Génère le JavaScript global pour les méthodes de gestion
 */
private function getGlobalScript() {
    return '
    <script>
    // Méthodes globales pour FilePut
    window.FilePut = {
        /**
         * Vide un input image multi
         */
        clearImagesInput: function(inputId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById("preview_" + inputId);
            const dropzone = input.closest(".fileput-container").querySelector(".fileput-dropzone");
            
            if (input) {
                // Réinitialiser l\'input file
                input.value = "";
                
                // Vider le preview
                if (preview) {
                    preview.innerHTML = "";
                }
                
                // Réinitialiser DataTransfer
                const dt = new DataTransfer();
                input.files = dt.files;
                
                // Réafficher le texte par défaut dans la dropzone
                const uploadText = dropzone.querySelector(".fileput-upload-text");
                if (uploadText && !dropzone.querySelector("img")) {
                    uploadText.textContent = "Cliquez ou glissez-déposez vos images ici";
                }
            }
        },

        /**
         * Vide un input image solo
         */
        clearImageInput: function(inputId) {
            const id = "fileput_image_" + inputId;
            const input = document.getElementById(id);
            const uploadText = document.getElementById("upload_text_" + id);
            
            if (input) {
                // Réinitialiser l\'input file
                input.value = "";
                
                // Réinitialiser DataTransfer
                const dt = new DataTransfer();
                input.files = dt.files;
                
                // Réafficher le texte par défaut
                if (uploadText) {
                    uploadText.textContent = "Cliquez ou glissez-déposez une image ici";
                }
            }
        },

        /**
         * afficher une image
         */
        showImage: function(inputId, image = null) {
            const id = "fileput_image_" + inputId;
            const uploadText = document.getElementById("upload_text_" + id);
            
            if (image && uploadText) {
                    uploadText.innerHTML = `<img src="${image}" style="max-width: 100%; max-height: 200px; border-radius: 4px;"><br><small>Cliquez pour changer</small>`;
            }
        },
        
        /**
         * Vide un input file
         */
        clearFileInput: function(inputId) {
            const input = document.getElementById(inputId);
            const previewTable = document.getElementById("preview_" + inputId);
            const previewBody = document.getElementById("preview_body_" + inputId);
            
            if (input) {
                // Réinitialiser l\'input file
                input.value = "";
                
                // Vider le preview
                if (previewBody) {
                    previewBody.innerHTML = "";
                }
                if (previewTable) {
                    previewTable.style.display = "none";
                }
                
                // Réinitialiser DataTransfer
                const dt = new DataTransfer();
                input.files = dt.files;
            }
        },
        
        /**
         * Vide un cropper
         */
        clearCropper: function(inputId) {
            const input = document.getElementById(inputId);
            const wrapper = document.getElementById("cropper_wrapper_" + inputId);
            const previewContainer = document.getElementById("crop_preview_" + inputId);
            const outputField = document.getElementById("cropped_" + inputId);
            const filenameField = document.getElementById("cropped_filename_" + inputId);
            
            if (input) {
                // Réinitialiser l\'input file
                input.value = "";
                
                // Cacher les containers
                if (wrapper) wrapper.style.display = "none";
                if (previewContainer) previewContainer.style.display = "none";
                
                // Vider les champs de sortie
                if (outputField) {
                    outputField.value = "";
                    if (outputField.tagName === "INPUT" && outputField.type === "file") {
                        const dt = new DataTransfer();
                        outputField.files = dt.files;
                    }
                }
                if (filenameField) filenameField.value = "";
            }
        },
        
        /**
         * Affiche les images existantes dans un input multi
         */
        showExistingImages: function(inputId, images) {
            const preview = document.getElementById("preview_" + inputId);
            if (!preview) return;
            
            preview.innerHTML = "";
            
            images.forEach((image, index) => {
                const div = document.createElement("div");
                div.className = "fileput-item";
                div.dataset.existing = index;
                
                const img = document.createElement("img");
                img.className = "fileput-image";
                img.src = image.url;
                img.alt = image.name || "Image existante";
                
                const removeBtn = document.createElement("button");
                removeBtn.className = "fileput-image-remove";
                removeBtn.innerHTML = "×";
                removeBtn.type = "button";
                removeBtn.onclick = function() {
                    FilePut.removeExistingImage(inputId, index);
                };
                
                // Champ caché pour l\'image existante
                const hiddenInput = document.createElement("input");
                hiddenInput.type = "hidden";
                hiddenInput.name = "existing_images[]";
                hiddenInput.value = image.url;
                
                div.appendChild(img);
                div.appendChild(removeBtn);
                div.appendChild(hiddenInput);
                preview.appendChild(div);
            });
        },
        
        /**
         * Affiche les fichiers existants
         */
        showExistingFiles: function(inputId, files) {
            const previewBody = document.getElementById("preview_body_" + inputId);
            const previewTable = document.getElementById("preview_" + inputId);
            
            if (!previewBody) return;
            
            previewBody.innerHTML = "";
            
            if (files.length > 0 && previewTable) {
                previewTable.style.display = "table";
            }
            
            files.forEach((file, index) => {
                const row = document.createElement("tr");
                row.dataset.existing = index;
                
                const nameCell = document.createElement("td");
                nameCell.textContent = file.name;
                
                const sizeCell = document.createElement("td");
                sizeCell.textContent = file.size || "N/A";
                
                const typeCell = document.createElement("td");
                typeCell.textContent = file.type || "Inconnu";
                
                const actionCell = document.createElement("td");
                const removeBtn = document.createElement("button");
                removeBtn.className = "fileput-remove";
                removeBtn.innerHTML = "Supprimer";
                removeBtn.type = "button";
                removeBtn.onclick = function() {
                    FilePut.removeExistingFile(inputId, index);
                };
                
                // Champ caché pour le fichier existant
                const hiddenInput = document.createElement("input");
                hiddenInput.type = "hidden";
                hiddenInput.name = "existing_files[]";
                hiddenInput.value = file.url;
                
                actionCell.appendChild(removeBtn);
                actionCell.appendChild(hiddenInput);
                
                row.appendChild(nameCell);
                row.appendChild(sizeCell);
                row.appendChild(typeCell);
                row.appendChild(actionCell);
                previewBody.appendChild(row);
            });
        },
        
        /**
         * Supprime une image existante
         */
        removeExistingImage: function(inputId, index) {
            const item = document.querySelector(\'[data-existing="\' + index + \']\');
            if (item) {
                item.remove();
            }
        },
        
        /**
         * Supprime un fichier existant
         */
        removeExistingFile: function(inputId, index) {
            const row = document.querySelector(\'[data-existing="\' + index + \']\');
            if (row) {
                row.remove();
            }
        },
        
        /**
         * Affiche une image existante dans un cropper
         */
        showExistingInCropper: function(inputId, imageUrl) {
            const input = document.getElementById(inputId);
            const wrapper = document.getElementById("cropper_wrapper_" + inputId);
            const previewContainer = document.getElementById("crop_preview_" + inputId);
            
            if (!input || !wrapper) return;
            
            // Simuler un chargement d\'image
            const data = window[\'cropperData_\' + inputId];
            if (data) {
                data.img = new Image();
                data.img.onload = function() {
                    // Setup du cropper
                    const canvas = document.getElementById("canvas_" + inputId);
                    const maxWidth = 500;
                    const maxHeight = 500;
                    
                    let canvasWidth = data.img.width;
                    let canvasHeight = data.img.height;
                    
                    const scale = Math.min(maxWidth / data.img.width, maxHeight / data.img.height);
                    canvasWidth = Math.floor(data.img.width * scale);
                    canvasHeight = Math.floor(data.img.height * scale);
                    
                    canvas.width = canvasWidth;
                    canvas.height = canvasHeight;
                    
                    data.canvas = canvas;
                    data.ctx = canvas.getContext("2d");
                    data.ctx.drawImage(data.img, 0, 0, canvasWidth, canvasHeight);
                    
                    // Afficher les containers
                    wrapper.style.display = "flex";
                    previewContainer.style.display = "block";
                    
                    // Afficher l\'image dans le preview
                    const previewContent = document.getElementById("preview_content_" + inputId);
                    if (previewContent) {
                        previewContent.innerHTML = \'<img src="\' + imageUrl + \'" alt="Image existante">\';
                    }
                };
                data.img.src = imageUrl;
                data.img.crossOrigin = "Anonymous";
            }
        }
    };
    </script>';
}
}