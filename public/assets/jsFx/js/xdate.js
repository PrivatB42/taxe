/**
 * Bibliothèque X_DATE - Manipulation avancée des dates
 * @version 1.0
 * @license MIT
 */
const X_DATE = {
    // ======================== CONSTRUCTEURS ========================
    
    /** Créer une date actuelle (alias x_date_now) */
    now() {
        return new Date();
    },
    
    /** Créer une date à partir d'une chaîne (alias x_date_fromString) */
    fromString(dateString, format = 'iso') {
        if (format === 'iso') {
            return new Date(dateString);
        } else if (format === 'fr') {
            // Format français: JJ/MM/AAAA
            const parts = dateString.split('/');
            if (parts.length === 3) {
                return new Date(parts[2], parts[1] - 1, parts[0]);
            }
        } else if (format === 'us') {
            // Format US: MM/DD/AAAA
            const parts = dateString.split('/');
            if (parts.length === 3) {
                return new Date(parts[2], parts[0] - 1, parts[1]);
            }
        }
        return new Date(dateString);
    },
    
    /** Créer une date à partir d'un timestamp (alias x_date_fromTimestamp) */
    fromTimestamp(timestamp) {
        return new Date(timestamp);
    },
    
    /** Créer une date à partir de parties (alias x_date_fromParts) */
    fromParts(year, month, day, hours = 0, minutes = 0, seconds = 0, milliseconds = 0) {
        return new Date(year, month - 1, day, hours, minutes, seconds, milliseconds);
    },
    
    // ======================== FORMATAGE ========================
    
    /** Formater une date (alias x_date_format) */
    format(date, formatString = 'dd/mm/yyyy') {
        const d = new Date(date);
        if (isNaN(d.getTime())) return 'Date invalide';
        
        const tokens = {
            yyyy: d.getFullYear(),
            yy: String(d.getFullYear()).slice(-2),
            mm: String(d.getMonth() + 1).padStart(2, '0'),
            m: d.getMonth() + 1,
            dd: String(d.getDate()).padStart(2, '0'),
            d: d.getDate(),
            hh: String(d.getHours()).padStart(2, '0'),
            h: d.getHours(),
            ii: String(d.getMinutes()).padStart(2, '0'),
            i: d.getMinutes(),
            ss: String(d.getSeconds()).padStart(2, '0'),
            s: d.getSeconds(),
            ms: String(d.getMilliseconds()).padStart(3, '0'),
            D: this._getDayName(d.getDay(), 'short'),
            DD: this._getDayName(d.getDay(), 'long'),
            M: this._getMonthName(d.getMonth(), 'short'),
            MM: this._getMonthName(d.getMonth(), 'long')
        };
        
        return formatString.replace(
            /yyyy|yy|mm|m|dd|d|hh|h|ii|i|ss|s|ms|DD|D|MM|M/g,
            match => tokens[match] || match
        );
    },
    
    /** Formater en date relative (alias x_date_relative) */
    relative(date, options = {}) {
        const now = new Date();
        const d = new Date(date);
        const diffMs = now - d;
        const diffSec = Math.floor(diffMs / 1000);
        const diffMin = Math.floor(diffSec / 60);
        const diffHour = Math.floor(diffMin / 60);
        const diffDay = Math.floor(diffHour / 24);
        const diffWeek = Math.floor(diffDay / 7);
        const diffMonth = Math.floor(diffDay / 30);
        const diffYear = Math.floor(diffDay / 365);
        
        const config = {
            justNow: 'à l\'instant',
            seconds: 'il y a {{count}} seconde(s)',
            minutes: 'il y a {{count}} minute(s)',
            hours: 'il y a {{count}} heure(s)',
            days: 'il y a {{count}} jour(s)',
            weeks: 'il y a {{count}} semaine(s)',
            months: 'il y a {{count}} mois',
            years: 'il y a {{count}} an(s)',
            future: 'dans {{count}}',
            ...options
        };
        
        if (diffMs < 0) {
            const futureDiff = Math.abs(diffMs);
            const futureSec = Math.floor(futureDiff / 1000);
            
            if (futureSec < 60) return config.future.replace('{{count}}', `${futureSec} seconde(s)`);
            if (futureSec < 3600) return config.future.replace('{{count}}', `${Math.floor(futureSec/60)} minute(s)`);
            if (futureSec < 86400) return config.future.replace('{{count}}', `${Math.floor(futureSec/3600)} heure(s)`);
            return config.future.replace('{{count}}', `${Math.floor(futureSec/86400)} jour(s)`);
        }
        
        if (diffSec < 10) return config.justNow;
        if (diffSec < 60) return config.seconds.replace('{{count}}', diffSec);
        if (diffMin < 60) return config.minutes.replace('{{count}}', diffMin);
        if (diffHour < 24) return config.hours.replace('{{count}}', diffHour);
        if (diffDay < 7) return config.days.replace('{{count}}', diffDay);
        if (diffWeek < 4) return config.weeks.replace('{{count}}', diffWeek);
        if (diffMonth < 12) return config.months.replace('{{count}}', diffMonth);
        return config.years.replace('{{count}}', diffYear);
    },
    
    /** Formater en durée (alias x_date_duration) */
    duration(milliseconds, options = {}) {
        const config = {
            days: 'j',
            hours: 'h',
            minutes: 'min',
            seconds: 's',
            milliseconds: 'ms',
            separator: ' ',
            ...options
        };
        
        const seconds = Math.floor(milliseconds / 1000);
        const minutes = Math.floor(seconds / 60);
        const hours = Math.floor(minutes / 60);
        const days = Math.floor(hours / 24);
        
        const parts = [];
        
        if (days > 0) parts.push(`${days}${config.days}`);
        if (hours % 24 > 0) parts.push(`${hours % 24}${config.hours}`);
        if (minutes % 60 > 0) parts.push(`${minutes % 60}${config.minutes}`);
        if (seconds % 60 > 0) parts.push(`${seconds % 60}${config.seconds}`);
        if (milliseconds % 1000 > 0 && milliseconds < 1000) {
            parts.push(`${milliseconds % 1000}${config.milliseconds}`);
        }
        
        return parts.join(config.separator);
    },
    
    // ======================== MANIPULATION ========================
    
    /** Ajouter du temps (alias x_date_add) */
    add(date, amount, unit = 'days') {
        const d = new Date(date);
        const units = {
            milliseconds: amount,
            seconds: amount * 1000,
            minutes: amount * 1000 * 60,
            hours: amount * 1000 * 60 * 60,
            days: amount * 1000 * 60 * 60 * 24,
            weeks: amount * 1000 * 60 * 60 * 24 * 7,
            months: function() {
                d.setMonth(d.getMonth() + amount);
                return d;
            },
            years: function() {
                d.setFullYear(d.getFullYear() + amount);
                return d;
            }
        };
        
        if (typeof units[unit] === 'function') {
            return units[unit]();
        } else if (units[unit] !== undefined) {
            return new Date(d.getTime() + units[unit]);
        }
        
        return d;
    },
    
    /** Soustraire du temps (alias x_date_subtract) */
    subtract(date, amount, unit = 'days') {
        return this.add(date, -amount, unit);
    },
    
    /** Définir le début du jour (alias x_date_startOfDay) */
    startOfDay(date) {
        const d = new Date(date);
        d.setHours(0, 0, 0, 0);
        return d;
    },
    
    /** Définir la fin du jour (alias x_date_endOfDay) */
    endOfDay(date) {
        const d = new Date(date);
        d.setHours(23, 59, 59, 999);
        return d;
    },
    
    /** Définir le début du mois (alias x_date_startOfMonth) */
    startOfMonth(date) {
        const d = new Date(date);
        d.setDate(1);
        d.setHours(0, 0, 0, 0);
        return d;
    },
    
    /** Définir la fin du mois (alias x_date_endOfMonth) */
    endOfMonth(date) {
        const d = new Date(date);
        d.setMonth(d.getMonth() + 1);
        d.setDate(0);
        d.setHours(23, 59, 59, 999);
        return d;
    },
    
    /** Définir le début de l'année (alias x_date_startOfYear) */
    startOfYear(date) {
        const d = new Date(date);
        d.setMonth(0, 1);
        d.setHours(0, 0, 0, 0);
        return d;
    },
    
    /** Définir la fin de l'année (alias x_date_endOfYear) */
    endOfYear(date) {
        const d = new Date(date);
        d.setMonth(11, 31);
        d.setHours(23, 59, 59, 999);
        return d;
    },
    
    // ======================== COMPARAISON ========================
    
    /** Vérifier si deux dates sont égales (alias x_date_isEqual) */
    isEqual(date1, date2, precision = 'millisecond') {
        const d1 = new Date(date1);
        const d2 = new Date(date2);
        
        const precisions = {
            millisecond: () => d1.getTime() === d2.getTime(),
            second: () => 
                d1.getFullYear() === d2.getFullYear() &&
                d1.getMonth() === d2.getMonth() &&
                d1.getDate() === d2.getDate() &&
                d1.getHours() === d2.getHours() &&
                d1.getMinutes() === d2.getMinutes() &&
                d1.getSeconds() === d2.getSeconds(),
            minute: () => 
                d1.getFullYear() === d2.getFullYear() &&
                d1.getMonth() === d2.getMonth() &&
                d1.getDate() === d2.getDate() &&
                d1.getHours() === d2.getHours() &&
                d1.getMinutes() === d2.getMinutes(),
            hour: () => 
                d1.getFullYear() === d2.getFullYear() &&
                d1.getMonth() === d2.getMonth() &&
                d1.getDate() === d2.getDate() &&
                d1.getHours() === d2.getHours(),
            day: () => 
                d1.getFullYear() === d2.getFullYear() &&
                d1.getMonth() === d2.getMonth() &&
                d1.getDate() === d2.getDate(),
            month: () => 
                d1.getFullYear() === d2.getFullYear() &&
                d1.getMonth() === d2.getMonth(),
            year: () => d1.getFullYear() === d2.getFullYear()
        };
        
        return precisions[precision] ? precisions[precision]() : false;
    },
    
    /** Vérifier si une date est avant une autre (alias x_date_isBefore) */
    isBefore(date1, date2) {
        return new Date(date1) < new Date(date2);
    },
    
    /** Vérifier si une date est après une autre (alias x_date_isAfter) */
    isAfter(date1, date2) {
        return new Date(date1) > new Date(date2);
    },
    
    /** Vérifier si une date est entre deux autres (alias x_date_isBetween) */
    isBetween(date, start, end, inclusive = true) {
        const d = new Date(date);
        const s = new Date(start);
        const e = new Date(end);
        
        if (inclusive) {
            return d >= s && d <= e;
        }
        return d > s && d < e;
    },
    
    /** Obtenir la différence entre deux dates (alias x_date_diff) */
    diff(date1, date2, unit = 'milliseconds') {
        const d1 = new Date(date1);
        const d2 = new Date(date2);
        const diffMs = Math.abs(d2 - d1);
        
        const units = {
            milliseconds: diffMs,
            seconds: Math.floor(diffMs / 1000),
            minutes: Math.floor(diffMs / (1000 * 60)),
            hours: Math.floor(diffMs / (1000 * 60 * 60)),
            days: Math.floor(diffMs / (1000 * 60 * 60 * 24)),
            weeks: Math.floor(diffMs / (1000 * 60 * 60 * 24 * 7)),
            months: function() {
                let months = (d2.getFullYear() - d1.getFullYear()) * 12;
                months -= d1.getMonth();
                months += d2.getMonth();
                return Math.abs(months);
            },
            years: function() {
                return Math.abs(d2.getFullYear() - d1.getFullYear());
            }
        };
        
        if (typeof units[unit] === 'function') {
            return units[unit]();
        }
        
        return units[unit] || 0;
    },
    
    // ======================== INFORMATIONS ========================
    
    /** Obtenir le jour de la semaine (alias x_date_getDay) */
    getDay(date, format = 'number') {
        const d = new Date(date);
        if (format === 'number') return d.getDay();
        return this._getDayName(d.getDay(), format);
    },
    
    /** Obtenir le nom du mois (alias x_date_getMonth) */
    getMonth(date, format = 'number') {
        const d = new Date(date);
        if (format === 'number') return d.getMonth() + 1;
        return this._getMonthName(d.getMonth(), format);
    },
    
    /** Obtenir le nombre de jours dans le mois (alias x_date_daysInMonth) */
    daysInMonth(date) {
        const d = new Date(date);
        return new Date(d.getFullYear(), d.getMonth() + 1, 0).getDate();
    },
    
    /** Vérifier si c'est une année bissextile (alias x_date_isLeapYear) */
    isLeapYear(date) {
        const year = new Date(date).getFullYear();
        return (year % 4 === 0 && year % 100 !== 0) || year % 400 === 0;
    },
    
    /** Obtenir le trimestre (alias x_date_getQuarter) */
    getQuarter(date) {
        const month = new Date(date).getMonth();
        return Math.floor(month / 3) + 1;
    },
    
    /** Obtenir la semaine de l'année (alias x_date_getWeek) */
    getWeek(date) {
        const d = new Date(date);
        d.setHours(0, 0, 0, 0);
        d.setDate(d.getDate() + 3 - (d.getDay() + 6) % 7);
        const week1 = new Date(d.getFullYear(), 0, 4);
        return 1 + Math.round(((d - week1) / 86400000 - 3 + (week1.getDay() + 6) % 7) / 7);
    },
    
    // ======================== UTILITAIRES ========================
    
    /** Cloner une date (alias x_date_clone) */
    clone(date) {
        return new Date(date);
    },
    
    /** Vérifier si c'est une date valide (alias x_date_isValid) */
    isValid(date) {
        return !isNaN(new Date(date).getTime());
    },
    
    /** Obtenir le timestamp (alias x_date_getTimestamp) */
    getTimestamp(date) {
        return new Date(date).getTime();
    },
    
    /** Convertir en objet (alias x_date_toObject) */
    toObject(date) {
        const d = new Date(date);
        return {
            year: d.getFullYear(),
            month: d.getMonth() + 1,
            day: d.getDate(),
            hours: d.getHours(),
            minutes: d.getMinutes(),
            seconds: d.getSeconds(),
            milliseconds: d.getMilliseconds(),
            weekday: d.getDay()
        };
    },
    
    /** Convertir depuis un objet (alias x_date_fromObject) */
    fromObject(obj) {
        return new Date(
            obj.year,
            obj.month - 1,
            obj.day,
            obj.hours,
            obj.minutes,
            obj.seconds,
            obj.milliseconds
        );
    },
    
    // ======================== INTERNE ========================
    
    _getDayName(dayIndex, format = 'long') {
        const days = {
            long: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
            short: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam']
        };
        return days[format][dayIndex];
    },
    
    _getMonthName(monthIndex, format = 'long') {
        const months = {
            long: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            short: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc']
        };
        return months[format][monthIndex];
    }
};

// ======================== ALIAS ========================
const x_date_now = X_DATE.now.bind(X_DATE);
const x_date_fromString = X_DATE.fromString.bind(X_DATE);
const x_date_fromTimestamp = X_DATE.fromTimestamp.bind(X_DATE);
const x_date_fromParts = X_DATE.fromParts.bind(X_DATE);
const x_date_format = X_DATE.format.bind(X_DATE);
const x_date_relative = X_DATE.relative.bind(X_DATE);
const x_date_duration = X_DATE.duration.bind(X_DATE);
const x_date_add = X_DATE.add.bind(X_DATE);
const x_date_subtract = X_DATE.subtract.bind(X_DATE);
const x_date_startOfDay = X_DATE.startOfDay.bind(X_DATE);
const x_date_endOfDay = X_DATE.endOfDay.bind(X_DATE);
const x_date_startOfMonth = X_DATE.startOfMonth.bind(X_DATE);
const x_date_endOfMonth = X_DATE.endOfMonth.bind(X_DATE);
const x_date_startOfYear = X_DATE.startOfYear.bind(X_DATE);
const x_date_endOfYear = X_DATE.endOfYear.bind(X_DATE);
const x_date_isEqual = X_DATE.isEqual.bind(X_DATE);
const x_date_isBefore = X_DATE.isBefore.bind(X_DATE);
const x_date_isAfter = X_DATE.isAfter.bind(X_DATE);
const x_date_isBetween = X_DATE.isBetween.bind(X_DATE);
const x_date_diff = X_DATE.diff.bind(X_DATE);
const x_date_getDay = X_DATE.getDay.bind(X_DATE);
const x_date_getMonth = X_DATE.getMonth.bind(X_DATE);
const x_date_daysInMonth = X_DATE.daysInMonth.bind(X_DATE);
const x_date_isLeapYear = X_DATE.isLeapYear.bind(X_DATE);
const x_date_getQuarter = X_DATE.getQuarter.bind(X_DATE);
const x_date_getWeek = X_DATE.getWeek.bind(X_DATE);
const x_date_clone = X_DATE.clone.bind(X_DATE);
const x_date_isValid = X_DATE.isValid.bind(X_DATE);
const x_date_getTimestamp = X_DATE.getTimestamp.bind(X_DATE);
const x_date_toObject = X_DATE.toObject.bind(X_DATE);
const x_date_fromObject = X_DATE.fromObject.bind(X_DATE);

// Export pour le navigateur
if (typeof window !== 'undefined') {
    window.X_DATE = X_DATE;
}

// Export pour les modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = X_DATE;
}