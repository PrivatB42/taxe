/**
 * Bibliothèque X_STRING - Manipulation avancée des strings
 * @version 1.0
 * @license MIT
 */
const X_STRING = {
    // ======================== CONVERSION ========================
    
    /** Convertir en majuscules (alias x_str_upper) */
    upper(str) {
        return str.toUpperCase();
    },
    
    /** Convertir en minuscules (alias x_str_lower) */
    lower(str) {
        return str.toLowerCase();
    },
    
    /** Première lettre en majuscule (alias x_str_ucfirst) */
    ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    },
    
    /** Première lettre de chaque mot en majuscule (alias x_str_ucwords) */
    ucwords(str) {
        return str.replace(/\b\w/g, char => char.toUpperCase());
    },
    
    /** Convertir en camelCase (alias x_str_camel) */
    camel(str) {
        return str
            .replace(/[^a-zA-Z0-9]+/g, ' ')
            .split(' ')
            .map((word, index) => 
                index === 0 
                    ? word.toLowerCase() 
                    : word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
            )
            .join('');
    },
    
    /** Convertir en snake_case (alias x_str_snake) */
    snake(str) {
        return str
            .replace(/[^a-zA-Z0-9]+/g, '_')
            .replace(/([a-z])([A-Z])/g, '$1_$2')
            .toLowerCase();
    },
    
    /** Convertir en kebab-case (alias x_str_kebab) */
    kebab(str) {
        return this.snake(str).replace(/_/g, '-');
    },
    
    /** Convertir en PascalCase (alias x_str_pascal) */
    pascal(str) {
        const camel = this.camel(str);
        return camel.charAt(0).toUpperCase() + camel.slice(1);
    },
    
    // ======================== MANIPULATION ========================
    
    /** Tronquer une chaîne (alias x_str_truncate) */
    truncate(str, length = 100, suffix = '...') {
        if (str.length <= length) return str;
        return str.substring(0, length) + suffix;
    },
    
    /** Répéter une chaîne (alias x_str_repeat) */
    repeat(str, count = 1) {
        return str.repeat(count);
    },
    
    /** Inverser une chaîne (alias x_str_reverse) */
    reverse(str) {
        return str.split('').reverse().join('');
    },
    
    /** Remplacer toutes les occurrences (alias x_str_replaceAll) */
    replaceAll(str, search, replacement) {
        return str.split(search).join(replacement);
    },
    
    /** Supprimer les espaces superflus (alias x_str_trim) */
    trim(str, chars = ' \t\n\r\0\x0B') {
        return str.replace(new RegExp(`^[${chars}]+|[${chars}]+$`, 'g'), '');
    },
    
    /** Supprimer les espaces à gauche (alias x_str_ltrim) */
    ltrim(str, chars = ' \t\n\r\0\x0B') {
        return str.replace(new RegExp(`^[${chars}]+`, 'g'), '');
    },
    
    /** Supprimer les espaces à droite (alias x_str_rtrim) */
    rtrim(str, chars = ' \t\n\r\0\x0B') {
        return str.replace(new RegExp(`[${chars}]+$`, 'g'), '');
    },
    
    // ======================== EXTRACTION ========================
    
    /** Extraire entre deux délimiteurs (alias x_str_between) */
    between(str, start, end) {
        const startIndex = str.indexOf(start);
        if (startIndex === -1) return '';
        
        const endIndex = str.indexOf(end, startIndex + start.length);
        if (endIndex === -1) return '';
        
        return str.substring(startIndex + start.length, endIndex);
    },
    
    /** Extraire avant un délimiteur (alias x_str_before) */
    before(str, delimiter) {
        const index = str.indexOf(delimiter);
        return index === -1 ? str : str.substring(0, index);
    },
    
    /** Extraire après un délimiteur (alias x_str_after) */
    after(str, delimiter) {
        const index = str.indexOf(delimiter);
        return index === -1 ? '' : str.substring(index + delimiter.length);
    },
    
    /** Obtenir les N premiers caractères (alias x_str_first) */
    first(str, length = 1) {
        return str.substring(0, length);
    },
    
    /** Obtenir les N derniers caractères (alias x_str_last) */
    last(str, length = 1) {
        return str.substring(str.length - length);
    },
    
    // ======================== RECHERCHE ========================
    
    /** Vérifier si contient une sous-chaîne (alias x_str_contains) */
    contains(str, search, caseSensitive = false) {
        if (!caseSensitive) {
            str = str.toLowerCase();
            search = search.toLowerCase();
        }
        return str.includes(search);
    },
    
    /** Vérifier si commence par (alias x_str_startsWith) */
    startsWith(str, search, caseSensitive = false) {
        if (!caseSensitive) {
            str = str.toLowerCase();
            search = search.toLowerCase();
        }
        return str.startsWith(search);
    },
    
    /** Vérifier si termine par (alias x_str_endsWith) */
    endsWith(str, search, caseSensitive = false) {
        if (!caseSensitive) {
            str = str.toLowerCase();
            search = search.toLowerCase();
        }
        return str.endsWith(search);
    },
    
    /** Compter les occurrences (alias x_str_count) */
    count(str, search, caseSensitive = false) {
        if (!caseSensitive) {
            str = str.toLowerCase();
            search = search.toLowerCase();
        }
        return str.split(search).length - 1;
    },
    
    /** Trouver la position (alias x_str_position) */
    position(str, search, caseSensitive = false, fromEnd = false) {
        if (!caseSensitive) {
            str = str.toLowerCase();
            search = search.toLowerCase();
        }
        return fromEnd ? str.lastIndexOf(search) : str.indexOf(search);
    },
    
    // ======================== VALIDATION ========================
    
    /** Vérifier si c'est un email valide (alias x_str_isEmail) */
    isEmail(str) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(str);
    },
    
    /** Vérifier si c'est une URL valide (alias x_str_isUrl) */
    isUrl(str) {
        try {
            new URL(str);
            return true;
        } catch {
            return false;
        }
    },
    
    /** Vérifier si c'est numérique (alias x_str_isNumeric) */
    isNumeric(str) {
        return !isNaN(parseFloat(str)) && isFinite(str);
    },
    
    /** Vérifier si c'est alphabétique (alias x_str_isAlpha) */
    isAlpha(str) {
        return /^[a-zA-Z]+$/.test(str);
    },
    
    /** Vérifier si c'est alphanumérique (alias x_str_isAlphaNum) */
    isAlphaNum(str) {
        return /^[a-zA-Z0-9]+$/.test(str);
    },
    
    /** Vérifier si c'est vide (alias x_str_isEmpty) */
    isEmpty(str) {
        return str.trim().length === 0;
    },
    
    /** Vérifier la longueur (alias x_str_isLength) */
    isLength(str, min = 0, max = Infinity) {
        const length = str.length;
        return length >= min && length <= max;
    },
    
    // ======================== FORMATAGE ========================
    
    /** Masquer une partie du texte (alias x_str_mask) */
    mask(str, options = {}) {
        const config = {
            start: 0,
            end: 4,
            maskChar: '*',
            showFirst: 0,
            showLast: 0,
            ...options
        };
        
        if (str.length <= config.showFirst + config.showLast) {
            return str;
        }
        
        const first = str.substring(0, config.showFirst);
        const last = config.showLast > 0 ? str.substring(str.length - config.showLast) : '';
        const masked = config.maskChar.repeat(str.length - config.showFirst - config.showLast);
        
        return first + masked + last;
    },
    
    /** Formater un nombre (alias x_str_formatNumber) */
    formatNumber(number, decimals = 0, decimalSep = ',', thousandSep = ' ') {
        const num = parseFloat(number);
        if (isNaN(num)) return number;
        
        return num.toFixed(decimals)
            .replace('.', decimalSep)
            .replace(/\B(?=(\d{3})+(?!\d))/g, thousandSep);
    },
    
    /** Formater une taille de fichier (alias x_str_formatFileSize) */
    formatFileSize(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(decimals)) + ' ' + sizes[i];
    },
    
    /** Slugifier (alias x_str_slug) */
    slug(str, separator = '-') {
        return str
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '') // Remove accents
            .replace(/[^a-z0-9]+/g, separator)
            .replace(new RegExp(`^${separator}|${separator}$`, 'g'), '');
    },
    
    /** HTML entities (alias x_str_entities) */
    entities(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    },
    
    /** Décoder HTML entities (alias x_str_decodeEntities) */
    decodeEntities(str) {
        const textarea = document.createElement('textarea');
        textarea.innerHTML = str;
        return textarea.value;
    },
    
    // ======================== UTILITAIRES ========================
    
    /** Générer un UUID (alias x_str_uuid) */
    uuid() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0;
            const v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    },
    
    /** Générer un mot de passe aléatoire (alias x_str_randomPassword) */
    randomPassword(length = 12, options = {}) {
        const config = {
            numbers: true,
            symbols: true,
            uppercase: true,
            lowercase: true,
            ...options
        };
        
        let chars = '';
        if (config.lowercase) chars += 'abcdefghijklmnopqrstuvwxyz';
        if (config.uppercase) chars += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if (config.numbers) chars += '0123456789';
        if (config.symbols) chars += '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        if (!chars) throw new Error('Au moins un type de caractère doit être activé');
        
        let password = '';
        for (let i = 0; i < length; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        
        return password;
    },
    
    /** Générer un lorem ipsum (alias x_str_lorem) */
    lorem(words = 50) {
        const loremWords = [
            'lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipiscing', 'elit',
            'sed', 'do', 'eiusmod', 'tempor', 'incididunt', 'ut', 'labore', 'et', 'dolore',
            'magna', 'aliqua', 'enim', 'ad', 'minim', 'veniam', 'quis', 'nostrud',
            'exercitation', 'ullamco', 'laboris', 'nisi', 'ut', 'aliquip', 'ex', 'ea',
            'commodo', 'consequat', 'duis', 'aute', 'irure', 'dolor', 'in', 'reprehenderit',
            'voluptate', 'velit', 'esse', 'cillum', 'dolore', 'eu', 'fugiat', 'nulla',
            'pariatur', 'excepteur', 'sint', 'occaecat', 'cupidatat', 'non', 'proident',
            'sunt', 'in', 'culpa', 'qui', 'officia', 'deserunt', 'mollit', 'anim', 'id', 'est', 'laborum'
        ];
        
        let result = '';
        for (let i = 0; i < words; i++) {
            result += loremWords[Math.floor(Math.random() * loremWords.length)];
            if (i < words - 1) result += ' ';
        }
        
        return this.ucfirst(result) + '.';
    },
    
    /** Compter les mots (alias x_str_wordCount) */
    wordCount(str) {
        return str.trim().split(/\s+/).filter(word => word.length > 0).length;
    },
    
    /** Compter les lignes (alias x_str_lineCount) */
    lineCount(str) {
        return str.split('\n').length;
    }
};

// ======================== ALIAS ========================
const x_str_upper = X_STRING.upper.bind(X_STRING);
const x_str_lower = X_STRING.lower.bind(X_STRING);
const x_str_ucfirst = X_STRING.ucfirst.bind(X_STRING);
const x_str_ucwords = X_STRING.ucwords.bind(X_STRING);
const x_str_camel = X_STRING.camel.bind(X_STRING);
const x_str_snake = X_STRING.snake.bind(X_STRING);
const x_str_kebab = X_STRING.kebab.bind(X_STRING);
const x_str_pascal = X_STRING.pascal.bind(X_STRING);
const x_str_truncate = X_STRING.truncate.bind(X_STRING);
const x_str_repeat = X_STRING.repeat.bind(X_STRING);
const x_str_reverse = X_STRING.reverse.bind(X_STRING);
const x_str_replaceAll = X_STRING.replaceAll.bind(X_STRING);
const x_str_trim = X_STRING.trim.bind(X_STRING);
const x_str_ltrim = X_STRING.ltrim.bind(X_STRING);
const x_str_rtrim = X_STRING.rtrim.bind(X_STRING);
const x_str_between = X_STRING.between.bind(X_STRING);
const x_str_before = X_STRING.before.bind(X_STRING);
const x_str_after = X_STRING.after.bind(X_STRING);
const x_str_first = X_STRING.first.bind(X_STRING);
const x_str_last = X_STRING.last.bind(X_STRING);
const x_str_contains = X_STRING.contains.bind(X_STRING);
const x_str_startsWith = X_STRING.startsWith.bind(X_STRING);
const x_str_endsWith = X_STRING.endsWith.bind(X_STRING);
const x_str_count = X_STRING.count.bind(X_STRING);
const x_str_position = X_STRING.position.bind(X_STRING);
const x_str_isEmail = X_STRING.isEmail.bind(X_STRING);
const x_str_isUrl = X_STRING.isUrl.bind(X_STRING);
const x_str_isNumeric = X_STRING.isNumeric.bind(X_STRING);
const x_str_isAlpha = X_STRING.isAlpha.bind(X_STRING);
const x_str_isAlphaNum = X_STRING.isAlphaNum.bind(X_STRING);
const x_str_isEmpty = X_STRING.isEmpty.bind(X_STRING);
const x_str_isLength = X_STRING.isLength.bind(X_STRING);
const x_str_mask = X_STRING.mask.bind(X_STRING);
const x_str_formatNumber = X_STRING.formatNumber.bind(X_STRING);
const x_str_formatFileSize = X_STRING.formatFileSize.bind(X_STRING);
const x_str_slug = X_STRING.slug.bind(X_STRING);
const x_str_entities = X_STRING.entities.bind(X_STRING);
const x_str_decodeEntities = X_STRING.decodeEntities.bind(X_STRING);
const x_str_uuid = X_STRING.uuid.bind(X_STRING);
const x_str_randomPassword = X_STRING.randomPassword.bind(X_STRING);
const x_str_lorem = X_STRING.lorem.bind(X_STRING);
const x_str_wordCount = X_STRING.wordCount.bind(X_STRING);
const x_str_lineCount = X_STRING.lineCount.bind(X_STRING);

// Export pour le navigateur
if (typeof window !== 'undefined') {
    window.X_STRING = X_STRING;
}

// Export pour les modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = X_STRING;
}



// // Conversion
// x_str_camel('hello world'); // → 'helloWorld'
// x_str_snake('HelloWorld'); // → 'hello_world'

// // Manipulation
// x_str_truncate('Lorem ipsum dolor sit amet', 10); // → 'Lorem ipsu...'
// x_str_mask('1234567890', { showFirst: 3, showLast: 2 }); // → '123*****90'

// // Validation
// x_str_isEmail('test@example.com'); // → true
// x_str_isUrl('https://example.com'); // → true

// // Formatage
// x_str_formatNumber(1234567.89, 2, ',', ' '); // → '1 234 567,89'
// x_str_formatFileSize(1024); // → '1 KB'

// // Utilitaires
// x_str_uuid(); // → 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'
// x_str_lorem(10); // → 'Lorem ipsum dolor sit amet consectetur...'