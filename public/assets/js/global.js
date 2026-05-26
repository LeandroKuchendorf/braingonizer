/**
 * Braingonizer - Main Application JS
 */

/**
 * Debug logging wrapper - only logs if debugging is enabled
 * @param {string} message - Message to log
 * @param {string|null} page - Page name (e.g., 'notes', 'tasks'). If null, only checks debug_global
 */
function debugLog(message, page = null) {
    if (typeof window.DEBUG_SETTINGS === 'undefined') {
        return;
    }
    
    if (page !== null) {
        const globalDebug = window.DEBUG_SETTINGS.debug_global === '1';
        const pageDebug = window.DEBUG_SETTINGS['debug_' + page] === '1';
        
        if (globalDebug || pageDebug) {
            console.log(`[DEBUG - ${page}] ${message}`);
        }
    } else {
        if (window.DEBUG_SETTINGS.debug_global === '1') {
            console.log(`[DEBUG] ${message}`);
        }
    }
}

/**
 * Debug AJAX request logging
 * @param {string} url - Request URL
 * @param {object} data - Request data
 * @param {string} page - Page name
 */
function debugAjax(url, data, page) {
    if (typeof window.DEBUG_SETTINGS === 'undefined') {
        return;
    }
    
    const globalDebug = window.DEBUG_SETTINGS.debug_global === '1';
    const pageDebug = window.DEBUG_SETTINGS['debug_' + page] === '1';
    
    if (globalDebug || pageDebug) {
        console.log(`[DEBUG - ${page} - AJAX] URL: ${url}`, data);
    }
}

/**
 * Debug AJAX response logging
 * @param {object} response - Response data
 * @param {string} page - Page name
 */
function debugResponse(response, page) {
    if (typeof window.DEBUG_SETTINGS === 'undefined') {
        return;
    }
    
    const globalDebug = window.DEBUG_SETTINGS.debug_global === '1';
    const pageDebug = window.DEBUG_SETTINGS['debug_' + page] === '1';
    
    if (globalDebug || pageDebug) {
        console.log(`[DEBUG - ${page} - RESPONSE]`, response);
    }
}

/**
 * Debug DOM event logging
 * @param {string} eventName - Event name
 * @param {HTMLElement} element - DOM element
 * @param {string} page - Page name
 */
function debugEvent(eventName, element, page) {
    if (typeof window.DEBUG_SETTINGS === 'undefined') {
        return;
    }
    
    const globalDebug = window.DEBUG_SETTINGS.debug_global === '1';
    const pageDebug = window.DEBUG_SETTINGS['debug_' + page] === '1';
    
    if (globalDebug || pageDebug) {
        console.log(`[DEBUG - ${page} - EVENT] ${eventName}`, element);
    }
}

/**
 * Debug performance timer
 * @param {string} label - Timer label
 * @param {Function} callback - Function to measure
 * @param {string} page - Page name
 * @returns {*} Result of callback function
 */
function debugTimer(label, callback, page) {
    if (typeof window.DEBUG_SETTINGS === 'undefined') {
        return callback();
    }
    
    const globalDebug = window.DEBUG_SETTINGS.debug_global === '1';
    const pageDebug = window.DEBUG_SETTINGS['debug_' + page] === '1';
    
    if (globalDebug || pageDebug) {
        const start = performance.now();
        const result = callback();
        const duration = performance.now() - start;
        console.log(`[DEBUG - ${page} - TIMER] ${label}: ${duration.toFixed(2)}ms`);
        return result;
    } else {
        return callback();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    debugLog('Braingonizer loaded.');
    
    // Initialize all Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add animation to filter buttons on click
    const filterButtons = document.querySelectorAll('.filter-btn, .file-filter-btn');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove animation class if it exists
            this.classList.remove('animate__animated', 'animate__rubberBand');
            
            // Trigger reflow to restart animation
            void this.offsetWidth;
            
            // Add animation class
            this.classList.add('animate__animated', 'animate__rubberBand');
            
            // Remove animation class after it completes
            setTimeout(() => {
                this.classList.remove('animate__animated', 'animate__rubberBand');
            }, 500);
        });
    });

    // Add fade-in animation to carousel items when they become active
    const carousels = document.querySelectorAll('.carousel');
    carousels.forEach(carousel => {
        carousel.addEventListener('slide.bs.carousel', function (e) {
            const nextSlide = e.relatedTarget;
            const cards = nextSlide.querySelectorAll('.card');
            
            cards.forEach((card, index) => {
                card.classList.remove('animate__animated', 'animate__fadeIn');
                void card.offsetWidth;
                card.classList.add('animate__animated', 'animate__fadeIn');
                card.style.animationDelay = `${index * 0.1}s`;
            });
        });
    });

    // Add bounce animation to badges on page load
    const badges = document.querySelectorAll('.badge');
    badges.forEach((badge, index) => {
        badge.classList.add('animate__animated', 'animate__fadeIn');
        badge.style.animationDelay = `${index * 0.05}s`;
    });
});
