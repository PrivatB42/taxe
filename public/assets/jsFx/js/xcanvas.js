/**
 * Bibliothèque XCanvas - Manipulation avancée du Canvas
 * @version 2.0
 * @license MIT
 */
const XCanvas = {
    // ======================== INITIALISATION ========================
    /**
     * Créer un nouveau canvas
     * @param {string|HTMLElement} container - ID ou élément conteneur
     * @param {number} width - Largeur du canvas
     * @param {number} height - Hauteur du canvas
     * @param {object} options - Options supplémentaires
     * @returns {object|null} { canvas, ctx } ou null en cas d'erreur
     */
    
    
    // create(container, width, height, options = {}) {
    //     try {
    //         if (!container) {
    //             throw new Error('Le conteneur doit être spécifié');
    //         }

    //         const containerEl = typeof container === 'string' 
    //             ? document.getElementById(container) 
    //             : container;
            
    //         if (!containerEl) {
    //             throw new Error(`Conteneur "${container}" introuvable`);
    //         }

    //         if (typeof width !== 'number' || typeof height !== 'number' || width <= 0 || height <= 0) {
    //             throw new Error('Les dimensions width et height doivent être des nombres positifs');
    //         }

    //         // Créer le canvas
    //         const canvas = document.createElement('canvas');
    //         canvas.width = width;
    //         canvas.height = height;
            
    //         // Appliquer les styles
    //         Object.assign(canvas.style, {
    //             border: options.border || '1px solid #000',
    //             backgroundColor: options.backgroundColor || 'transparent',
    //             display: 'block',
    //             ...options.style
    //         });

    //         // Ajouter au conteneur
    //         containerEl.appendChild(canvas);

    //         // Récupérer le contexte
    //         const ctx = canvas.getContext('2d', {
    //             willReadFrequently: options.willReadFrequently || false
    //         });

    //         if (!ctx) {
    //             throw new Error('Impossible d\'obtenir le contexte 2D du canvas');
    //         }

    //         // Sauvegarder les références
    //         canvas._xcanvas = {
    //             drawing: false,
    //             lastPos: { x: 0, y: 0 },
    //             tools: {},
    //             history: [],
    //             historyIndex: -1,
    //             events: {}
    //         };

    //         return { canvas, ctx };
    //     } catch (error) {
    //         console.error('XCanvas.create - Erreur:', error.message);
    //         return null;
    //     }
    // },

   
    
    

    // ======================== INITIALISATION ========================
    
    
    /**
     * Créer un nouveau canvas avec toolbar
     * @param {string|HTMLElement} container - ID ou élément conteneur
     * @param {number} width - Largeur du canvas
     * @param {number} height - Hauteur du canvas
     * @param {object} options - Options supplémentaires
     * @returns {object} { canvas, ctx, toolbar }
     */
    create(container, width, height, options = {}) {
        try {
            if (!container) throw new Error('Conteneur non spécifié');
            
            const containerEl = typeof container === 'string' 
                ? document.getElementById(container) 
                : container;
            
            if (!containerEl) throw new Error(`Conteneur "${container}" introuvable`);
            if (typeof width !== 'number' || typeof height !== 'number' || width <= 0 || height <= 0) {
                throw new Error('Dimensions invalides');
            }

            // Créer le wrapper
            const wrapper = document.createElement('div');
            wrapper.className = 'xcanvas-wrapper';
            wrapper.style.position = 'relative';
            
            // Créer la toolbar
            const toolbar = document.createElement('div');
            toolbar.className = 'xcanvas-toolbar';
            toolbar.style.display = 'flex';
            toolbar.style.flexWrap = 'wrap';
            toolbar.style.gap = '5px';
            toolbar.style.padding = '10px';
            toolbar.style.backgroundColor = options.toolbarBg || '#f0f0f0';
            toolbar.style.borderBottom = '1px solid #ccc';
            
            // Créer le canvas
            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;
            
            Object.assign(canvas.style, {
                border: options.border || '1px solid #000',
                backgroundColor: options.backgroundColor || 'transparent',
                display: 'block',
                ...options.style
            });

            // Assembler les éléments
            wrapper.appendChild(toolbar);
            wrapper.appendChild(canvas);
            containerEl.appendChild(wrapper);
            
            // Récupérer le contexte
            const ctx = canvas.getContext('2d', {
                willReadFrequently: options.willReadFrequently || false
            });
            
            if (!ctx) throw new Error('Impossible d\'obtenir le contexte 2D');

            // Sauvegarder les références
            canvas._xcanvas = {
                drawing: false,
                lastPos: { x: 0, y: 0 },
                tools: {},
                history: [],
                historyIndex: -1,
                events: {},
                toolbar: toolbar,
                wrapper: wrapper,
                currentTool: null,
                layers: [],
                activeLayer: null
            };

            // Créer un calque par défaut
            this.createLayer(canvas);

            return { canvas, ctx, toolbar };
        } catch (error) {
            console.error('XCanvas.create - Erreur:', error.message);
            return null;
        }
    },
    
    
    
    
    
     // ======================== OUTILS DE DESSIN ========================
    
    
    
    
    
    /**
     * Configurer le style de dessin
     * @param {CanvasRenderingContext2D} ctx - Contexte canvas
     * @param {object} options - Options de style
     * @returns {boolean} true si réussi, false sinon
     */
    setStyle(ctx, options = {}) {
        try {
            if (!ctx || !ctx.canvas) {
                throw new Error('Contexte canvas invalide');
            }

            if (options.fillStyle) ctx.fillStyle = options.fillStyle;
            if (options.strokeStyle) ctx.strokeStyle = options.strokeStyle;
            if (options.lineWidth) ctx.lineWidth = options.lineWidth;
            if (options.lineCap) ctx.lineCap = options.lineCap;
            if (options.lineJoin) ctx.lineJoin = options.lineJoin;
            if (options.globalAlpha) ctx.globalAlpha = options.globalAlpha;
            if (options.globalCompositeOperation) ctx.globalCompositeOperation = options.globalCompositeOperation;
            if (options.font) ctx.font = options.font;
            if (options.textAlign) ctx.textAlign = options.textAlign;
            if (options.textBaseline) ctx.textBaseline = options.textBaseline;
            if (options.direction) ctx.direction = options.direction;

            return true;
        } catch (error) {
            console.error('XCanvas.setStyle - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Dessiner une ligne
     * @param {CanvasRenderingContext2D} ctx - Contexte canvas
     * @param {number} x1 - Position X de départ
     * @param {number} y1 - Position Y de départ
     * @param {number} x2 - Position X d'arrivée
     * @param {number} y2 - Position Y d'arrivée
     * @param {object} options - Options de style
     * @returns {boolean} true si réussi, false sinon
     */
    drawLine(ctx, x1, y1, x2, y2, options = {}) {
        try {
            this.saveState(ctx);
            this.setStyle(ctx, options);
            
            ctx.beginPath();
            ctx.moveTo(x1, y1);
            ctx.lineTo(x2, y2);
            ctx.stroke();
            
            this.restoreState(ctx);
            return true;
        } catch (error) {
            console.error('XCanvas.drawLine - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Dessiner un rectangle
     * @param {CanvasRenderingContext2D} ctx - Contexte canvas
     * @param {number} x - Position X
     * @param {number} y - Position Y
     * @param {number} width - Largeur
     * @param {number} height - Hauteur
     * @param {object} options - Options de style et de dessin
     * @returns {boolean} true si réussi, false sinon
     */
    drawRect(ctx, x, y, width, height, options = {}) {
        try {
            this.saveState(ctx);
            this.setStyle(ctx, options);
            
            if (options.fill) {
                ctx.fillRect(x, y, width, height);
            }
            if (options.stroke) {
                ctx.strokeRect(x, y, width, height);
            }
            
            this.restoreState(ctx);
            return true;
        } catch (error) {
            console.error('XCanvas.drawRect - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Dessiner un cercle
     * @param {CanvasRenderingContext2D} ctx - Contexte canvas
     * @param {number} x - Position X du centre
     * @param {number} y - Position Y du centre
     * @param {number} radius - Rayon
     * @param {object} options - Options de style et de dessin
     * @returns {boolean} true si réussi, false sinon
     */
    drawCircle(ctx, x, y, radius, options = {}) {
        try {
            this.saveState(ctx);
            this.setStyle(ctx, options);
            
            ctx.beginPath();
            ctx.arc(x, y, radius, 0, Math.PI * 2);
            
            if (options.fill) {
                ctx.fill();
            }
            if (options.stroke) {
                ctx.stroke();
            }
            
            this.restoreState(ctx);
            return true;
        } catch (error) {
            console.error('XCanvas.drawCircle - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Dessiner un texte
     * @param {CanvasRenderingContext2D} ctx - Contexte canvas
     * @param {string} text - Texte à dessiner
     * @param {number} x - Position X
     * @param {number} y - Position Y
     * @param {object} options - Options de style et de texte
     * @returns {boolean} true si réussi, false sinon
     */
    drawText(ctx, text, x, y, options = {}) {
        try {
            this.saveState(ctx);
            
            this.setStyle(ctx, {
                font: options.font || '16px Arial',
                fillStyle: options.fillStyle || ctx.fillStyle,
                textAlign: options.textAlign || 'left',
                textBaseline: options.textBaseline || 'top',
                direction: options.direction || 'ltr'
            });
            
            if (options.maxWidth) {
                ctx.fillText(text, x, y, options.maxWidth);
            } else {
                ctx.fillText(text, x, y);
            }
            
            this.restoreState(ctx);
            return true;
        } catch (error) {
            console.error('XCanvas.drawText - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Effacer une zone du canvas
     * @param {CanvasRenderingContext2D} ctx - Contexte canvas
     * @param {number} x - Position X
     * @param {number} y - Position Y
     * @param {number} width - Largeur
     * @param {number} height - Hauteur
     * @returns {boolean} true si réussi, false sinon
     */
    clearArea(ctx, x = 0, y = 0, width = null, height = null) {
        try {
            if (!ctx || !ctx.canvas) {
                throw new Error('Contexte canvas invalide');
            }

            width = width || ctx.canvas.width;
            height = height || ctx.canvas.height;
            ctx.clearRect(x, y, width, height);
            return true;
        } catch (error) {
            console.error('XCanvas.clearArea - Erreur:', error.message);
            return false;
        }
    },

    // ======================== GESTION DES ÉTATS ========================
    /**
     * Sauvegarder l'état courant du contexte
     * @param {CanvasRenderingContext2D} ctx - Contexte canvas
     * @returns {boolean} true si réussi, false sinon
     */
    saveState(ctx) {
        try {
            if (!ctx || !ctx.canvas) {
                throw new Error('Contexte canvas invalide');
            }
            ctx.save();
            return true;
        } catch (error) {
            console.error('XCanvas.saveState - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Restaurer l'état précédent du contexte
     * @param {CanvasRenderingContext2D} ctx - Contexte canvas
     * @returns {boolean} true si réussi, false sinon
     */
    restoreState(ctx) {
        try {
            if (!ctx || !ctx.canvas) {
                throw new Error('Contexte canvas invalide');
            }
            ctx.restore();
            return true;
        } catch (error) {
            console.error('XCanvas.restoreState - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Sauvegarder l'état actuel du canvas dans l'historique
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @returns {boolean} true si réussi, false sinon
     */
    saveToHistory(canvas) {
        try {
            if (!canvas || !canvas._xcanvas) {
                throw new Error('Canvas invalide ou non initialisé avec XCanvas');
            }

            // Limiter la taille de l'historique
            if (canvas._xcanvas.historyIndex < canvas._xcanvas.history.length - 1) {
                canvas._xcanvas.history = canvas._xcanvas.history.slice(0, canvas._xcanvas.historyIndex + 1);
            }
            
            // Sauvegarder l'image actuelle
            const imageData = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height);
            canvas._xcanvas.history.push(imageData);
            canvas._xcanvas.historyIndex = canvas._xcanvas.history.length - 1;
            
            return true;
        } catch (error) {
            console.error('XCanvas.saveToHistory - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Annuler la dernière action
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @returns {boolean} true si réussi, false sinon
     */
    undo(canvas) {
        try {
            if (!canvas || !canvas._xcanvas || canvas._xcanvas.historyIndex <= 0) {
                return false;
            }
            
            canvas._xcanvas.historyIndex--;
            const imageData = canvas._xcanvas.history[canvas._xcanvas.historyIndex];
            canvas.getContext('2d').putImageData(imageData, 0, 0);
            return true;
        } catch (error) {
            console.error('XCanvas.undo - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Rétablir la dernière action annulée
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @returns {boolean} true si réussi, false sinon
     */
    redo(canvas) {
        try {
            if (!canvas || !canvas._xcanvas || canvas._xcanvas.historyIndex >= canvas._xcanvas.history.length - 1) {
                return false;
            }
            
            canvas._xcanvas.historyIndex++;
            const imageData = canvas._xcanvas.history[canvas._xcanvas.historyIndex];
            canvas.getContext('2d').putImageData(imageData, 0, 0);
            return true;
        } catch (error) {
            console.error('XCanvas.redo - Erreur:', error.message);
            return false;
        }
    },

    // ======================== OUTILS INTERACTIFS ========================
    /**
     * Activer le mode dessin libre
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {object} options - Options de dessin
     * @returns {boolean} true si réussi, false sinon
     */
    enableFreeDrawing(canvas, options = {}) {
        try {
            if (!canvas || !canvas._xcanvas) {
                throw new Error('Canvas invalide ou non initialisé avec XCanvas');
            }
            
            const ctx = canvas.getContext('2d');
            let isDrawing = false;
            let lastPos = { x: 0, y: 0 };
            
            // Configurer les options par défaut
            const config = {
                color: '#000000',
                size: 2,
                lineCap: 'round',
                lineJoin: 'round',
                ...options
            };
            
            // Gestionnaires d'événements
            const startDrawing = (e) => {
                isDrawing = true;
                lastPos = this.getMousePosition(canvas, e);
                this.saveToHistory(canvas);
            };
            
            const draw = (e) => {
                if (!isDrawing) return;
                
                const currentPos = this.getMousePosition(canvas, e);
                
                ctx.beginPath();
                ctx.lineCap = config.lineCap;
                ctx.lineJoin = config.lineJoin;
                ctx.lineWidth = config.size;
                ctx.strokeStyle = config.color;
                ctx.globalCompositeOperation = 'source-over';
                
                ctx.moveTo(lastPos.x, lastPos.y);
                ctx.lineTo(currentPos.x, currentPos.y);
                ctx.stroke();
                
                lastPos = currentPos;
            };
            
            const stopDrawing = () => {
                isDrawing = false;
            };
            
            // Supprimer les anciens écouteurs s'ils existent
            this.disableFreeDrawing(canvas);
            
            // Ajouter les écouteurs
            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDrawing);
            canvas.addEventListener('mouseout', stopDrawing);
            
            // Ajouter le support tactile
            canvas.addEventListener('touchstart', (e) => {
                e.preventDefault();
                startDrawing(e.touches[0]);
            });
            
            canvas.addEventListener('touchmove', (e) => {
                e.preventDefault();
                draw(e.touches[0]);
            });
            
            canvas.addEventListener('touchend', stopDrawing);
            
            // Sauvegarder les gestionnaires pour pouvoir les supprimer plus tard
            canvas._xcanvas.tools.freeDrawing = {
                handlers: { startDrawing, draw, stopDrawing },
                config
            };
            
            return true;
        } catch (error) {
            console.error('XCanvas.enableFreeDrawing - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Désactiver le mode dessin libre
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @returns {boolean} true si réussi, false sinon
     */
    disableFreeDrawing(canvas) {
        try {
            if (!canvas || !canvas._xcanvas || !canvas._xcanvas.tools.freeDrawing) {
                return false;
            }
            
            const { handlers } = canvas._xcanvas.tools.freeDrawing;
            
            canvas.removeEventListener('mousedown', handlers.startDrawing);
            canvas.removeEventListener('mousemove', handlers.draw);
            canvas.removeEventListener('mouseup', handlers.stopDrawing);
            canvas.removeEventListener('mouseout', handlers.stopDrawing);
            
            // Supprimer les écouteurs tactiles
            canvas.removeEventListener('touchstart', handlers.startDrawing);
            canvas.removeEventListener('touchmove', handlers.draw);
            canvas.removeEventListener('touchend', handlers.stopDrawing);
            
            delete canvas._xcanvas.tools.freeDrawing;
            return true;
        } catch (error) {
            console.error('XCanvas.disableFreeDrawing - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Activer la gomme
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {object} options - Options de la gomme
     * @returns {boolean} true si réussi, false sinon
     */
    enableEraser(canvas, options = {}) {
        try {
            if (!canvas || !canvas._xcanvas) {
                throw new Error('Canvas invalide ou non initialisé avec XCanvas');
            }
            
            const ctx = canvas.getContext('2d');
            let isErasing = false;
            let lastPos = { x: 0, y: 0 };
            
            // Configurer les options par défaut
            const config = {
                size: 10,
                lineCap: 'round',
                ...options
            };
            
            // Gestionnaires d'événements
            const startErasing = (e) => {
                isErasing = true;
                lastPos = this.getMousePosition(canvas, e);
                this.saveToHistory(canvas);
            };
            
            const erase = (e) => {
                if (!isErasing) return;
                
                const currentPos = this.getMousePosition(canvas, e);
                
                ctx.beginPath();
                ctx.lineCap = config.lineCap;
                ctx.lineWidth = config.size;
                ctx.globalCompositeOperation = 'destination-out';
                
                ctx.moveTo(lastPos.x, lastPos.y);
                ctx.lineTo(currentPos.x, currentPos.y);
                ctx.stroke();
                
                lastPos = currentPos;
            };
            
            const stopErasing = () => {
                isErasing = false;
            };
            
            // Supprimer les anciens écouteurs s'ils existent
            this.disableEraser(canvas);
            
            // Ajouter les écouteurs
            canvas.addEventListener('mousedown', startErasing);
            canvas.addEventListener('mousemove', erase);
            canvas.addEventListener('mouseup', stopErasing);
            canvas.addEventListener('mouseout', stopErasing);
            
            // Ajouter le support tactile
            canvas.addEventListener('touchstart', (e) => {
                e.preventDefault();
                startErasing(e.touches[0]);
            });
            
            canvas.addEventListener('touchmove', (e) => {
                e.preventDefault();
                erase(e.touches[0]);
            });
            
            canvas.addEventListener('touchend', stopErasing);
            
            // Sauvegarder les gestionnaires pour pouvoir les supprimer plus tard
            canvas._xcanvas.tools.eraser = {
                handlers: { startErasing, erase, stopErasing },
                config
            };
            
            return true;
        } catch (error) {
            console.error('XCanvas.enableEraser - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Désactiver la gomme
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @returns {boolean} true si réussi, false sinon
     */
    disableEraser(canvas) {
        try {
            if (!canvas || !canvas._xcanvas || !canvas._xcanvas.tools.eraser) {
                return false;
            }
            
            const { handlers } = canvas._xcanvas.tools.eraser;
            
            canvas.removeEventListener('mousedown', handlers.startErasing);
            canvas.removeEventListener('mousemove', handlers.erase);
            canvas.removeEventListener('mouseup', handlers.stopErasing);
            canvas.removeEventListener('mouseout', handlers.stopErasing);
            
            // Supprimer les écouteurs tactiles
            canvas.removeEventListener('touchstart', handlers.startErasing);
            canvas.removeEventListener('touchmove', handlers.erase);
            canvas.removeEventListener('touchend', handlers.stopErasing);
            
            delete canvas._xcanvas.tools.eraser;
            return true;
        } catch (error) {
            console.error('XCanvas.disableEraser - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Activer le remplissage (pot de peinture)
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {object} options - Options de remplissage
     * @returns {boolean} true si réussi, false sinon
     */
    enableFillTool(canvas, options = {}) {
        try {
            if (!canvas || !canvas._xcanvas) {
                throw new Error('Canvas invalide ou non initialisé avec XCanvas');
            }
            
            const ctx = canvas.getContext('2d');
            
            // Configurer les options par défaut
            const config = {
                color: '#000000',
                tolerance: 0,
                ...options
            };
            
            // Gestionnaire d'événement
            const fill = (e) => {
                const pos = this.getMousePosition(canvas, e);
                this.saveToHistory(canvas);
                
                // Récupérer la couleur du pixel cliqué
                const pixel = ctx.getImageData(pos.x, pos.y, 1, 1).data;
                const targetColor = `rgb(${pixel[0]}, ${pixel[1]}, ${pixel[2]})`;
                
                // Créer une image temporaire pour le remplissage
                const tempCanvas = document.createElement('canvas');
                tempCanvas.width = canvas.width;
                tempCanvas.height = canvas.height;
                const tempCtx = tempCanvas.getContext('2d');
                
                // Copier le canvas actuel
                tempCtx.drawImage(canvas, 0, 0);
                
                // Configurer le style
                tempCtx.fillStyle = config.color;
                tempCtx.globalCompositeOperation = 'source-over';
                
                // Remplir (implémentation simplifiée)
                tempCtx.fillRect(pos.x - 5, pos.y - 5, 10, 10); // Version simplifiée
                
                // Copier le résultat sur le canvas principal
                ctx.drawImage(tempCanvas, 0, 0);
            };
            
            // Supprimer l'ancien écouteur s'il existe
            this.disableFillTool(canvas);
            
            // Ajouter l'écouteur
            canvas.addEventListener('click', fill);
            
            // Sauvegarder le gestionnaire pour pouvoir le supprimer plus tard
            canvas._xcanvas.tools.fill = {
                handler: fill,
                config
            };
            
            return true;
        } catch (error) {
            console.error('XCanvas.enableFillTool - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Désactiver le remplissage
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @returns {boolean} true si réussi, false sinon
     */
    disableFillTool(canvas) {
        try {
            if (!canvas || !canvas._xcanvas || !canvas._xcanvas.tools.fill) {
                return false;
            }
            
            const { handler } = canvas._xcanvas.tools.fill;
            canvas.removeEventListener('click', handler);
            delete canvas._xcanvas.tools.fill;
            return true;
        } catch (error) {
            console.error('XCanvas.disableFillTool - Erreur:', error.message);
            return false;
        }
    },

    // ======================== NOUVELLES FONCTIONNALITÉS ========================
    /**
     * Dessiner une image dans le canvas
     * @param {CanvasRenderingContext2D} ctx - Contexte canvas
     * @param {string|HTMLImageElement} image - URL ou élément image
     * @param {number} x - Position X
     * @param {number} y - Position Y
     * @param {number} width - Largeur
     * @param {number} height - Hauteur
     * @param {function} callback - Fonction de rappel
     * @returns {boolean} true si réussi, false sinon
     */
    drawImage(ctx, image, x, y, width, height, callback = null) {
        try {
            if (!ctx || !ctx.canvas) {
                throw new Error('Contexte canvas invalide');
            }

            if (typeof image === 'string') {
                const img = new Image();
                img.onload = () => {
                    ctx.drawImage(img, x, y, width, height);
                    callback?.(true);
                };
                img.onerror = () => {
                    callback?.(false);
                };
                img.src = image;
            } else if (image instanceof HTMLImageElement) {
                ctx.drawImage(image, x, y, width, height);
                callback?.(true);
            } else {
                throw new Error('Type d\'image non supporté');
            }
            
            return true;
        } catch (error) {
            console.error('XCanvas.drawImage - Erreur:', error.message);
            callback?.(false);
            return false;
        }
    },

    /**
     * Dessiner une forme polygonale
     * @param {CanvasRenderingContext2D} ctx - Contexte canvas
     * @param {Array} points - Tableau de points [{x, y}, ...]
     * @param {object} options - Options de style et de dessin
     * @returns {boolean} true si réussi, false sinon
     */
    drawPolygon(ctx, points, options = {}) {
        try {
            if (!ctx || !ctx.canvas) {
                throw new Error('Contexte canvas invalide');
            }

            if (!Array.isArray(points)) {
                throw new Error('Les points doivent être un tableau');
            }

            this.saveState(ctx);
            this.setStyle(ctx, options);
            
            ctx.beginPath();
            ctx.moveTo(points[0].x, points[0].y);
            
            for (let i = 1; i < points.length; i++) {
                ctx.lineTo(points[i].x, points[i].y);
            }
            
            ctx.closePath();
            
            if (options.fill) {
                ctx.fill();
            }
            if (options.stroke) {
                ctx.stroke();
            }
            
            this.restoreState(ctx);
            return true;
        } catch (error) {
            console.error(XCanvas.drawPolygon , error.message);
            return false;
        }
    },

    /**
     * Dessiner une courbe de Bézier
     * @param {CanvasRenderingContext2D} ctx - Contexte canvas
     * @param {number} startX - Position X de départ
     * @param {number} startY - Position Y de départ
     * @param {number} cp1X - Position X du premier point de contrôle
     * @param {number} cp1Y - Position Y du premier point de contrôle
     * @param {number} cp2X - Position X du deuxième point de contrôle
     * @param {number} cp2Y - Position Y du deuxième point de contrôle
     * @param {number} endX - Position X d'arrivée
     * @param {number} endY - Position Y d'arrivée
     * @param {object} options - Options de style
     * @returns {boolean} true si réussi, false sinon
     */
    drawBezierCurve(ctx, startX, startY, cp1X, cp1Y, cp2X, cp2Y, endX, endY, options = {}) {
        try {
            this.saveState(ctx);
            this.setStyle(ctx, options);
            
            ctx.beginPath();
            ctx.moveTo(startX, startY);
            ctx.bezierCurveTo(cp1X, cp1Y, cp2X, cp2Y, endX, endY);
            ctx.stroke();
            
            this.restoreState(ctx);
            return true;
        } catch (error) {
            console.error('XCanvas.drawBezierCurve - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Dessiner une courbe quadratique
     * @param {CanvasRenderingContext2D} ctx - Contexte canvas
     * @param {number} startX - Position X de départ
     * @param {number} startY - Position Y de départ
     * @param {number} cpX - Position X du point de contrôle
     * @param {number} cpY - Position Y du point de contrôle
     * @param {number} endX - Position X d'arrivée
     * @param {number} endY - Position Y d'arrivée
     * @param {object} options - Options de style
     * @returns {boolean} true si réussi, false sinon
     */
    drawQuadraticCurve(ctx, startX, startY, cpX, cpY, endX, endY, options = {}) {
        try {
            this.saveState(ctx);
            this.setStyle(ctx, options);
            
            ctx.beginPath();
            ctx.moveTo(startX, startY);
            ctx.quadraticCurveTo(cpX, cpY, endX, endY);
            ctx.stroke();
            
            this.restoreState(ctx);
            return true;
        } catch (error) {
            console.error('XCanvas.drawQuadraticCurve - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Dessiner une ellipse
     * @param {CanvasRenderingContext2D} ctx - Contexte canvas
     * @param {number} x - Position X du centre
     * @param {number} y - Position Y du centre
     * @param {number} radiusX - Rayon horizontal
     * @param {number} radiusY - Rayon vertical
     * @param {number} rotation - Rotation en radians
     * @param {number} startAngle - Angle de départ
     * @param {number} endAngle - Angle de fin
     * @param {boolean} anticlockwise - Sens anti-horaire
     * @param {object} options - Options de style
     * @returns {boolean} true si réussi, false sinon
     */
    drawEllipse(ctx, x, y, radiusX, radiusY, rotation = 0, startAngle = 0, endAngle = Math.PI * 2, anticlockwise = false, options = {}) {
        try {
            this.saveState(ctx);
            this.setStyle(ctx, options);
            
            ctx.beginPath();
            ctx.ellipse(x, y, radiusX, radiusY, rotation, startAngle, endAngle, anticlockwise);
            
            if (options.fill) {
                ctx.fill();
            }
            if (options.stroke) {
                ctx.stroke();
            }
            
            this.restoreState(ctx);
            return true;
        } catch (error) {
            console.error('XCanvas.drawEllipse - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Dessiner un dégradé linéaire
     * @param {CanvasRenderingContext2D} ctx - Contexte canvas
     * @param {number} x1 - Position X de départ
     * @param {number} y1 - Position Y de départ
     * @param {number} x2 - Position X d'arrivée
     * @param {number} y2 - Position Y d'arrivée
     * @param {Array} colorStops - Tableau de stops de couleur [{offset, color}, ...]
     * @param {object} shape - Forme à remplir (rectangle ou cercle)
     * @returns {boolean} true si réussi, false sinon
     */
    drawLinearGradient(ctx, x1, y1, x2, y2, colorStops, shape = null) {
        try {
            if (!ctx || !ctx.canvas) {
                throw new Error('Contexte canvas invalide');
            }

            const gradient = ctx.createLinearGradient(x1, y1, x2, y2);
            
            colorStops.forEach(stop => {
                gradient.addColorStop(stop.offset, stop.color);
            });
            
            if (shape) {
                this.saveState(ctx);
                ctx.fillStyle = gradient;
                
                if (shape.type === 'rect') {
                    ctx.fillRect(shape.x, shape.y, shape.width, shape.height);
                } else if (shape.type === 'circle') {
                    ctx.beginPath();
                    ctx.arc(shape.x, shape.y, shape.radius, 0, Math.PI * 2);
                    ctx.fill();
                }
                
                this.restoreState(ctx);
            }
            
            return gradient;
        } catch (error) {
            console.error('XCanvas.drawLinearGradient - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Dessiner un dégradé radial
     * @param {CanvasRenderingContext2D} ctx - Contexte canvas
     * @param {number} x1 - Position X du premier cercle
     * @param {number} y1 - Position Y du premier cercle
     * @param {number} r1 - Rayon du premier cercle
     * @param {number} x2 - Position X du deuxième cercle
     * @param {number} y2 - Position Y du deuxième cercle
     * @param {number} r2 - Rayon du deuxième cercle
     * @param {Array} colorStops - Tableau de stops de couleur [{offset, color}, ...]
     * @param {object} shape - Forme à remplir (rectangle ou cercle)
     * @returns {boolean} true si réussi, false sinon
     */
    drawRadialGradient(ctx, x1, y1, r1, x2, y2, r2, colorStops, shape = null) {
        try {
            if (!ctx || !ctx.canvas) {
                throw new Error('Contexte canvas invalide');
            }

            const gradient = ctx.createRadialGradient(x1, y1, r1, x2, y2, r2);
            
            colorStops.forEach(stop => {
                gradient.addColorStop(stop.offset, stop.color);
            });
            
            if (shape) {
                this.saveState(ctx);
                ctx.fillStyle = gradient;
                
                if (shape.type === 'rect') {
                    ctx.fillRect(shape.x, shape.y, shape.width, shape.height);
                } else if (shape.type === 'circle') {
                    ctx.beginPath();
                    ctx.arc(shape.x, shape.y, shape.radius, 0, Math.PI * 2);
                    ctx.fill();
                }
                
                this.restoreState(ctx);
            }
            
            return gradient;
        } catch (error) {
            console.error('XCanvas.drawRadialGradient - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Dessiner un motif répété
     * @param {CanvasRenderingContext2D} ctx - Contexte canvas
     * @param {string|HTMLImageElement} image - Image à répéter
     * @param {string} repetition - Type de répétition ('repeat', 'repeat-x', 'repeat-y', 'no-repeat')
     * @param {object} shape - Forme à remplir
     * @param {function} callback - Fonction de rappel
     * @returns {boolean} true si réussi, false sinon
     */
    drawPattern(ctx, image, repetition = 'repeat', shape = null, callback = null) {
        try {
            if (!ctx || !ctx.canvas) {
                throw new Error('Contexte canvas invalide');
            }

            if (typeof image === 'string') {
                const img = new Image();
                img.onload = () => {
                    const pattern = ctx.createPattern(img, repetition);
                    if (shape) {
                        this.saveState(ctx);
                        ctx.fillStyle = pattern;
                        
                        if (shape.type === 'rect') {
                            ctx.fillRect(shape.x, shape.y, shape.width, shape.height);
                        } else if (shape.type === 'circle') {
                            ctx.beginPath();
                            ctx.arc(shape.x, shape.y, shape.radius, 0, Math.PI * 2);
                            ctx.fill();
                        }
                        
                        this.restoreState(ctx);
                    }
                    callback?.(pattern);
                };
                img.onerror = () => {
                    callback?.(false);
                };
                img.src = image;
            } else if (image instanceof HTMLImageElement) {
                const pattern = ctx.createPattern(image, repetition);
                if (shape) {
                    this.saveState(ctx);
                    ctx.fillStyle = pattern;
                    
                    if (shape.type === 'rect') {
                        ctx.fillRect(shape.x, shape.y, shape.width, shape.height);
                    } else if (shape.type === 'circle') {
                        ctx.beginPath();
                        ctx.arc(shape.x, shape.y, shape.radius, 0, Math.PI * 2);
                        ctx.fill();
                    }
                    
                    this.restoreState(ctx);
                }
                callback?.(pattern);
            } else {
                throw new Error('Type d\'image non supporté');
            }
            
            return true;
        } catch (error) {
            console.error('XCanvas.drawPattern - Erreur:', error.message);
            callback?.(false);
            return false;
        }
    },

    /**
     * Appliquer une ombre portée
     * @param {CanvasRenderingContext2D} ctx - Contexte canvas
     * @param {object} options - Options d'ombre
     * @returns {boolean} true si réussi, false sinon
     */
    setShadow(ctx, options = {}) {
        try {
            if (!ctx || !ctx.canvas) {
                throw new Error('Contexte canvas invalide');
            }

            ctx.shadowColor = options.color || 'black';
            ctx.shadowBlur = options.blur || 0;
            ctx.shadowOffsetX = options.offsetX || 0;
            ctx.shadowOffsetY = options.offsetY || 0;
            return true;
        } catch (error) {
            console.error('XCanvas.setShadow - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Transformer le canvas (rotation, échelle, translation)
     * @param {CanvasRenderingContext2D} ctx - Contexte canvas
     * @param {object} options - Options de transformation
     * @returns {boolean} true si réussi, false sinon
     */
    transform(ctx, options = {}) {
        try {
            if (!ctx || !ctx.canvas) {
                throw new Error('Contexte canvas invalide');
            }

            this.saveState(ctx);
            
            if (options.translate) {
                ctx.translate(options.translate.x, options.translate.y);
            }
            
            if (options.rotate) {
                ctx.rotate(options.rotate);
            }
            
            if (options.scale) {
                ctx.scale(options.scale.x, options.scale.y);
            }
            
            return true;
        } catch (error) {
            console.error('XCanvas.transform - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Réinitialiser toutes les transformations
     * @param {CanvasRenderingContext2D} ctx - Contexte canvas
     * @returns {boolean} true si réussi, false sinon
     */
    resetTransform(ctx) {
        try {
            if (!ctx || !ctx.canvas) {
                throw new Error('Contexte canvas invalide');
            }

            ctx.setTransform(1, 0, 0, 1, 0, 0);
            return true;
        } catch (error) {
            console.error('XCanvas.resetTransform - Erreur:', error.message);
            return false;
        }
    },

    // ======================== UTILITAIRES ========================
    /**
     * Obtenir la position de la souris relative au canvas
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {MouseEvent|TouchEvent} event - Événement de souris/touch
     * @returns {object} { x, y }
     */
    getMousePosition(canvas, event) {
        try {
            if (!canvas || !event) {
                throw new Error('Canvas ou événement invalide');
            }

            const rect = canvas.getBoundingClientRect();
            let clientX, clientY;
            
            if (event.touches) {
                clientX = event.touches[0].clientX;
                clientY = event.touches[0].clientY;
            } else {
                clientX = event.clientX;
                clientY = event.clientY;
            }
            
            return {
                x: clientX - rect.left,
                y: clientY - rect.top
            };
        } catch (error) {
            console.error('XCanvas.getMousePosition - Erreur:', error.message);
            return { x: 0, y: 0 };
        }
    },

    /**
     * Effacer tout le canvas
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @returns {boolean} true si réussi, false sinon
     */
    clearCanvas(canvas) {
        try {
            if (!canvas || !canvas.getContext) {
                throw new Error('Canvas invalide');
            }

            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            this.saveToHistory(canvas);
            return true;
        } catch (error) {
            console.error('XCanvas.clearCanvas - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Changer la taille du canvas
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {number} width - Nouvelle largeur
     * @param {number} height - Nouvelle hauteur
     * @param {boolean} preserveContent - Conserver le contenu existant
     * @returns {boolean} true si réussi, false sinon
     */
    resizeCanvas(canvas, width, height, preserveContent = true) {
        try {
            if (!canvas || !canvas.getContext) {
                throw new Error('Canvas invalide');
            }

            if (typeof width !== 'number' || typeof height !== 'number' || width <= 0 || height <= 0) {
                throw new Error('Les dimensions width et height doivent être des nombres positifs');
            }

            if (preserveContent) {
                const tempCanvas = document.createElement('canvas');
                const tempCtx = tempCanvas.getContext('2d');
                tempCanvas.width = canvas.width;
                tempCanvas.height = canvas.height;
                tempCtx.drawImage(canvas, 0, 0);
                
                canvas.width = width;
                canvas.height = height;
                canvas.getContext('2d').drawImage(tempCanvas, 0, 0, width, height);
            } else {
                canvas.width = width;
                canvas.height = height;
            }
            
            this.saveToHistory(canvas);
            return true;
        } catch (error) {
            console.error('XCanvas.resizeCanvas - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Exporter le canvas en image
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {string} format - Format de l'image (png, jpeg, webp)
     * @param {number} quality - Qualité (0-1 pour jpeg/webp)
     * @returns {string|null} URL de l'image ou null en cas d'erreur
     */
    exportImage(canvas, format = 'png', quality = 1) {
        try {
            if (!canvas || !canvas.toDataURL) {
                throw new Error('Canvas invalide');
            }

            const mimeType = `image/${format}`;
            return canvas.toDataURL(mimeType, quality);
        } catch (error) {
            console.error('XCanvas.exportImage - Erreur:', error.message);
            return null;
        }
    },

    /**
     * Télécharger le canvas en image
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {string} filename - Nom du fichier
     * @param {string} format - Format de l'image (png, jpeg, webp)
     * @param {number} quality - Qualité (0-1 pour jpeg/webp)
     * @returns {boolean} true si réussi, false sinon
     */
    downloadImage(canvas, filename = 'canvas', format = 'png', quality = 1) {
        try {
            if (!canvas || !canvas.toDataURL) {
                throw new Error('Canvas invalide');
            }

            const link = document.createElement('a');
            link.download = `${filename}.${format}`;
            link.href = this.exportImage(canvas, format, quality);
            link.click();
            return true;
        } catch (error) {
            console.error('XCanvas.downloadImage - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Charger une image dans le canvas
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {string|File} source - URL de l'image ou fichier
     * @param {function} callback - Fonction de rappel
     * @returns {boolean} true si réussi, false sinon
     */
    loadImage(canvas, source, callback = null) {
        try {
            if (!canvas || !canvas.getContext) {
                throw new Error('Canvas invalide');
            }

            const ctx = canvas.getContext('2d');
            const img = new Image();
            
            img.onload = () => {
                try {
                    // Redimensionner le canvas si nécessaire
                    if (canvas.width !== img.width || canvas.height !== img.height) {
                        canvas.width = img.width;
                        canvas.height = img.height;
                    }
                    
                    ctx.drawImage(img, 0, 0);
                    this.saveToHistory(canvas);
                    callback?.(img);
                } catch (error) {
                    console.error('XCanvas.loadImage - Erreur dans onload:', error.message);
                    callback?.(null, error);
                }
            };
            
            img.onerror = (err) => {
                console.error('Erreur de chargement de l\'image:', err);
                callback?.(null, err);
            };
            
            if (typeof source === 'string') {
                img.src = source;
            } else if (source instanceof File) {
                const reader = new FileReader();
                reader.onload = (e) => img.src = e.target.result;
                reader.readAsDataURL(source);
            } else {
                throw new Error('Source d\'image non supportée');
            }
            
            return true;
        } catch (error) {
            console.error('XCanvas.loadImage - Erreur:', error.message);
            callback?.(null, error);
            return false;
        }
    },

    // ======================== FILTRES ET EFFETS ========================
    /**
     * Appliquer un filtre de luminosité/contraste
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {number} brightness - Luminosité (-100 à 100)
     * @param {number} contrast - Contraste (-100 à 100)
     * @returns {boolean} true si réussi, false sinon
     */
    applyBrightnessContrast(canvas, brightness = 0, contrast = 0) {
        try {
            if (!canvas || !canvas.getContext) {
                throw new Error('Canvas invalide');
            }

            this.saveToHistory(canvas);
            const ctx = canvas.getContext('2d');
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const data = imageData.data;
            
            // Convertir les valeurs en facteurs
            const brightnessFactor = brightness / 100;
            const contrastFactor = (contrast + 100) / 100;
            
            for (let i = 0; i < data.length; i += 4) {
                // Luminosité
                data[i] = data[i] + (brightnessFactor * 255);
                data[i + 1] = data[i + 1] + (brightnessFactor * 255);
                data[i + 2] = data[i + 2] + (brightnessFactor * 255);
                
                // Contraste
                data[i] = ((data[i] / 255 - 0.5) * contrastFactor + 0.5) * 255;
                data[i + 1] = ((data[i + 1] / 255 - 0.5) * contrastFactor + 0.5) * 255;
                data[i + 2] = ((data[i + 2] / 255 - 0.5) * contrastFactor + 0.5) * 255;
                
                // Limiter les valeurs entre 0 et 255
                data[i] = Math.max(0, Math.min(255, data[i]));
                data[i + 1] = Math.max(0, Math.min(255, data[i + 1]));
                data[i + 2] = Math.max(0, Math.min(255, data[i + 2]));
            }
            
            ctx.putImageData(imageData, 0, 0);
            return true;
        } catch (error) {
            console.error('XCanvas.applyBrightnessContrast - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Appliquer un filtre noir et blanc
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @returns {boolean} true si réussi, false sinon
     */
    applyBlackAndWhite(canvas) {
        try {
            if (!canvas || !canvas.getContext) {
                throw new Error('Canvas invalide');
            }

            this.saveToHistory(canvas);
            const ctx = canvas.getContext('2d');
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const data = imageData.data;
            
            for (let i = 0; i < data.length; i += 4) {
                const avg = (data[i] + data[i + 1] + data[i + 2]) / 3;
                data[i] = avg;
                data[i + 1] = avg;
                data[i + 2] = avg;
            }
            
            ctx.putImageData(imageData, 0, 0);
            return true;
        } catch (error) {
            console.error('XCanvas.applyBlackAndWhite - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Appliquer un filtre de flou
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {number} radius - Rayon du flou
     * @returns {boolean} true si réussi, false sinon
     */
    applyBlur(canvas, radius = 1) {
        try {
            if (!canvas || !canvas.getContext) {
                throw new Error('Canvas invalide');
            }

            this.saveToHistory(canvas);
            const ctx = canvas.getContext('2d');
            
            // Plusieurs passes pour un meilleur flou
            for (let i = 0; i < radius; i++) {
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const tempData = new Uint8ClampedArray(imageData.data);
                
                for (let y = 1; y < canvas.height - 1; y++) {
                    for (let x = 1; x < canvas.width - 1; x++) {
                        const idx = (y * canvas.width + x) * 4;
                        
                        // Moyenne des pixels voisins
                        let r = 0, g = 0, b = 0, a = 0;
                        for (let dy = -1; dy <= 1; dy++) {
                            for (let dx = -1; dx <= 1; dx++) {
                                const nIdx = ((y + dy) * canvas.width + (x + dx)) * 4;
                                r += tempData[nIdx];
                                g += tempData[nIdx + 1];
                                b += tempData[nIdx + 2];
                                a += tempData[nIdx + 3];
                            }
                        }
                        
                        imageData.data[idx] = r / 9;
                        imageData.data[idx + 1] = g / 9;
                        imageData.data[idx + 2] = b / 9;
                        imageData.data[idx + 3] = a / 9;
                    }
                }
                
                ctx.putImageData(imageData, 0, 0);
            }
            
            return true;
        } catch (error) {
            console.error('XCanvas.applyBlur - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Appliquer un filtre de seuil (threshold)
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {number} threshold - Seuil (0-255)
     * @returns {boolean} true si réussi, false sinon
     */
    applyThreshold(canvas, threshold = 128) {
        try {
            if (!canvas || !canvas.getContext) {
                throw new Error('Canvas invalide');
            }

            this.saveToHistory(canvas);
            const ctx = canvas.getContext('2d');
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const data = imageData.data;
            
            for (let i = 0; i < data.length; i += 4) {
                const avg = (data[i] + data[i + 1] + data[i + 2]) / 3;
                const value = avg >= threshold ? 255 : 0;
                data[i] = value;
                data[i + 1] = value;
                data[i + 2] = value;
            }
            
            ctx.putImageData(imageData, 0, 0);
            return true;
        } catch (error) {
            console.error('XCanvas.applyThreshold - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Appliquer un filtre de sépia
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @returns {boolean} true si réussi, false sinon
     */
    applySepia(canvas) {
        try {
            if (!canvas || !canvas.getContext) {
                throw new Error('Canvas invalide');
            }

            this.saveToHistory(canvas);
            const ctx = canvas.getContext('2d');
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const data = imageData.data;
            
            for (let i = 0; i < data.length; i += 4) {
                const r = data[i];
                const g = data[i + 1];
                const b = data[i + 2];
                
                data[i] = Math.min(255, (r * 0.393) + (g * 0.769) + (b * 0.189));
                data[i + 1] = Math.min(255, (r * 0.349) + (g * 0.686) + (b * 0.168));
                data[i + 2] = Math.min(255, (r * 0.272) + (g * 0.534) + (b * 0.131));
            }
            
            ctx.putImageData(imageData, 0, 0);
            return true;
        } catch (error) {
            console.error('XCanvas.applySepia - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Appliquer un filtre d'inversion de couleur
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @returns {boolean} true si réussi, false sinon
     */
    applyInvert(canvas) {
        try {
            if (!canvas || !canvas.getContext) {
                throw new Error('Canvas invalide');
            }

            this.saveToHistory(canvas);
            const ctx = canvas.getContext('2d');
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const data = imageData.data;
            
            for (let i = 0; i < data.length; i += 4) {
                data[i] = 255 - data[i];
                data[i + 1] = 255 - data[i + 1];
                data[i + 2] = 255 - data[i + 2];
            }
            
            ctx.putImageData(imageData, 0, 0);
            return true;
        } catch (error) {
            console.error('XCanvas.applyInvert - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Appliquer un filtre de saturation
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {number} amount - Niveau de saturation (0-200)
     * @returns {boolean} true si réussi, false sinon
     */
    applySaturation(canvas, amount = 100) {
        try {
            if (!canvas || !canvas.getContext) {
                throw new Error('Canvas invalide');
            }

            this.saveToHistory(canvas);
            const ctx = canvas.getContext('2d');
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const data = imageData.data;
            
            const factor = amount / 100;
            
            for (let i = 0; i < data.length; i += 4) {
                const r = data[i];
                const g = data[i + 1];
                const b = data[i + 2];
                
                const gray = 0.2989 * r + 0.5870 * g + 0.1140 * b;
                
                data[i] = Math.min(255, gray + (r - gray) * factor);
                data[i + 1] = Math.min(255, gray + (g - gray) * factor);
                data[i + 2] = Math.min(255, gray + (b - gray) * factor);
            }
            
            ctx.putImageData(imageData, 0, 0);
            return true;
        } catch (error) {
            console.error('XCanvas.applySaturation - Erreur:', error.message);
            return false;
        }
    },

    // ======================== GESTION DES ÉVÉNEMENTS ========================
    /**
     * Ajouter un écouteur d'événement au canvas
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {string} event - Type d'événement
     * @param {function} handler - Fonction de gestion
     * @returns {boolean} true si réussi, false sinon
     */
    addEventListener(canvas, event, handler) {
        try {
            if (!canvas || !canvas._xcanvas) {
                throw new Error('Canvas invalide ou non initialisé avec XCanvas');
            }

            canvas.addEventListener(event, handler);
            
            // Stocker la référence pour pouvoir le supprimer plus tard
            if (!canvas._xcanvas.events[event]) {
                canvas._xcanvas.events[event] = [];
            }
            canvas._xcanvas.events[event].push(handler);
            
            return true;
        } catch (error) {
            console.error('XCanvas.addEventListener - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Supprimer un écouteur d'événement du canvas
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {string} event - Type d'événement
     * @param {function} handler - Fonction de gestion
     * @returns {boolean} true si réussi, false sinon
     */
    removeEventListener(canvas, event, handler) {
        try {
            if (!canvas || !canvas._xcanvas) {
                throw new Error('Canvas invalide ou non initialisé avec XCanvas');
            }

            canvas.removeEventListener(event, handler);
            
            // Supprimer de la liste des événements
            if (canvas._xcanvas.events[event]) {
                const index = canvas._xcanvas.events[event].indexOf(handler);
                if (index !== -1) {
                    canvas._xcanvas.events[event].splice(index, 1);
                }
            }
            
            return true;
        } catch (error) {
            console.error('XCanvas.removeEventListener - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Supprimer tous les écouteurs d'événements du canvas
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @returns {boolean} true si réussi, false sinon
     */
    removeAllEventListeners(canvas) {
        try {
            if (!canvas || !canvas._xcanvas) {
                throw new Error('Canvas invalide ou non initialisé avec XCanvas');
            }

            for (const event in canvas._xcanvas.events) {
                for (const handler of canvas._xcanvas.events[event]) {
                    canvas.removeEventListener(event, handler);
                }
                canvas._xcanvas.events[event] = [];
            }
            
            return true;
        } catch (error) {
            console.error('XCanvas.removeAllEventListeners - Erreur:', error.message);
            return false;
        }
    },

    // ======================== DÉTECTION DE COLLISION ========================
    /**
     * Vérifier si un point est dans un rectangle
     * @param {number} x - Position X du point
     * @param {number} y - Position Y du point
     * @param {number} rectX - Position X du rectangle
     * @param {number} rectY - Position Y du rectangle
     * @param {number} rectWidth - Largeur du rectangle
     * @param {number} rectHeight - Hauteur du rectangle
     * @returns {boolean} true si collision, false sinon
     */
    isPointInRect(x, y, rectX, rectY, rectWidth, rectHeight) {
        return x >= rectX && x <= rectX + rectWidth &&
               y >= rectY && y <= rectY + rectHeight;
    },

    /**
     * Vérifier si un point est dans un cercle
     * @param {number} x - Position X du point
     * @param {number} y - Position Y du point
     * @param {number} circleX - Position X du cercle
     * @param {number} circleY - Position Y du cercle
     * @param {number} radius - Rayon du cercle
     * @returns {boolean} true si collision, false sinon
     */
    isPointInCircle(x, y, circleX, circleY, radius) {
        const dx = x - circleX;
        const dy = y - circleY;
        return dx * dx + dy * dy <= radius * radius;
    },

    /**
     * Vérifier si deux rectangles se chevauchent
     * @param {number} x1 - Position X du premier rectangle
     * @param {number} y1 - Position Y du premier rectangle
     * @param {number} width1 - Largeur du premier rectangle
     * @param {number} height1 - Hauteur du premier rectangle
     * @param {number} x2 - Position X du deuxième rectangle
     * @param {number} y2 - Position Y du deuxième rectangle
     * @param {number} width2 - Largeur du deuxième rectangle
     * @param {number} height2 - Hauteur du deuxième rectangle
     * @returns {boolean} true si collision, false sinon
     */
    isRectColliding(x1, y1, width1, height1, x2, y2, width2, height2) {
        return x1 < x2 + width2 &&
               x1 + width1 > x2 &&
               y1 < y2 + height2 &&
               y1 + height1 > y2;
    },

    // ======================== ANIMATION ========================
    /**
     * Démarrer une animation
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {function} callback - Fonction d'animation
     * @returns {number} ID de l'animation (pour l'arrêter)
     */
    startAnimation(canvas, callback) {
        try {
            if (!canvas || !canvas._xcanvas) {
                throw new Error('Canvas invalide ou non initialisé avec XCanvas');
            }

            let lastTime = 0;
            const animate = (time) => {
                const deltaTime = time - lastTime;
                lastTime = time;
                
                callback(deltaTime);
                
                canvas._xcanvas.animationId = requestAnimationFrame(animate);
            };
            
            canvas._xcanvas.animationId = requestAnimationFrame(animate);
            return canvas._xcanvas.animationId;
        } catch (error) {
            console.error('XCanvas.startAnimation - Erreur:', error.message);
            return -1;
        }
    },

    /**
     * Arrêter une animation
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @returns {boolean} true si réussi, false sinon
     */
    stopAnimation(canvas) {
        try {
            if (!canvas || !canvas._xcanvas || !canvas._xcanvas.animationId) {
                return false;
            }

            cancelAnimationFrame(canvas._xcanvas.animationId);
            delete canvas._xcanvas.animationId;
            return true;
        } catch (error) {
            console.error('XCanvas.stopAnimation - Erreur:', error.message);
            return false;
        }
    },








    // ======================== GESTION DES CALQUES ========================
    /**
     * Créer un nouveau calque
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {object} options - Options du calque
     * @returns {object} Calque créé
     */
    createLayer(canvas, options = {}) {
        try {
            if (!canvas || !canvas._xcanvas) throw new Error('Canvas non initialisé');
            
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = canvas.width;
            tempCanvas.height = canvas.height;
            const tempCtx = tempCanvas.getContext('2d');
            
            const layer = {
                canvas: tempCanvas,
                ctx: tempCtx,
                name: options.name || `Calque ${canvas._xcanvas.layers.length + 1}`,
                visible: options.visible !== false,
                opacity: options.opacity || 1,
                compositeOperation: options.compositeOperation || 'source-over'
            };
            
            canvas._xcanvas.layers.push(layer);
            canvas._xcanvas.activeLayer = layer;
            
            this.renderLayers(canvas);
            return layer;
        } catch (error) {
            console.error('XCanvas.createLayer - Erreur:', error.message);
            return null;
        }
    },

    /**
     * Rendre tous les calques visibles
     * @param {HTMLCanvasElement} canvas - Élément canvas
     */
    renderLayers(canvas) {
        try {
            if (!canvas || !canvas._xcanvas) throw new Error('Canvas non initialisé');
            
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            canvas._xcanvas.layers.forEach(layer => {
                if (layer.visible) {
                    ctx.globalAlpha = layer.opacity;
                    ctx.globalCompositeOperation = layer.compositeOperation;
                    ctx.drawImage(layer.canvas, 0, 0);
                }
            });
            
            ctx.globalAlpha = 1;
            ctx.globalCompositeOperation = 'source-over';
        } catch (error) {
            console.error('XCanvas.renderLayers - Erreur:', error.message);
        }
    },

    /**
     * Fusionner les calques
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {Array} layerIndexes - Index des calques à fusionner
     */
    mergeLayers(canvas, layerIndexes) {
        try {
            if (!canvas || !canvas._xcanvas) throw new Error('Canvas non initialisé');
            
            const layers = canvas._xcanvas.layers;
            if (layerIndexes.some(idx => idx < 0 || idx >= layers.length)) {
                throw new Error('Index de calque invalide');
            }
            
            const mergedCanvas = document.createElement('canvas');
            mergedCanvas.width = canvas.width;
            mergedCanvas.height = canvas.height;
            const mergedCtx = mergedCanvas.getContext('2d');
            
            layerIndexes.sort().forEach(idx => {
                const layer = layers[idx];
                mergedCtx.globalAlpha = layer.opacity;
                mergedCtx.globalCompositeOperation = layer.compositeOperation;
                mergedCtx.drawImage(layer.canvas, 0, 0);
            });
            
            // Supprimer les anciens calques et ajouter le fusionné
            const newLayers = layers.filter((_, idx) => !layerIndexes.includes(idx));
            newLayers.push({
                canvas: mergedCanvas,
                ctx: mergedCtx,
                name: 'Fusionné',
                visible: true,
                opacity: 1,
                compositeOperation: 'source-over'
            });
            
            canvas._xcanvas.layers = newLayers;
            canvas._xcanvas.activeLayer = newLayers[newLayers.length - 1];
            this.renderLayers(canvas);
            
            return true;
        } catch (error) {
            console.error('XCanvas.mergeLayers - Erreur:', error.message);
            return false;
        }
    },

    // ======================== TOOLBAR ========================
    /**
     * Ajouter un outil à la toolbar
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {string} name - Nom de l'outil
     * @param {string} icon - Icône (texte ou HTML)
     * @param {function} action - Fonction à exécuter
     * @param {object} options - Options supplémentaires
     */
    addTool(canvas, name, icon, action, options = {}) {
        try {
            if (!canvas || !canvas._xcanvas) throw new Error('Canvas non initialisé');
            
            const button = document.createElement('button');
            button.innerHTML = icon;
            button.title = name;
            button.style.padding = '8px';
            button.style.border = '1px solid #ccc';
            button.style.borderRadius = '4px';
            button.style.cursor = 'pointer';
            button.style.backgroundColor = options.bgColor || '#fff';
            
            if (options.toggle) {
                button.addEventListener('click', () => {
                    if (canvas._xcanvas.currentTool === name) {
                        canvas._xcanvas.currentTool = null;
                        button.style.backgroundColor = options.bgColor || '#fff';
                    } else {
                        canvas._xcanvas.currentTool = name;
                        // Désactiver les autres boutons toggle
                        Array.from(canvas._xcanvas.toolbar.querySelectorAll('button')).forEach(btn => {
                            if (btn !== button) btn.style.backgroundColor = options.bgColor || '#fff';
                        });
                        button.style.backgroundColor = options.activeColor || '#ddd';
                    }
                    action(canvas);
                });
            } else {
                button.addEventListener('click', () => action(canvas));
            }
            
            canvas._xcanvas.toolbar.appendChild(button);
            return button;
        } catch (error) {
            console.error('XCanvas.addTool - Erreur:', error.message);
            return null;
        }
    },

    /**
     * Ajouter un sélecteur de couleur à la toolbar
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {string} type - 'fill' ou 'stroke'
     * @param {string} defaultColor - Couleur par défaut
     */
    addColorPicker(canvas, type, defaultColor = '#000000') {
        try {
            if (!canvas || !canvas._xcanvas) throw new Error('Canvas non initialisé');
            
            const input = document.createElement('input');
            input.type = 'color';
            input.value = defaultColor;
            input.title = type === 'fill' ? 'Couleur de remplissage' : 'Couleur de contour';
            input.style.width = '30px';
            input.style.height = '30px';
            input.style.padding = '0';
            input.style.border = '1px solid #ccc';
            
            input.addEventListener('input', (e) => {
                if (type === 'fill') {
                    canvas._xcanvas.fillColor = e.target.value;
                } else {
                    canvas._xcanvas.strokeColor = e.target.value;
                }
            });
            
            // Stocker la couleur initiale
            if (type === 'fill') {
                canvas._xcanvas.fillColor = defaultColor;
            } else {
                canvas._xcanvas.strokeColor = defaultColor;
            }
            
            canvas._xcanvas.toolbar.appendChild(input);
            return input;
        } catch (error) {
            console.error('XCanvas.addColorPicker - Erreur:', error.message);
            return null;
        }
    },

    /**
     * Ajouter un sélecteur d'épaisseur à la toolbar
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {number} defaultValue - Valeur par défaut
     */
    addWidthSelector(canvas, defaultValue = 2) {
        try {
            if (!canvas || !canvas._xcanvas) throw new Error('Canvas non initialisé');
            
            const input = document.createElement('input');
            input.type = 'range';
            input.min = '1';
            input.max = '50';
            input.value = defaultValue;
            input.title = 'Épaisseur du trait';
            input.style.width = '100px';
            
            input.addEventListener('input', (e) => {
                canvas._xcanvas.lineWidth = e.target.value;
            });
            
            // Afficher la valeur
            const label = document.createElement('span');
            label.textContent = defaultValue;
            label.style.marginLeft = '5px';
            input.addEventListener('input', (e) => {
                label.textContent = e.target.value;
            });
            
            const container = document.createElement('div');
            container.style.display = 'flex';
            container.style.alignItems = 'center';
            container.appendChild(input);
            container.appendChild(label);
            
            // Stocker la valeur initiale
            canvas._xcanvas.lineWidth = defaultValue;
            
            canvas._xcanvas.toolbar.appendChild(container);
            return container;
        } catch (error) {
            console.error('XCanvas.addWidthSelector - Erreur:', error.message);
            return null;
        }
    },

    // ======================== OUTILS DE DESSIN ========================
    /**
     * Activer l'outil de sélection
     * @param {HTMLCanvasElement} canvas - Élément canvas
     */
    enableSelectionTool(canvas) {
        try {
            if (!canvas || !canvas._xcanvas) throw new Error('Canvas non initialisé');
            
            let isSelecting = false;
            let startX, startY;
            let selectionRect = null;
            
            const startSelection = (e) => {
                const pos = this.getMousePosition(canvas, e);
                startX = pos.x;
                startY = pos.y;
                isSelecting = true;
                
                // Créer un rectangle de sélection
                selectionRect = {
                    x: startX,
                    y: startY,
                    width: 0,
                    height: 0
                };
            };
            
            const updateSelection = (e) => {
                if (!isSelecting) return;
                
                const pos = this.getMousePosition(canvas, e);
                selectionRect.width = pos.x - startX;
                selectionRect.height = pos.y - startY;
                
                // Redessiner les calques + rectangle de sélection
                this.renderLayers(canvas);
                
                const ctx = canvas.getContext('2d');
                ctx.strokeStyle = '#0095ff';
                ctx.lineWidth = 1;
                ctx.setLineDash([5, 5]);
                ctx.strokeRect(
                    selectionRect.x,
                    selectionRect.y,
                    selectionRect.width,
                    selectionRect.height
                );
                ctx.setLineDash([]);
            };
            
            const endSelection = (e) => {
                if (!isSelecting) return;
                isSelecting = false;
                
                const pos = this.getMousePosition(canvas, e);
                selectionRect.width = pos.x - startX;
                selectionRect.height = pos.y - startY;
                
                // Ne garder que si la sélection est assez grande
                if (Math.abs(selectionRect.width) > 5 && Math.abs(selectionRect.height) > 5) {
                    canvas._xcanvas.selection = selectionRect;
                    
                    // Dessiner la sélection en pointillés
                    this.renderLayers(canvas);
                    const ctx = canvas.getContext('2d');
                    ctx.strokeStyle = '#0095ff';
                    ctx.lineWidth = 1;
                    ctx.setLineDash([5, 5]);
                    ctx.strokeRect(
                        selectionRect.x,
                        selectionRect.y,
                        selectionRect.width,
                        selectionRect.height
                    );
                    ctx.setLineDash([]);
                } else {
                    canvas._xcanvas.selection = null;
                    this.renderLayers(canvas);
                }
            };
            
            // Supprimer les anciens écouteurs
            this.disableSelectionTool(canvas);
            
            // Ajouter les nouveaux écouteurs
            canvas.addEventListener('mousedown', startSelection);
            canvas.addEventListener('mousemove', updateSelection);
            canvas.addEventListener('mouseup', endSelection);
            
            // Sauvegarder les gestionnaires
            canvas._xcanvas.tools.selection = {
                startSelection,
                updateSelection,
                endSelection
            };
            
            return true;
        } catch (error) {
            console.error('XCanvas.enableSelectionTool - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Désactiver l'outil de sélection
     * @param {HTMLCanvasElement} canvas - Élément canvas
     */
    disableSelectionTool(canvas) {
        try {
            if (!canvas || !canvas._xcanvas || !canvas._xcanvas.tools.selection) {
                return false;
            }
            
            const { startSelection, updateSelection, endSelection } = canvas._xcanvas.tools.selection;
            
            canvas.removeEventListener('mousedown', startSelection);
            canvas.removeEventListener('mousemove', updateSelection);
            canvas.removeEventListener('mouseup', endSelection);
            
            delete canvas._xcanvas.tools.selection;
            return true;
        } catch (error) {
            console.error('XCanvas.disableSelectionTool - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Activer l'outil de déplacement
     * @param {HTMLCanvasElement} canvas - Élément canvas
     */
    enableMoveTool(canvas) {
        try {
            if (!canvas || !canvas._xcanvas) throw new Error('Canvas non initialisé');
            
            let isMoving = false;
            let startX, startY;
            let moved = false;
            
            const startMove = (e) => {
                if (!canvas._xcanvas.selection) return;
                
                const pos = this.getMousePosition(canvas, e);
                startX = pos.x;
                startY = pos.y;
                isMoving = true;
                moved = false;
            };
            
            const move = (e) => {
                if (!isMoving || !canvas._xcanvas.selection) return;
                
                const pos = this.getMousePosition(canvas, e);
                const dx = pos.x - startX;
                const dy = pos.y - startY;
                
                // Déplacer la sélection
                canvas._xcanvas.selection.x += dx;
                canvas._xcanvas.selection.y += dy;
                
                startX = pos.x;
                startY = pos.y;
                moved = true;
                
                // Redessiner avec la nouvelle position
                this.renderLayers(canvas);
                const ctx = canvas.getContext('2d');
                ctx.strokeStyle = '#0095ff';
                ctx.lineWidth = 1;
                ctx.setLineDash([5, 5]);
                ctx.strokeRect(
                    canvas._xcanvas.selection.x,
                    canvas._xcanvas.selection.y,
                    canvas._xcanvas.selection.width,
                    canvas._xcanvas.selection.height
                );
                ctx.setLineDash([]);
            };
            
            const endMove = () => {
                if (!isMoving) return;
                isMoving = false;
                
                if (moved) {
                    // Appliquer le déplacement au calque actif
                    const { x, y, width, height } = canvas._xcanvas.selection;
                    const layer = canvas._xcanvas.activeLayer;
                    
                    // Copier la zone sélectionnée
                    const imageData = layer.ctx.getImageData(x, y, width, height);
                    
                    // Effacer la zone originale
                    layer.ctx.clearRect(x, y, width, height);
                    
                    // Redessiner à la nouvelle position
                    layer.ctx.putImageData(imageData, x, y);
                    
                    // Mettre à jour la sélection
                    canvas._xcanvas.selection = { x, y, width, height };
                    
                    this.renderLayers(canvas);
                }
            };
            
            // Supprimer les anciens écouteurs
            this.disableMoveTool(canvas);
            
            // Ajouter les nouveaux écouteurs
            canvas.addEventListener('mousedown', startMove);
            canvas.addEventListener('mousemove', move);
            canvas.addEventListener('mouseup', endMove);
            canvas.addEventListener('mouseout', endMove);
            
            // Sauvegarder les gestionnaires
            canvas._xcanvas.tools.move = {
                startMove,
                move,
                endMove
            };
            
            return true;
        } catch (error) {
            console.error('XCanvas.enableMoveTool - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Désactiver l'outil de déplacement
     * @param {HTMLCanvasElement} canvas - Élément canvas
     */
    disableMoveTool(canvas) {
        try {
            if (!canvas || !canvas._xcanvas || !canvas._xcanvas.tools.move) {
                return false;
            }
            
            const { startMove, move, endMove } = canvas._xcanvas.tools.move;
            
            canvas.removeEventListener('mousedown', startMove);
            canvas.removeEventListener('mousemove', move);
            canvas.removeEventListener('mouseup', endMove);
            canvas.removeEventListener('mouseout', endMove);
            
            delete canvas._xcanvas.tools.move;
            return true;
        } catch (error) {
            console.error('XCanvas.disableMoveTool - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Activer l'outil de recadrage
     * @param {HTMLCanvasElement} canvas - Élément canvas
     */
    enableCropTool(canvas) {
        try {
            if (!canvas || !canvas._xcanvas) throw new Error('Canvas non initialisé');
            
            let isCropping = false;
            let startX, startY;
            let cropRect = null;
            
            const startCrop = (e) => {
                const pos = this.getMousePosition(canvas, e);
                startX = pos.x;
                startY = pos.y;
                isCropping = true;
                
                cropRect = {
                    x: startX,
                    y: startY,
                    width: 0,
                    height: 0
                };
            };
            
            const updateCrop = (e) => {
                if (!isCropping) return;
                
                const pos = this.getMousePosition(canvas, e);
                cropRect.width = pos.x - startX;
                cropRect.height = pos.y - startY;
                
                // Redessiner les calques + rectangle de recadrage
                this.renderLayers(canvas);
                
                const ctx = canvas.getContext('2d');
                ctx.strokeStyle = '#ff0000';
                ctx.lineWidth = 1;
                ctx.setLineDash([5, 5]);
                ctx.strokeRect(
                    cropRect.x,
                    cropRect.y,
                    cropRect.width,
                    cropRect.height
                );
                ctx.setLineDash([]);
            };
            
            const endCrop = (e) => {
                if (!isCropping) return;
                isCropping = false;
                
                const pos = this.getMousePosition(canvas, e);
                cropRect.width = pos.x - startX;
                cropRect.height = pos.y - startY;
                
                // Appliquer le recadrage si la zone est valide
                if (Math.abs(cropRect.width) > 5 && Math.abs(cropRect.height) > 5) {
                    this.applyCrop(canvas, cropRect);
                }
                
                cropRect = null;
                this.renderLayers(canvas);
            };
            
            // Supprimer les anciens écouteurs
            this.disableCropTool(canvas);
            
            // Ajouter les nouveaux écouteurs
            canvas.addEventListener('mousedown', startCrop);
            canvas.addEventListener('mousemove', updateCrop);
            canvas.addEventListener('mouseup', endCrop);
            
            // Sauvegarder les gestionnaires
            canvas._xcanvas.tools.crop = {
                startCrop,
                updateCrop,
                endCrop
            };
            
            return true;
        } catch (error) {
            console.error('XCanvas.enableCropTool - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Appliquer le recadrage
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {object} cropRect - Rectangle de recadrage
     */
    applyCrop(canvas, cropRect) {
        try {
            if (!canvas || !canvas._xcanvas) throw new Error('Canvas non initialisé');
            
            // Normaliser le rectangle (peut être dessiné dans n'importe quelle direction)
            const x = Math.min(cropRect.x, cropRect.x + cropRect.width);
            const y = Math.min(cropRect.y, cropRect.y + cropRect.height);
            const width = Math.abs(cropRect.width);
            const height = Math.abs(cropRect.height);
            
            // Redimensionner le canvas
            canvas.width = width;
            canvas.height = height;
            
            // Redimensionner tous les calques et recadrer leur contenu
            canvas._xcanvas.layers.forEach(layer => {
                const tempCanvas = document.createElement('canvas');
                tempCanvas.width = width;
                tempCanvas.height = height;
                const tempCtx = tempCanvas.getContext('2d');
                
                // Copier la partie recadrée
                tempCtx.drawImage(
                    layer.canvas,
                    x, y, width, height,  // Source
                    0, 0, width, height  // Destination
                );
                
                // Remplacer le canvas du calque
                layer.canvas = tempCanvas;
                layer.ctx = tempCtx;
            });
            
            this.renderLayers(canvas);
            return true;
        } catch (error) {
            console.error('XCanvas.applyCrop - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Désactiver l'outil de recadrage
     * @param {HTMLCanvasElement} canvas - Élément canvas
     */
    disableCropTool(canvas) {
        try {
            if (!canvas || !canvas._xcanvas || !canvas._xcanvas.tools.crop) {
                return false;
            }
            
            const { startCrop, updateCrop, endCrop } = canvas._xcanvas.tools.crop;
            
            canvas.removeEventListener('mousedown', startCrop);
            canvas.removeEventListener('mousemove', updateCrop);
            canvas.removeEventListener('mouseup', endCrop);
            
            delete canvas._xcanvas.tools.crop;
            return true;
        } catch (error) {
            console.error('XCanvas.disableCropTool - Erreur:', error.message);
            return false;
        }
    },

    // ======================== OUTILS DE TEXTE ========================
    /**
     * Activer l'outil de texte
     * @param {HTMLCanvasElement} canvas - Élément canvas
     */
    enableTextTool(canvas) {
        try {
            if (!canvas || !canvas._xcanvas) throw new Error('Canvas non initialisé');
            
            const addText = (e) => {
                const pos = this.getMousePosition(canvas, e);
                
                // Créer un élément input temporaire
                const input = document.createElement('input');
                input.type = 'text';
                input.style.position = 'absolute';
                input.style.left = `${pos.x + canvas.offsetLeft}px`;
                input.style.top = `${pos.y + canvas.offsetTop}px`;
                input.style.border = '1px dashed #000';
                input.style.padding = '5px';
                input.style.fontFamily = 'Arial';
                input.style.fontSize = '16px';
                input.style.zIndex = '1000';
                
                // Ajouter au wrapper
                canvas._xcanvas.wrapper.appendChild(input);
                input.focus();
                
                const handleKeyDown = (e) => {
                    if (e.key === 'Enter') {
                        // Dessiner le texte sur le canvas
                        const ctx = canvas._xcanvas.activeLayer.ctx;
                        ctx.font = '16px Arial';
                        ctx.fillStyle = canvas._xcanvas.fillColor || '#000000';
                        ctx.fillText(input.value, pos.x, pos.y + 16); // +16 pour compenser la hauteur du texte
                        
                        // Supprimer l'input
                        input.removeEventListener('keydown', handleKeyDown);
                        canvas._xcanvas.wrapper.removeChild(input);
                        
                        // Redessiner les calques
                        this.renderLayers(canvas);
                    } else if (e.key === 'Escape') {
                        // Annuler
                        input.removeEventListener('keydown', handleKeyDown);
                        canvas._xcanvas.wrapper.removeChild(input);
                    }
                };
                
                input.addEventListener('keydown', handleKeyDown);
            };
            
            // Supprimer l'ancien écouteur
            this.disableTextTool(canvas);
            
            // Ajouter le nouvel écouteur
            canvas.addEventListener('click', addText);
            
            // Sauvegarder le gestionnaire
            canvas._xcanvas.tools.text = {
                addText
            };
            
            return true;
        } catch (error) {
            console.error('XCanvas.enableTextTool - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Désactiver l'outil de texte
     * @param {HTMLCanvasElement} canvas - Élément canvas
     */
    disableTextTool(canvas) {
        try {
            if (!canvas || !canvas._xcanvas || !canvas._xcanvas.tools.text) {
                return false;
            }
            
            const { addText } = canvas._xcanvas.tools.text;
            canvas.removeEventListener('click', addText);
            delete canvas._xcanvas.tools.text;
            return true;
        } catch (error) {
            console.error('XCanvas.disableTextTool - Erreur:', error.message);
            return false;
        }
    },

    // ======================== OUTILS DE FORME ========================
    /**
     * Activer l'outil de forme
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {string} shapeType - Type de forme ('rect', 'circle', 'line', 'polygon')
     */
    enableShapeTool(canvas, shapeType = 'rect') {
        try {
            if (!canvas || !canvas._xcanvas) throw new Error('Canvas non initialisé');
            
            let isDrawing = false;
            let startX, startY;
            let tempShape = null;
            
            const startDrawing = (e) => {
                const pos = this.getMousePosition(canvas, e);
                startX = pos.x;
                startY = pos.y;
                isDrawing = true;
                
                tempShape = {
                    type: shapeType,
                    startX,
                    startY,
                    currentX: startX,
                    currentY: startY
                };
            };
            
            const drawShape = (e) => {
                if (!isDrawing || !tempShape) return;
                
                const pos = this.getMousePosition(canvas, e);
                tempShape.currentX = pos.x;
                tempShape.currentY = pos.y;
                
                // Redessiner les calques + forme temporaire
                this.renderLayers(canvas);
                
                const ctx = canvas.getContext('2d');
                this.drawTempShape(ctx, tempShape);
            };
            
            const endDrawing = (e) => {
                if (!isDrawing || !tempShape) return;
                isDrawing = false;
                
                const pos = this.getMousePosition(canvas, e);
                tempShape.currentX = pos.x;
                tempShape.currentY = pos.y;
                
                // Dessiner la forme finale sur le calque actif
                const layerCtx = canvas._xcanvas.activeLayer.ctx;
                this.drawTempShape(layerCtx, tempShape, true);
                
                tempShape = null;
                this.renderLayers(canvas);
            };
            
            // Dessiner une forme temporaire ou finale
            this.drawTempShape = (ctx, shape, isFinal = false) => {
                ctx.save();
                
                if (isFinal) {
                    ctx.fillStyle = canvas._xcanvas.fillColor || '#000000';
                    ctx.strokeStyle = canvas._xcanvas.strokeColor || '#000000';
                    ctx.lineWidth = canvas._xcanvas.lineWidth || 2;
                } else {
                    ctx.strokeStyle = '#0095ff';
                    ctx.lineWidth = 1;
                    ctx.setLineDash([5, 5]);
                }
                
                const x = Math.min(shape.startX, shape.currentX);
                const y = Math.min(shape.startY, shape.currentY);
                const width = Math.abs(shape.currentX - shape.startX);
                const height = Math.abs(shape.currentY - shape.startY);
                
                switch (shape.type) {
                    case 'rect':
                        if (isFinal) {
                            ctx.fillRect(x, y, width, height);
                            ctx.strokeRect(x, y, width, height);
                        } else {
                            ctx.strokeRect(x, y, width, height);
                        }
                        break;
                        
                    case 'circle':
                        const radius = Math.sqrt(width * width + height * height) / 2;
                        const centerX = shape.startX + (shape.currentX - shape.startX) / 2;
                        const centerY = shape.startY + (shape.currentY - shape.startY) / 2;
                        
                        ctx.beginPath();
                        ctx.arc(centerX, centerY, radius, 0, Math.PI * 2);
                        if (isFinal) {
                            ctx.fill();
                            ctx.stroke();
                        } else {
                            ctx.stroke();
                        }
                        break;
                        
                    case 'line':
                        ctx.beginPath();
                        ctx.moveTo(shape.startX, shape.startY);
                        ctx.lineTo(shape.currentX, shape.currentY);
                        ctx.stroke();
                        break;
                        
                    case 'polygon':
                        // Implémentation simplifiée (triangle pour l'exemple)
                        ctx.beginPath();
                        ctx.moveTo(shape.startX, shape.startY);
                        ctx.lineTo(shape.currentX, shape.currentY);
                        ctx.lineTo(shape.startX + (shape.currentX - shape.startX) / 2, shape.startY - height);
                        ctx.closePath();
                        if (isFinal) {
                            ctx.fill();
                            ctx.stroke();
                        } else {
                            ctx.stroke();
                        }
                        break;
                }
                
                ctx.restore();
            };
            
            // Supprimer les anciens écouteurs
            this.disableShapeTool(canvas);
            
            // Ajouter les nouveaux écouteurs
            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', drawShape);
            canvas.addEventListener('mouseup', endDrawing);
            canvas.addEventListener('mouseout', endDrawing);
            
            // Sauvegarder les gestionnaires
            canvas._xcanvas.tools.shape = {
                startDrawing,
                drawShape,
                endDrawing,
                shapeType
            };
            
            return true;
        } catch (error) {
            console.error('XCanvas.enableShapeTool - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Désactiver l'outil de forme
     * @param {HTMLCanvasElement} canvas - Élément canvas
     */
    disableShapeTool(canvas) {
        try {
            if (!canvas || !canvas._xcanvas || !canvas._xcanvas.tools.shape) {
                return false;
            }
            
            const { startDrawing, drawShape, endDrawing } = canvas._xcanvas.tools.shape;
            
            canvas.removeEventListener('mousedown', startDrawing);
            canvas.removeEventListener('mousemove', drawShape);
            canvas.removeEventListener('mouseup', endDrawing);
            canvas.removeEventListener('mouseout', endDrawing);
            
            delete canvas._xcanvas.tools.shape;
            return true;
        } catch (error) {
            console.error('XCanvas.disableShapeTool - Erreur:', error.message);
            return false;
        }
    },

    // ======================== OUTILS DE TRANSFORMATION ========================
    /**
     * Redimensionner la sélection
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {number} scaleX - Échelle horizontale
     * @param {number} scaleY - Échelle verticale
     */
    resizeSelection(canvas, scaleX, scaleY) {
        try {
            if (!canvas || !canvas._xcanvas || !canvas._xcanvas.selection) {
                throw new Error('Aucune sélection active');
            }
            
            const selection = canvas._xcanvas.selection;
            const layer = canvas._xcanvas.activeLayer;
            
            // Obtenir les données de l'image sélectionnée
            const imageData = layer.ctx.getImageData(
                selection.x,
                selection.y,
                selection.width,
                selection.height
            );
            
            // Créer un canvas temporaire pour le redimensionnement
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = selection.width;
            tempCanvas.height = selection.height;
            const tempCtx = tempCanvas.getContext('2d');
            tempCtx.putImageData(imageData, 0, 0);
            
            // Calculer les nouvelles dimensions
            const newWidth = selection.width * scaleX;
            const newHeight = selection.height * scaleY;
            
            // Effacer la zone originale
            layer.ctx.clearRect(selection.x, selection.y, selection.width, selection.height);
            
            // Redessiner l'image redimensionnée
            layer.ctx.drawImage(
                tempCanvas,
                0, 0, selection.width, selection.height,
                selection.x, selection.y, newWidth, newHeight
            );
            
            // Mettre à jour la sélection
            canvas._xcanvas.selection.width = newWidth;
            canvas._xcanvas.selection.height = newHeight;
            
            this.renderLayers(canvas);
            return true;
        } catch (error) {
            console.error('XCanvas.resizeSelection - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Faire pivoter la sélection
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {number} angle - Angle de rotation en degrés
     */
    rotateSelection(canvas, angle) {
        try {
            if (!canvas || !canvas._xcanvas || !canvas._xcanvas.selection) {
                throw new Error('Aucune sélection active');
            }
            
            const selection = canvas._xcanvas.selection;
            const layer = canvas._xcanvas.activeLayer;
            
            // Obtenir les données de l'image sélectionnée
            const imageData = layer.ctx.getImageData(
                selection.x,
                selection.y,
                selection.width,
                selection.height
            );
            
            // Créer un canvas temporaire pour la rotation
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = selection.width;
            tempCanvas.height = selection.height;
            const tempCtx = tempCanvas.getContext('2d');
            tempCtx.putImageData(imageData, 0, 0);
            
            // Effacer la zone originale
            layer.ctx.clearRect(selection.x, selection.y, selection.width, selection.height);
            
            // Sauvegarder le contexte
            layer.ctx.save();
            
            // Déplacer le point d'origine au centre de la sélection
            layer.ctx.translate(
                selection.x + selection.width / 2,
                selection.y + selection.height / 2
            );
            
            // Appliquer la rotation
            layer.ctx.rotate(angle * Math.PI / 180);
            
            // Redessiner l'image pivotée
            layer.ctx.drawImage(
                tempCanvas,
                -selection.width / 2,
                -selection.height / 2,
                selection.width,
                selection.height
            );
            
            // Restaurer le contexte
            layer.ctx.restore();
            
            this.renderLayers(canvas);
            return true;
        } catch (error) {
            console.error('XCanvas.rotateSelection - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Retourner la sélection horizontalement ou verticalement
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {string} direction - 'horizontal' ou 'vertical'
     */
    flipSelection(canvas, direction = 'horizontal') {
        try {
            if (!canvas || !canvas._xcanvas || !canvas._xcanvas.selection) {
                throw new Error('Aucune sélection active');
            }
            
            const selection = canvas._xcanvas.selection;
            const layer = canvas._xcanvas.activeLayer;
            
            // Obtenir les données de l'image sélectionnée
            const imageData = layer.ctx.getImageData(
                selection.x,
                selection.y,
                selection.width,
                selection.height
            );
            
            // Créer un canvas temporaire pour le retournement
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = selection.width;
            tempCanvas.height = selection.height;
            const tempCtx = tempCanvas.getContext('2d');
            
            // Appliquer le retournement
            if (direction === 'horizontal') {
                tempCtx.translate(selection.width, 0);
                tempCtx.scale(-1, 1);
            } else {
                tempCtx.translate(0, selection.height);
                tempCtx.scale(1, -1);
            }
            
            tempCtx.putImageData(imageData, 0, 0);
            
            // Effacer la zone originale
            layer.ctx.clearRect(selection.x, selection.y, selection.width, selection.height);
            
            // Redessiner l'image retournée
            layer.ctx.drawImage(tempCanvas, selection.x, selection.y);
            
            this.renderLayers(canvas);
            return true;
        } catch (error) {
            console.error('XCanvas.flipSelection - Erreur:', error.message);
            return false;
        }
    },

    // ======================== OUTILS DE COULEUR ========================
    /**
     * Appliquer un ajustement de teinte/saturation/luminosité
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {number} hue - Teinte (-180 à 180)
     * @param {number} saturation - Saturation (-100 à 100)
     * @param {number} lightness - Luminosité (-100 à 100)
     */
    adjustHSL(canvas, hue = 0, saturation = 0, lightness = 0) {
        try {
            if (!canvas || !canvas._xcanvas) throw new Error('Canvas non initialisé');
            
            this.saveToHistory(canvas);
            const layer = canvas._xcanvas.activeLayer;
            const imageData = layer.ctx.getImageData(0, 0, canvas.width, canvas.height);
            const data = imageData.data;
            
            // Convertir les valeurs en facteurs
            const h = hue / 180;
            const s = saturation / 100;
            const l = lightness / 100;
            
            for (let i = 0; i < data.length; i += 4) {
                let r = data[i] / 255;
                let g = data[i + 1] / 255;
                let b = data[i + 2] / 255;
                
                // Convertir RGB en HSL
                const max = Math.max(r, g, b);
                const min = Math.min(r, g, b);
                let h, s, l = (max + min) / 2;
                
                if (max === min) {
                    h = s = 0; // Achromatique
                } else {
                    const d = max - min;
                    s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                    
                    switch (max) {
                        case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                        case g: h = (b - r) / d + 2; break;
                        case b: h = (r - g) / d + 4; break;
                    }
                    
                    h /= 6;
                }
                
                // Appliquer les ajustements
                h = (h + hue) % 1;
                if (h < 0) h += 1;
                
                s = Math.max(0, Math.min(1, s + s));
                l = Math.max(0, Math.min(1, l + l));
                
                // Convertir HSL en RGB
                if (s === 0) {
                    r = g = b = l; // Achromatique
                } else {
                    const hue2rgb = (p, q, t) => {
                        if (t < 0) t += 1;
                        if (t > 1) t -= 1;
                        if (t < 1/6) return p + (q - p) * 6 * t;
                        if (t < 1/2) return q;
                        if (t < 2/3) return p + (q - p) * (2/3 - t) * 6;
                        return p;
                    };
                    
                    const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
                    const p = 2 * l - q;
                    
                    r = hue2rgb(p, q, h + 1/3);
                    g = hue2rgb(p, q, h);
                    b = hue2rgb(p, q, h - 1/3);
                }
                
                // Mettre à jour les valeurs
                data[i] = r * 255;
                data[i + 1] = g * 255;
                data[i + 2] = b * 255;
            }
            
            layer.ctx.putImageData(imageData, 0, 0);
            this.renderLayers(canvas);
            return true;
        } catch (error) {
            console.error('XCanvas.adjustHSL - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Remplacer une couleur par une autre
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {string} targetColor - Couleur à remplacer (hex, rgb ou hsl)
     * @param {string} newColor - Nouvelle couleur (hex, rgb ou hsl)
     * @param {number} tolerance - Tolérance (0-100)
     */
    replaceColor(canvas, targetColor, newColor, tolerance = 10) {
        try {
            if (!canvas || !canvas._xcanvas) throw new Error('Canvas non initialisé');
            
            this.saveToHistory(canvas);
            const layer = canvas._xcanvas.activeLayer;
            const imageData = layer.ctx.getImageData(0, 0, canvas.width, canvas.height);
            const data = imageData.data;
            
            // Convertir les couleurs en RGB
            const targetRgb = this.colorToRgb(targetColor);
            const newRgb = this.colorToRgb(newColor);
            
            if (!targetRgb || !newRgb) throw new Error('Couleur invalide');
            
            const tol = tolerance / 100 * 255;
            
            for (let i = 0; i < data.length; i += 4) {
                const r = data[i];
                const g = data[i + 1];
                const b = data[i + 2];
                
                // Vérifier si la couleur est dans la plage de tolérance
                if (Math.abs(r - targetRgb.r) <= tol &&
                    Math.abs(g - targetRgb.g) <= tol &&
                    Math.abs(b - targetRgb.b) <= tol) {
                    
                    // Remplacer la couleur
                    data[i] = newRgb.r;
                    data[i + 1] = newRgb.g;
                    data[i + 2] = newRgb.b;
                }
            }
            
            layer.ctx.putImageData(imageData, 0, 0);
            this.renderLayers(canvas);
            return true;
        } catch (error) {
            console.error('XCanvas.replaceColor - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Convertir une couleur en objet RGB
     * @param {string} color - Couleur (hex, rgb ou hsl)
     * @returns {object} { r, g, b }
     */
    colorToRgb(color) {
        // Implémentation simplifiée - en réalité, il faudrait gérer tous les formats
        const ctx = document.createElement('canvas').getContext('2d');
        ctx.fillStyle = color;
        const hex = ctx.fillStyle;
        
        if (hex.match(/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i)) {
            return {
                r: parseInt(hex.substr(1, 2), 16),
                g: parseInt(hex.substr(3, 2), 16),
                b: parseInt(hex.substr(5, 2), 16)
            };
        }
        
        return null;
    },

    // ======================== GESTION DES FICHIERS ========================
    /**
     * Ouvrir une image depuis un fichier
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {File} file - Fichier image
     * @param {function} callback - Fonction de rappel
     */
    openImage(canvas, file, callback = null) {
        try {
            if (!canvas || !canvas._xcanvas) throw new Error('Canvas non initialisé');
            if (!file || !file.type.match('image.*')) throw new Error('Fichier non valide');
            
            const reader = new FileReader();
            
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    // Redimensionner le canvas si nécessaire
                    if (canvas.width !== img.width || canvas.height !== img.height) {
                        canvas.width = img.width;
                        canvas.height = img.height;
                        
                        // Redimensionner tous les calques
                        canvas._xcanvas.layers.forEach(layer => {
                            const tempCanvas = document.createElement('canvas');
                            tempCanvas.width = img.width;
                            tempCanvas.height = img.height;
                            layer.canvas = tempCanvas;
                            layer.ctx = tempCanvas.getContext('2d');
                        });
                    }
                    
                    // Dessiner l'image sur le calque actif
                    canvas._xcanvas.activeLayer.ctx.drawImage(img, 0, 0);
                    this.renderLayers(canvas);
                    this.saveToHistory(canvas);
                    callback?.(true);
                };
                
                img.onerror = () => {
                    callback?.(false, new Error('Erreur de chargement de l\'image'));
                };
                
                img.src = e.target.result;
            };
            
            reader.onerror = () => {
                callback?.(false, new Error('Erreur de lecture du fichier'));
            };
            
            reader.readAsDataURL(file);
            return true;
        } catch (error) {
            console.error('XCanvas.openImage - Erreur:', error.message);
            callback?.(false, error);
            return false;
        }
    },

    /**
     * Enregistrer le canvas dans un fichier
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {string} filename - Nom du fichier
     * @param {string} format - Format ('png', 'jpeg', 'webp')
     * @param {number} quality - Qualité (0-1)
     */
    saveImage(canvas, filename = 'image', format = 'png', quality = 1) {
        try {
            if (!canvas || !canvas._xcanvas) throw new Error('Canvas non initialisé');
            
            const link = document.createElement('a');
            link.download = `${filename}.${format}`;
            link.href = canvas.toDataURL(`image/${format}`, quality);
            link.click();
            return true;
        } catch (error) {
            console.error('XCanvas.saveImage - Erreur:', error.message);
            return false;
        }
    },

    // ======================== UTILITAIRES ========================
    /**
     * Obtenir la position de la souris relative au canvas
     * @param {HTMLCanvasElement} canvas - Élément canvas
     * @param {MouseEvent|TouchEvent} event - Événement de souris/touch
     * @returns {object} { x, y }
     */
    getMousePosition(canvas, event) {
        try {
            const rect = canvas.getBoundingClientRect();
            let clientX, clientY;
            
            if (event.touches) {
                clientX = event.touches[0].clientX;
                clientY = event.touches[0].clientY;
            } else {
                clientX = event.clientX;
                clientY = event.clientY;
            }
            
            return {
                x: clientX - rect.left,
                y: clientY - rect.top
            };
        } catch (error) {
            console.error('XCanvas.getMousePosition - Erreur:', error.message);
            return { x: 0, y: 0 };
        }
    },

    /**
     * Sauvegarder l'état actuel dans l'historique
     * @param {HTMLCanvasElement} canvas - Élément canvas
     */
    saveToHistory(canvas) {
        try {
            if (!canvas || !canvas._xcanvas) throw new Error('Canvas non initialisé');
            
            // Limiter la taille de l'historique
            if (canvas._xcanvas.historyIndex < canvas._xcanvas.history.length - 1) {
                canvas._xcanvas.history = canvas._xcanvas.history.slice(0, canvas._xcanvas.historyIndex + 1);
            }
            
            // Sauvegarder l'image actuelle
            const imageData = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height);
            canvas._xcanvas.history.push(imageData);
            canvas._xcanvas.historyIndex = canvas._xcanvas.history.length - 1;
            
            return true;
        } catch (error) {
            console.error('XCanvas.saveToHistory - Erreur:', error.message);
            return false;
        }
    },

    /**
     * Annuler la dernière action
     * @param {HTMLCanvasElement} canvas - Élément canvas
     */
    // undo(canvas) {
    //     try {
    //         if (!canvas || !canvas._xcanvas || canvas._xcanvas.historyIndex <= 0) {
    //             return false;
    //         }
            
    //         canvas._xcanvas.historyIndex--;
    //         const imageData = canvas._xcanvas.history[canvas._xcanvas.historyIndex];
    //         canvas.getContext('2d').putImageData(imageData, 0, 0);
    //         return true;
    //     } catch (error) {
    //         console.error('XCanvas.undo - Erreur:', error.message);
    //         return false;
    //     }
    // },

    /**
     * Rétablir la dernière action annulée
     * @param {HTMLCanvasElement} canvas - Élément canvas
     */
    // redo(canvas) {
    //     try {
    //         if (!canvas || !canvas._xcanvas || canvas._xcanvas.historyIndex >= canvas._xcanvas.history.length - 1) {
    //             return false;
    //         }
            
    //         canvas._xcanvas.historyIndex++;
    //         const imageData = canvas._xcanvas.history[canvas._xcanvas.historyIndex];
    //         canvas.getContext('2d').putImageData(imageData, 0, 0);
    //         return true;
    //     } catch (error) {
    //         console.error('XCanvas.redo - Erreur:', error.message);
    //         return false;
    //     }
    // }
};

// ======================== ALIAS ========================
// Création d'alias avec le préfixe x_canvas_
const x_canvas_new = XCanvas.create.bind(XCanvas);
const x_canvas_line = XCanvas.drawLine.bind(XCanvas);
const x_canvas_rect = XCanvas.drawRect.bind(XCanvas);
const x_canvas_circle = XCanvas.drawCircle.bind(XCanvas);
const x_canvas_text = XCanvas.drawText.bind(XCanvas);
const x_canvas_clear = XCanvas.clearArea.bind(XCanvas);
const x_canvas_save = XCanvas.saveState.bind(XCanvas);
const x_canvas_restore = XCanvas.restoreState.bind(XCanvas);
const x_canvas_pen = XCanvas.enableFreeDrawing.bind(XCanvas);
const x_canvas_stop_pen = XCanvas.disableFreeDrawing.bind(XCanvas);
const x_canvas_eraser = XCanvas.enableEraser.bind(XCanvas);
const x_canvas_stop_eraser = XCanvas.disableEraser.bind(XCanvas);
const x_canvas_fill = XCanvas.enableFillTool.bind(XCanvas);
const x_canvas_stop_fill = XCanvas.disableFillTool.bind(XCanvas);
const x_canvas_get_pos = XCanvas.getMousePosition.bind(XCanvas);
const x_canvas_export = XCanvas.exportImage.bind(XCanvas);
const x_canvas_download = XCanvas.downloadImage.bind(XCanvas);
const x_canvas_load = XCanvas.loadImage.bind(XCanvas);
const x_canvas_bw = XCanvas.applyBlackAndWhite.bind(XCanvas);
const x_canvas_blur = XCanvas.applyBlur.bind(XCanvas);
const x_canvas_brightness = XCanvas.applyBrightnessContrast.bind(XCanvas);
const x_canvas_undo = XCanvas.undo.bind(XCanvas);
const x_canvas_redo = XCanvas.redo.bind(XCanvas);
const x_canvas_clear_all = XCanvas.clearCanvas.bind(XCanvas);
const x_canvas_resize = XCanvas.resizeCanvas.bind(XCanvas);
const x_canvas_image = XCanvas.drawImage.bind(XCanvas);
const x_canvas_polygon = XCanvas.drawPolygon.bind(XCanvas);
const x_canvas_bezier = XCanvas.drawBezierCurve.bind(XCanvas);
const x_canvas_quadratic = XCanvas.drawQuadraticCurve.bind(XCanvas);
const x_canvas_ellipse = XCanvas.drawEllipse.bind(XCanvas);
const x_canvas_gradient_linear = XCanvas.drawLinearGradient.bind(XCanvas);
const x_canvas_gradient_radial = XCanvas.drawRadialGradient.bind(XCanvas);
const x_canvas_pattern = XCanvas.drawPattern.bind(XCanvas);
const x_canvas_shadow = XCanvas.setShadow.bind(XCanvas);
const x_canvas_transform = XCanvas.transform.bind(XCanvas);
const x_canvas_reset_transform = XCanvas.resetTransform.bind(XCanvas);
const x_canvas_threshold = XCanvas.applyThreshold.bind(XCanvas);
const x_canvas_sepia = XCanvas.applySepia.bind(XCanvas);
const x_canvas_invert = XCanvas.applyInvert.bind(XCanvas);
const x_canvas_saturation = XCanvas.applySaturation.bind(XCanvas);
const x_canvas_add_event = XCanvas.addEventListener.bind(XCanvas);
const x_canvas_remove_event = XCanvas.removeEventListener.bind(XCanvas);
const x_canvas_remove_all_events = XCanvas.removeAllEventListeners.bind(XCanvas);
const x_canvas_point_in_rect = XCanvas.isPointInRect.bind(XCanvas);
const x_canvas_point_in_circle = XCanvas.isPointInCircle.bind(XCanvas);
const x_canvas_rect_colliding = XCanvas.isRectColliding.bind(XCanvas);
const x_canvas_animate = XCanvas.startAnimation.bind(XCanvas);
const x_canvas_stop_animate = XCanvas.stopAnimation.bind(XCanvas);



const x_canvas_add_tool = XCanvas.addTool.bind(XCanvas);
const x_canvas_add_color_picker = XCanvas.addColorPicker.bind(XCanvas);
const x_canvas_add_width_selector = XCanvas.addWidthSelector.bind(XCanvas);
const x_canvas_create_layer = XCanvas.createLayer.bind(XCanvas);
const x_canvas_render_layers = XCanvas.renderLayers.bind(XCanvas);
const x_canvas_merge_layers = XCanvas.mergeLayers.bind(XCanvas);
const x_canvas_enable_selection = XCanvas.enableSelectionTool.bind(XCanvas);
const x_canvas_disable_selection = XCanvas.disableSelectionTool.bind(XCanvas);
const x_canvas_enable_move = XCanvas.enableMoveTool.bind(XCanvas);
const x_canvas_disable_move = XCanvas.disableMoveTool.bind(XCanvas);
const x_canvas_enable_crop = XCanvas.enableCropTool.bind(XCanvas);
const x_canvas_disable_crop = XCanvas.disableCropTool.bind(XCanvas);
const x_canvas_apply_crop = XCanvas.applyCrop.bind(XCanvas);
const x_canvas_enable_text = XCanvas.enableTextTool.bind(XCanvas);
const x_canvas_disable_text = XCanvas.disableTextTool.bind(XCanvas);
const x_canvas_enable_shape = XCanvas.enableShapeTool.bind(XCanvas);
const x_canvas_disable_shape = XCanvas.disableShapeTool.bind(XCanvas);
const x_canvas_resize_selection = XCanvas.resizeSelection.bind(XCanvas);
const x_canvas_rotate_selection = XCanvas.rotateSelection.bind(XCanvas);
const x_canvas_flip_selection = XCanvas.flipSelection.bind(XCanvas);
const x_canvas_adjust_hsl = XCanvas.adjustHSL.bind(XCanvas);
const x_canvas_replace_color = XCanvas.replaceColor.bind(XCanvas);
const x_canvas_open_image = XCanvas.openImage.bind(XCanvas);
const x_canvas_save_image = XCanvas.saveImage.bind(XCanvas);
//const x_canvas_get_pos = XCanvas.getMousePosition.bind(XCanvas);
const x_canvas_save_history = XCanvas.saveToHistory.bind(XCanvas);
//const x_canvas_undo = XCanvas.undo.bind(XCanvas);
//const x_canvas_redo = XCanvas.redo.bind(XCanvas);

// Export pour le navigateur
if (typeof window !== 'undefined') {
    window.XCanvas = XCanvas;
    // Alias global
    window.x_canvas = XCanvas;
}

// Export pour les modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = XCanvas;
}