<?php $lang = $lang ?? new LanguageService(); ?>

<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/ProActiv/superadmin">Super Admin</a></li>
                            <li class="breadcrumb-item active">Paramètres</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-cogs text-primary me-2"></i>
                        Paramètres système
                    </h1>
                    <p class="text-muted mb-0">Configuration générale de la plateforme ProActiv</p>
                </div>
                <div class="btn-group">
                    <button class="btn btn-outline-secondary" onclick="resetToDefaults()">
                        <i class="fas fa-undo me-1"></i>Réinitialiser
                    </button>
                    <button class="btn btn-success" onclick="saveAllSettings()">
                        <i class="fas fa-save me-1"></i>Sauvegarder tout
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Navigation des paramètres -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="#general" class="list-group-item list-group-item-action active" data-tab="general">
                            <i class="fas fa-cog me-2"></i>Général
                        </a>
                        <a href="#security" class="list-group-item list-group-item-action" data-tab="security">
                            <i class="fas fa-shield-alt me-2"></i>Sécurité
                        </a>
                        <a href="#email" class="list-group-item list-group-item-action" data-tab="email">
                            <i class="fas fa-envelope me-2"></i>Email
                        </a>
                        <a href="#payment" class="list-group-item list-group-item-action" data-tab="payment">
                            <i class="fas fa-credit-card me-2"></i>Paiements
                        </a>
                        <a href="#features" class="list-group-item list-group-item-action" data-tab="features">
                            <i class="fas fa-puzzle-piece me-2"></i>Fonctionnalités
                        </a>
                        <a href="#maintenance" class="list-group-item list-group-item-action" data-tab="maintenance">
                            <i class="fas fa-tools me-2"></i>Maintenance
                        </a>
                        <a href="#backup" class="list-group-item list-group-item-action" data-tab="backup">
                            <i class="fas fa-database me-2"></i>Sauvegarde
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenu des paramètres -->
        <div class="col-md-9">
            
            <!-- Paramètres généraux -->
            <div class="tab-content" id="general">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog text-primary me-2"></i>
                            Paramètres généraux
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="generalForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="site_name">Nom du site</label>
                                        <input type="text" class="form-control" id="site_name" name="site_name" 
                                               value="<?= $settings['site_name'] ?? 'ProActiv' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="site_url">URL du site</label>
                                        <input type="url" class="form-control" id="site_url" name="site_url" 
                                               value="<?= $settings['site_url'] ?? 'https://proactiv.example.com' ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="site_description">Description du site</label>
                                <textarea class="form-control" id="site_description" name="site_description" rows="3"><?= $settings['site_description'] ?? 'Plateforme pour auto-entrepreneurs' ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="default_language">Langue par défaut</label>
                                        <select class="form-control" id="default_language" name="default_language">
                                            <option value="fr" <?= ($settings['default_language'] ?? 'fr') === 'fr' ? 'selected' : '' ?>>Français</option>
                                            <option value="en" <?= ($settings['default_language'] ?? 'fr') === 'en' ? 'selected' : '' ?>>English</option>
                                            <option value="es" <?= ($settings['default_language'] ?? 'fr') === 'es' ? 'selected' : '' ?>>Español</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="default_timezone">Fuseau horaire</label>
                                        <select class="form-control" id="default_timezone" name="default_timezone">
                                            <option value="Europe/Paris" <?= ($settings['default_timezone'] ?? 'Europe/Paris') === 'Europe/Paris' ? 'selected' : '' ?>>Europe/Paris</option>
                                            <option value="Europe/Brussels" <?= ($settings['default_timezone'] ?? 'Europe/Paris') === 'Europe/Brussels' ? 'selected' : '' ?>>Europe/Brussels</option>
                                            <option value="Europe/Zurich" <?= ($settings['default_timezone'] ?? 'Europe/Paris') === 'Europe/Zurich' ? 'selected' : '' ?>>Europe/Zurich</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="registration_enabled">Inscription ouverte</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="registration_enabled" name="registration_enabled" 
                                                   <?= ($settings['registration_enabled'] ?? true) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="registration_enabled">
                                                Autoriser les nouvelles inscriptions
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="maintenance_mode">Mode maintenance</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" 
                                                   <?= ($settings['maintenance_mode'] ?? false) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="maintenance_mode">
                                                Activer le mode maintenance
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Sauvegarder
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Paramètres de sécurité -->
            <div class="tab-content d-none" id="security">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-shield-alt text-danger me-2"></i>
                            Paramètres de sécurité
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="securityForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="session_timeout">Timeout de session (minutes)</label>
                                        <input type="number" class="form-control" id="session_timeout" name="session_timeout" 
                                               value="<?= $settings['session_timeout'] ?? 60 ?>" min="5" max="1440">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="max_login_attempts">Tentatives de connexion max</label>
                                        <input type="number" class="form-control" id="max_login_attempts" name="max_login_attempts" 
                                               value="<?= $settings['max_login_attempts'] ?? 5 ?>" min="3" max="10">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="password_min_length">Longueur minimale mot de passe</label>
                                        <input type="number" class="form-control" id="password_min_length" name="password_min_length" 
                                               value="<?= $settings['password_min_length'] ?? 8 ?>" min="6" max="20">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="password_complexity">Complexité mot de passe</label>
                                        <select class="form-control" id="password_complexity" name="password_complexity">
                                            <option value="low" <?= ($settings['password_complexity'] ?? 'medium') === 'low' ? 'selected' : '' ?>>Faible</option>
                                            <option value="medium" <?= ($settings['password_complexity'] ?? 'medium') === 'medium' ? 'selected' : '' ?>>Moyenne</option>
                                            <option value="high" <?= ($settings['password_complexity'] ?? 'medium') === 'high' ? 'selected' : '' ?>>Élevée</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label>Options de sécurité</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="two_factor_enabled" name="two_factor_enabled" 
                                           <?= ($settings['two_factor_enabled'] ?? false) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="two_factor_enabled">
                                        Authentification à deux facteurs (2FA)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="email_verification" name="email_verification" 
                                           <?= ($settings['email_verification'] ?? true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="email_verification">
                                        Vérification email obligatoire
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="captcha_enabled" name="captcha_enabled" 
                                           <?= ($settings['captcha_enabled'] ?? false) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="captcha_enabled">
                                        CAPTCHA sur les formulaires
                                    </label>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-shield-alt me-1"></i>Sauvegarder
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Paramètres email -->
            <div class="tab-content d-none" id="email">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-envelope text-info me-2"></i>
                            Configuration email
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="emailForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="smtp_host">Serveur SMTP</label>
                                        <input type="text" class="form-control" id="smtp_host" name="smtp_host" 
                                               value="<?= $settings['smtp_host'] ?? 'smtp.gmail.com' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="smtp_port">Port SMTP</label>
                                        <input type="number" class="form-control" id="smtp_port" name="smtp_port" 
                                               value="<?= $settings['smtp_port'] ?? 587 ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="smtp_username">Nom d'utilisateur SMTP</label>
                                        <input type="text" class="form-control" id="smtp_username" name="smtp_username" 
                                               value="<?= $settings['smtp_username'] ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="smtp_password">Mot de passe SMTP</label>
                                        <input type="password" class="form-control" id="smtp_password" name="smtp_password" 
                                               value="<?= $settings['smtp_password'] ?? '' ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="from_email">Email expéditeur</label>
                                        <input type="email" class="form-control" id="from_email" name="from_email" 
                                               value="<?= $settings['from_email'] ?? 'noreply@proactiv.com' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="from_name">Nom expéditeur</label>
                                        <input type="text" class="form-control" id="from_name" name="from_name" 
                                               value="<?= $settings['from_name'] ?? 'ProActiv' ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label>Options email</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="smtp_encryption" name="smtp_encryption" 
                                           <?= ($settings['smtp_encryption'] ?? true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="smtp_encryption">
                                        Chiffrement TLS/SSL
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" 
                                           <?= ($settings['email_notifications'] ?? true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="email_notifications">
                                        Notifications par email
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-save me-1"></i>Sauvegarder
                                </button>
                                <button type="button" class="btn btn-outline-info" onclick="testEmail()">
                                    <i class="fas fa-paper-plane me-1"></i>Tester
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Paramètres de paiement -->
            <div class="tab-content d-none" id="payment">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-credit-card text-success me-2"></i>
                            Configuration des paiements
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="paymentForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="stripe_public_key">Clé publique Stripe</label>
                                        <input type="text" class="form-control" id="stripe_public_key" name="stripe_public_key" 
                                               value="<?= $settings['stripe_public_key'] ?? '' ?>" placeholder="pk_...">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="stripe_secret_key">Clé secrète Stripe</label>
                                        <input type="password" class="form-control" id="stripe_secret_key" name="stripe_secret_key" 
                                               value="<?= $settings['stripe_secret_key'] ?? '' ?>" placeholder="sk_...">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="paypal_client_id">PayPal Client ID</label>
                                        <input type="text" class="form-control" id="paypal_client_id" name="paypal_client_id" 
                                               value="<?= $settings['paypal_client_id'] ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="paypal_secret">PayPal Secret</label>
                                        <input type="password" class="form-control" id="paypal_secret" name="paypal_secret" 
                                               value="<?= $settings['paypal_secret'] ?? '' ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="default_currency">Devise par défaut</label>
                                        <select class="form-control" id="default_currency" name="default_currency">
                                            <option value="EUR" <?= ($settings['default_currency'] ?? 'EUR') === 'EUR' ? 'selected' : '' ?>>EUR (€)</option>
                                            <option value="USD" <?= ($settings['default_currency'] ?? 'EUR') === 'USD' ? 'selected' : '' ?>>USD ($)</option>
                                            <option value="CHF" <?= ($settings['default_currency'] ?? 'EUR') === 'CHF' ? 'selected' : '' ?>>CHF</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="tax_rate">Taux de TVA (%)</label>
                                        <input type="number" class="form-control" id="tax_rate" name="tax_rate" 
                                               value="<?= $settings['tax_rate'] ?? 20 ?>" min="0" max="30" step="0.1">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label>Options de paiement</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="stripe_enabled" name="stripe_enabled" 
                                           <?= ($settings['stripe_enabled'] ?? true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="stripe_enabled">
                                        Activer Stripe
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="paypal_enabled" name="paypal_enabled" 
                                           <?= ($settings['paypal_enabled'] ?? true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="paypal_enabled">
                                        Activer PayPal
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="test_mode" name="test_mode" 
                                           <?= ($settings['test_mode'] ?? true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="test_mode">
                                        Mode test (sandbox)
                                    </label>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-credit-card me-1"></i>Sauvegarder
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Gestion des fonctionnalités -->
            <div class="tab-content d-none" id="features">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-puzzle-piece text-warning me-2"></i>
                            Gestion des fonctionnalités
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="featuresForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Calculateurs</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="calculator_charges" name="calculator_charges" 
                                               <?= ($settings['calculator_charges'] ?? true) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="calculator_charges">
                                            Calculateur charges sociales
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="calculator_taxes" name="calculator_taxes" 
                                               <?= ($settings['calculator_taxes'] ?? true) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="calculator_taxes">
                                            Calculateur impôts
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="calculator_revenue" name="calculator_revenue" 
                                               <?= ($settings['calculator_revenue'] ?? true) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="calculator_revenue">
                                            Calculateur revenus
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6>Autres fonctionnalités</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="forum_enabled" name="forum_enabled" 
                                               <?= ($settings['forum_enabled'] ?? true) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="forum_enabled">
                                            Forum communautaire
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="documents_enabled" name="documents_enabled" 
                                               <?= ($settings['documents_enabled'] ?? true) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="documents_enabled">
                                            Génération de documents
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="comments_enabled" name="comments_enabled" 
                                               <?= ($settings['comments_enabled'] ?? true) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="comments_enabled">
                                            Système de commentaires
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="max_file_size">Taille max fichiers (MB)</label>
                                        <input type="number" class="form-control" id="max_file_size" name="max_file_size" 
                                               value="<?= $settings['max_file_size'] ?? 10 ?>" min="1" max="100">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="api_rate_limit">Limite API (req/min)</label>
                                        <input type="number" class="form-control" id="api_rate_limit" name="api_rate_limit" 
                                               value="<?= $settings['api_rate_limit'] ?? 60 ?>" min="10" max="1000">
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-puzzle-piece me-1"></i>Sauvegarder
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Maintenance -->
            <div class="tab-content d-none" id="maintenance">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-tools text-secondary me-2"></i>
                            Maintenance système
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="maintenance-action">
                                    <h6><i class="fas fa-broom me-2"></i>Nettoyage</h6>
                                    <p class="text-muted">Nettoyer les fichiers temporaires et les logs anciens</p>
                                    <button class="btn btn-outline-secondary" onclick="cleanupSystem()">
                                        <i class="fas fa-broom me-1"></i>Nettoyer
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="maintenance-action">
                                    <h6><i class="fas fa-sync me-2"></i>Cache</h6>
                                    <p class="text-muted">Vider le cache de l'application</p>
                                    <button class="btn btn-outline-warning" onclick="clearCache()">
                                        <i class="fas fa-sync me-1"></i>Vider le cache
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="maintenance-action">
                                    <h6><i class="fas fa-database me-2"></i>Base de données</h6>
                                    <p class="text-muted">Optimiser les tables de la base de données</p>
                                    <button class="btn btn-outline-info" onclick="optimizeDatabase()">
                                        <i class="fas fa-database me-1"></i>Optimiser
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="maintenance-action">
                                    <h6><i class="fas fa-chart-line me-2"></i>Statistiques</h6>
                                    <p class="text-muted">Recalculer les statistiques</p>
                                    <button class="btn btn-outline-primary" onclick="recalculateStats()">
                                        <i class="fas fa-chart-line me-1"></i>Recalculer
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Mode maintenance</h6>
                            <p class="mb-3">Activer le mode maintenance pour effectuer des opérations critiques.</p>
                            <button class="btn btn-warning" onclick="toggleMaintenanceMode()">
                                <i class="fas fa-tools me-1"></i>Activer la maintenance
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sauvegarde -->
            <div class="tab-content d-none" id="backup">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-database text-info me-2"></i>
                            Sauvegarde et restauration
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Créer une sauvegarde</h6>
                                <p class="text-muted">Sauvegarder la base de données et les fichiers</p>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="backup_database" checked>
                                    <label class="form-check-label" for="backup_database">
                                        Base de données
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="backup_files" checked>
                                    <label class="form-check-label" for="backup_files">
                                        Fichiers utilisateurs
                                    </label>
                                </div>
                                <button class="btn btn-primary" onclick="createBackup()">
                                    <i class="fas fa-download me-1"></i>Créer une sauvegarde
                                </button>
                            </div>
                            
                            <div class="col-md-6">
                                <h6>Sauvegardes existantes</h6>
                                <div class="backup-list">
                                    <div class="backup-item d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <strong>backup_2024_09_05.sql</strong><br>
                                            <small class="text-muted">5 septembre 2024 - 2.3 MB</small>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" onclick="downloadBackup('backup_2024_09_05.sql')">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="deleteBackup('backup_2024_09_05.sql')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="backup-item d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <strong>backup_2024_09_01.sql</strong><br>
                                            <small class="text-muted">1 septembre 2024 - 2.1 MB</small>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" onclick="downloadBackup('backup_2024_09_01.sql')">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="deleteBackup('backup_2024_09_01.sql')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Sauvegarde automatique</h6>
                            <p class="mb-3">Configurer les sauvegardes automatiques quotidiennes.</p>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="auto_backup" 
                                       <?= ($settings['auto_backup'] ?? false) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="auto_backup">
                                    Activer les sauvegardes automatiques (quotidiennes à 2h00)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.maintenance-action {
    padding: 1.5rem;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    margin-bottom: 1rem;
    text-align: center;
}

.backup-item {
    padding: 1rem;
    border: 1px solid #e9ecef;
    border-radius: 8px;
}

.tab-content {
    transition: opacity 0.3s ease;
}

.list-group-item.active {
    background-color: #007bff;
    border-color: #007bff;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des onglets
    document.querySelectorAll('[data-tab]').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Désactiver tous les onglets
            document.querySelectorAll('[data-tab]').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.add('d-none'));
            
            // Activer l'onglet cliqué
            this.classList.add('active');
            document.getElementById(this.dataset.tab).classList.remove('d-none');
        });
    });
    
    // Gestion des formulaires
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            saveSettings(this);
        });
    });
});

function saveSettings(form) {
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    // Simulation de sauvegarde
    console.log('Sauvegarde des paramètres:', data);
    
    // En production, envoyer les données au serveur
    // fetch('/ProActiv/superadmin/settings/save', {
    //     method: 'POST',
    //     body: formData
    // }).then(response => response.json())
    //   .then(data => {
    //       if (data.success) {
    //           showAlert('success', 'Paramètres sauvegardés avec succès');
    //       } else {
    //           showAlert('danger', 'Erreur lors de la sauvegarde');
    //       }
    //   });
    
    showAlert('success', 'Paramètres sauvegardés avec succès');
}

function saveAllSettings() {
    // Sauvegarder tous les formulaires
    document.querySelectorAll('form').forEach(form => {
        if (!form.closest('.d-none')) {
            saveSettings(form);
        }
    });
}

function resetToDefaults() {
    if (confirm('Êtes-vous sûr de vouloir réinitialiser tous les paramètres ?')) {
        location.reload();
    }
}

function testEmail() {
    showAlert('info', 'Test d\'email en cours...');
    
    // Simulation de test email
    setTimeout(() => {
        showAlert('success', 'Email de test envoyé avec succès !');
    }, 2000);
}

function cleanupSystem() {
    showAlert('info', 'Nettoyage en cours...');
    setTimeout(() => {
        showAlert('success', 'Nettoyage terminé - 150 MB libérés');
    }, 3000);
}

function clearCache() {
    showAlert('info', 'Vidage du cache en cours...');
    setTimeout(() => {
        showAlert('success', 'Cache vidé avec succès');
    }, 1500);
}

function optimizeDatabase() {
    showAlert('info', 'Optimisation de la base de données...');
    setTimeout(() => {
        showAlert('success', 'Base de données optimisée');
    }, 4000);
}

function recalculateStats() {
    showAlert('info', 'Recalcul des statistiques...');
    setTimeout(() => {
        showAlert('success', 'Statistiques recalculées');
    }, 2500);
}

function toggleMaintenanceMode() {
    const isActive = document.getElementById('maintenance_mode').checked;
    
    if (!isActive) {
        if (confirm('Activer le mode maintenance ? Les utilisateurs ne pourront plus accéder au site.')) {
            document.getElementById('maintenance_mode').checked = true;
            showAlert('warning', 'Mode maintenance activé');
        }
    } else {
        document.getElementById('maintenance_mode').checked = false;
        showAlert('success', 'Mode maintenance désactivé');
    }
}

function createBackup() {
    const includeDB = document.getElementById('backup_database').checked;
    const includeFiles = document.getElementById('backup_files').checked;
    
    showAlert('info', 'Création de la sauvegarde en cours...');
    
    setTimeout(() => {
        showAlert('success', 'Sauvegarde créée avec succès');
    }, 5000);
}

function downloadBackup(filename) {
    showAlert('info', 'Téléchargement de ' + filename + '...');
}

function deleteBackup(filename) {
    if (confirm('Supprimer la sauvegarde ' + filename + ' ?')) {
        showAlert('success', 'Sauvegarde supprimée');
    }
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);
}
</script>

