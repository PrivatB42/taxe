/**
 * Gestion de l'authentification avec token JWT
 */

// Récupérer le token depuis localStorage
function getAuthToken() {
    return localStorage.getItem('auth_token');
}

// Récupérer les données utilisateur depuis localStorage
function getUser() {
    const userStr = localStorage.getItem('user');
    return userStr ? JSON.parse(userStr) : null;
}

// Vérifier si l'utilisateur est authentifié
function isAuthenticated() {
    return !!getAuthToken();
}

// Ajouter le token aux requêtes AJAX
function setupAuthInterceptor() {
    // Intercepter les requêtes fetch
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        const token = getAuthToken();
        if (token && args[1]) {
            args[1].headers = args[1].headers || {};
            if (!args[1].headers['Authorization']) {
                args[1].headers['Authorization'] = `Bearer ${token}`;
            }
        }
        return originalFetch.apply(this, args);
    };

    // Intercepter les requêtes XMLHttpRequest
    const originalOpen = XMLHttpRequest.prototype.open;
    const originalSend = XMLHttpRequest.prototype.send;

    XMLHttpRequest.prototype.open = function(method, url, ...rest) {
        this._url = url;
        return originalOpen.apply(this, [method, url, ...rest]);
    };

    XMLHttpRequest.prototype.send = function(...args) {
        const token = getAuthToken();
        if (token && this._url && !this._url.startsWith('http://') && !this._url.startsWith('https://')) {
            this.setRequestHeader('Authorization', `Bearer ${token}`);
        }
        return originalSend.apply(this, args);
    };
}

// Déconnexion
function logout() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
    window.location.href = '/login';
}

// Vérifier et rafraîchir le token si nécessaire
async function checkAndRefreshToken() {
    const token = getAuthToken();
    if (!token) {
        if (window.location.pathname !== '/login') {
            logout();
        }
        return false;
    }

    try {
        // Vérifier si le token est expiré
        const payload = JSON.parse(atob(token.split('.')[1]));
        const exp = payload.exp * 1000; // Convertir en millisecondes
        const now = Date.now();

        // Si le token expire dans moins de 5 minutes, le rafraîchir
        if (exp - now < 5 * 60 * 1000) {
            const response = await fetch('/api/auth/refresh', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.token) {
                    localStorage.setItem('auth_token', data.token);
                    return true;
                }
            }
        }

        return true;
    } catch (error) {
        console.error('Erreur lors de la vérification du token:', error);
        if (window.location.pathname !== '/login') {
            logout();
        }
        return false;
    }
}

// Initialiser l'authentification au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    setupAuthInterceptor();
    
    // Vérifier le token toutes les minutes
    setInterval(checkAndRefreshToken, 60 * 1000);
    
    // Vérifier immédiatement
    if (window.location.pathname !== '/login') {
        checkAndRefreshToken();
    }
});

// Exporter les fonctions pour utilisation globale
window.auth = {
    getToken: getAuthToken,
    getUser: getUser,
    isAuthenticated: isAuthenticated,
    logout: logout,
    checkAndRefreshToken: checkAndRefreshToken
};

