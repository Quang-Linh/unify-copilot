/**
 * ProActiv - JavaScript amélioré avec interactions avancées
 * Fonctionnalités interactives et animations modernes
 */

class ProActivApp {
    constructor() {
        this.config = {
            animationDuration: 300,
            debounceDelay: 250,
            toastDuration: 4000
        };

        this.state = {
            isLoading: false,
            notifications: [],
            theme: localStorage.getItem('proactiv-theme') || 'light'
        };

        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.onDOMReady();
        });

        // Événements globaux
        this.bindGlobalEvents();
        console.log('🚀 ProActiv App initialisée');
    }

    onDOMReady() {
        // Fonctionnalités existantes
        this.initializeTooltips();
        this.initializeAlerts();
        this.initializeFormValidation();
        this.initializeAjaxForms();
        this.initializeCalculators();
        this.initializeTabs();

        // Nouvelles fonctionnalités
        this.animateOnLoad();
        this.initNotifications();
        this.startRealTimeUpdates();
        this.initLoadingStates();
    }

    bindGlobalEvents() {
        // Gestion des clics avec effets
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn') || e.target.closest('.btn')) {
                this.createRippleEffect(e);
            }
            if (e.target.matches('[data-action]')) {
                this.handleQuickAction(e);
            }
        });

        // Gestion du scroll
        window.addEventListener('scroll', this.debounce(() => {
            this.handleScroll();
        }, 100));

        // Gestion du redimensionnement
        window.addEventListener('resize', this.debounce(() => {
            this.handleResize();
        }, 250));
    }

    // === NOUVELLES FONCTIONNALITÉS ===

    animateOnLoad() {
        // Animation séquentielle des cartes
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });

        this.animateCounters();
    }

    animateCounters() {
        const counters = document.querySelectorAll('[data-counter]');
        
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-counter'));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;

            const updateCounter = () => {
                current += step;
                if (current < target) {
                    counter.textContent = Math.floor(current).toLocaleString();
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target.toLocaleString();
                }
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        updateCounter();
                        observer.unobserve(entry.target);
                    }
                });
            });

            observer.observe(counter);
        });
    }

    createRippleEffect(e) {
        const button = e.target.closest('.btn');
        const rect = button.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;

        const ripple = document.createElement('span');
        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        `;

        button.style.position = 'relative';
        button.style.overflow = 'hidden';
        button.appendChild(ripple);

        setTimeout(() => ripple.remove(), 600);
    }

    handleQuickAction(e) {
        e.preventDefault();
        const action = e.target.getAttribute('data-action');
        const target = e.target;

        this.setLoadingState(target, true);

        switch (action) {
            case 'refresh-stats':
                this.refreshStats(target);
                break;
            case 'quick-calculation':
                this.quickCalculation(target);
                break;
            case 'export-data':
                this.exportData(target);
                break;
        }
    }

    async refreshStats(button) {
        try {
            const response = await fetch('/api/stats', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (response.ok) {
                const data = await response.json();
                this.updateStatsDisplay(data);
                this.showNotification('Statistiques mises à jour', 'success');
            }
        } catch (error) {
            this.showNotification('Erreur lors de la mise à jour', 'error');
        } finally {
            this.setLoadingState(button, false);
        }
    }

    setLoadingState(element, isLoading) {
        if (isLoading) {
            element.classList.add('loading');
            element.disabled = true;
            const icon = element.querySelector('i');
            if (icon) icon.classList.add('fa-spin');
        } else {
            element.classList.remove('loading');
            element.disabled = false;
            const icon = element.querySelector('i');
            if (icon) icon.classList.remove('fa-spin');
        }
    }

    showNotification(message, type = 'info', duration = this.config.toastDuration) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = `
            top: 20px; right: 20px; z-index: 9999; min-width: 300px;
            animation: slideInRight 0.5s ease-out;
        `;

        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;

        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), duration);
        return notification;
    }

    handleScroll() {
        const navbar = document.querySelector('.navbar');
        if (navbar) {
            if (window.scrollY > 50) {
                navbar.style.backgroundColor = 'rgba(13, 110, 253, 0.98)';
            } else {
                navbar.style.backgroundColor = 'rgba(13, 110, 253, 0.95)';
            }
        }
    }

    handleResize() {
        const isMobile = window.innerWidth < 768;
        document.body.classList.toggle('mobile-view', isMobile);
    }

    // === FONCTIONNALITÉS EXISTANTES AMÉLIORÉES ===
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Initialise les alertes avec auto-fermeture
 */
function initializeAlerts() {
    // Auto-fermeture des alertes de succès
    const successAlerts = document.querySelectorAll('.alert-success');
    successAlerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            if (bsAlert) {
                bsAlert.close();
            }
        }, 3000);
    });
}

/**
 * Validation des formulaires côté client
 */
function initializeFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    
    // Validation en temps réel pour les emails
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(function(input) {
        input.addEventListener('blur', function() {
            validateEmail(this);
        });
    });
    
    // Validation des mots de passe
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            validatePassword(this);
        });
    });
}

/**
 * Valide un champ email
 */
function validateEmail(input) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const isValid = emailRegex.test(input.value);
    
    if (input.value && !isValid) {
        input.setCustomValidity('Veuillez saisir une adresse email valide');
        input.classList.add('is-invalid');
    } else {
        input.setCustomValidity('');
        input.classList.remove('is-invalid');
        if (input.value) {
            input.classList.add('is-valid');
        }
    }
}

/**
 * Valide un mot de passe
 */
function validatePassword(input) {
    const password = input.value;
    const minLength = 8;
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    const hasNumbers = /\d/.test(password);
    const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
    
    let strength = 0;
    let message = '';
    
    if (password.length >= minLength) strength++;
    if (hasUpperCase) strength++;
    if (hasLowerCase) strength++;
    if (hasNumbers) strength++;
    if (hasSpecialChar) strength++;
    
    // Indicateur de force du mot de passe
    const strengthIndicator = input.parentElement.querySelector('.password-strength');
    if (strengthIndicator) {
        if (strength < 3) {
            strengthIndicator.className = 'password-strength weak';
            message = 'Mot de passe faible';
        } else if (strength < 5) {
            strengthIndicator.className = 'password-strength medium';
            message = 'Mot de passe moyen';
        } else {
            strengthIndicator.className = 'password-strength strong';
            message = 'Mot de passe fort';
        }
        strengthIndicator.textContent = message;
    }
}

/**
 * Gestion des formulaires AJAX
 */
function initializeAjaxForms() {
    const ajaxForms = document.querySelectorAll('.ajax-form');
    
    ajaxForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitAjaxForm(this);
        });
    });
}

/**
 * Soumission d'un formulaire en AJAX
 */
function submitAjaxForm(form) {
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // État de chargement
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Envoi...';
    submitBtn.disabled = true;
    
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: form.method,
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message || 'Opération réussie');
            if (data.redirect) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            }
            if (data.reset) {
                form.reset();
            }
        } else {
            showAlert('danger', data.message || 'Une erreur est survenue');
            if (data.errors) {
                displayFormErrors(form, data.errors);
            }
        }
    })
    .catch(error => {
        console.error('Erreur AJAX:', error);
        showAlert('danger', 'Erreur de connexion. Veuillez réessayer.');
    })
    .finally(() => {
        // Restauration du bouton
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

/**
 * Affiche une alerte
 */
function showAlert(type, message) {
    const alertContainer = document.getElementById('alert-container') || document.body;
    const alertId = 'alert-' + Date.now();
    
    const alertHTML = `
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    alertContainer.insertAdjacentHTML('afterbegin', alertHTML);
    
    // Auto-suppression après 5 secondes
    setTimeout(() => {
        const alert = document.getElementById(alertId);
        if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 5000);
}

/**
 * Affiche les erreurs de validation
 */
function displayFormErrors(form, errors) {
    // Supprime les anciennes erreurs
    form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    
    // Affiche les nouvelles erreurs
    Object.keys(errors).forEach(fieldName => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = errors[fieldName];
            field.parentElement.appendChild(errorDiv);
        }
    });
}

/**
 * Initialise les calculateurs
 */
function initializeCalculators() {
    // Calculateur de charges sociales
    const chargeCalculator = document.getElementById('charge-calculator');
    if (chargeCalculator) {
        chargeCalculator.addEventListener('input', calculateCharges);
    }
    
    // Calculateur d'impôts
    const taxCalculator = document.getElementById('tax-calculator');
    if (taxCalculator) {
        taxCalculator.addEventListener('input', calculateTax);
    }
}

/**
 * Calcul des charges sociales
 */
function calculateCharges() {
    const revenue = parseFloat(document.getElementById('revenue').value) || 0;
    const activityType = document.getElementById('activity_type').value;
    
    let socialRate = 0;
    switch (activityType) {
        case 'liberal':
            socialRate = 0.22; // 22%
            break;
        case 'commercial':
            socialRate = 0.128; // 12.8%
            break;
        case 'artisanal':
            socialRate = 0.128; // 12.8%
            break;
    }
    
    const socialCharges = revenue * socialRate;
    const netRevenue = revenue - socialCharges;
    
    // Affichage des résultats
    updateCalculatorResult('social-charges', socialCharges);
    updateCalculatorResult('net-revenue', netRevenue);
    updateCalculatorResult('social-rate', (socialRate * 100).toFixed(1) + '%');
}

/**
 * Calcul des impôts
 */
function calculateTax() {
    const revenue = parseFloat(document.getElementById('tax-revenue').value) || 0;
    const activityType = document.getElementById('tax-activity-type').value;
    
    // Abattement forfaitaire
    let abattement = 0;
    switch (activityType) {
        case 'liberal':
            abattement = 0.34; // 34%
            break;
        case 'commercial':
            abattement = 0.71; // 71%
            break;
        case 'artisanal':
            abattement = 0.50; // 50%
            break;
    }
    
    const taxableIncome = revenue * (1 - abattement);
    
    // Calcul de l'impôt (simulation simplifiée)
    let tax = 0;
    if (taxableIncome > 10225) {
        tax = (taxableIncome - 10225) * 0.11; // Tranche 11%
    }
    if (taxableIncome > 26070) {
        tax = (10225 * 0) + (15845 * 0.11) + ((taxableIncome - 26070) * 0.30);
    }
    
    updateCalculatorResult('taxable-income', taxableIncome);
    updateCalculatorResult('estimated-tax', tax);
}

/**
 * Met à jour un résultat de calculateur
 */
function updateCalculatorResult(elementId, value) {
    const element = document.getElementById(elementId);
    if (element) {
        if (typeof value === 'number') {
            element.textContent = formatCurrency(value);
        } else {
            element.textContent = value;
        }
    }
}

/**
 * Formate un montant en euros
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(amount);
}

/**
 * Copie du texte dans le presse-papiers
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showAlert('success', 'Copié dans le presse-papiers');
    }, function() {
        showAlert('warning', 'Impossible de copier automatiquement');
    });
}

/**
 * Confirmation avant suppression
 */
function confirmDelete(message = 'Êtes-vous sûr de vouloir supprimer cet élément ?') {
    return confirm(message);
}

/**
 * Fonction utilitaire pour débouncer les événements
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Gestion des onglets avec mémorisation
 */
function initializeTabs() {
    const tabElements = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabElements.forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function(e) {
            localStorage.setItem('activeTab', e.target.getAttribute('href'));
        });
    });
    
    // Restauration de l'onglet actif
    const activeTab = localStorage.getItem('activeTab');
    if (activeTab) {
        const tab = document.querySelector(`[href="${activeTab}"]`);
        if (tab) {
            new bootstrap.Tab(tab).show();
        }
    }
}

// Initialisation des onglets au chargement
document.addEventListener('DOMContentLoaded', initializeTabs);
