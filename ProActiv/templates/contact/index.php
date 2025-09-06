<?php
// Capture du contenu dans une variable
ob_start();
?>

<!-- Hero Section -->
<section class="hero-section bg-gradient text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">Nous contacter</h1>
                <p class="lead mb-4">
                    Une question ? Un problème ? Notre équipe est là pour vous aider.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                
                <!-- Messages de feedback -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= htmlspecialchars($_SESSION['success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= htmlspecialchars($_SESSION['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <div class="row">
                    <!-- Formulaire de contact -->
                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h4 class="mb-0">
                                    <i class="fas fa-envelope me-2"></i>Envoyez-nous un message
                                </h4>
                            </div>
                            <div class="card-body p-4">
                                <form action="/contact/send" method="POST">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Nom complet *</label>
                                            <input type="text" class="form-control" id="name" name="name" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email *</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="subject" class="form-label">Sujet</label>
                                        <select class="form-select" id="subject" name="subject">
                                            <option value="support">Support technique</option>
                                            <option value="billing">Facturation</option>
                                            <option value="feature">Demande de fonctionnalité</option>
                                            <option value="bug">Signaler un bug</option>
                                            <option value="other">Autre</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="message" class="form-label">Message *</label>
                                        <textarea class="form-control" id="message" name="message" rows="6" required 
                                                  placeholder="Décrivez votre demande en détail..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i>Envoyer le message
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informations de contact -->
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Informations
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <h6><i class="fas fa-headset text-primary me-2"></i>Support technique</h6>
                                    <p class="mb-2">
                                        <a href="mailto:support@linhstudio.click" class="text-decoration-none">
                                            support@linhstudio.click
                                        </a>
                                    </p>
                                    <p class="small text-muted">Réponse sous 24h en moyenne</p>
                                </div>
                                
                                <div class="mb-4">
                                    <h6><i class="fas fa-clock text-success me-2"></i>Horaires</h6>
                                    <p class="mb-1">Lundi - Vendredi</p>
                                    <p class="mb-1">9h00 - 18h00 (CET)</p>
                                    <p class="small text-muted">Support par email uniquement</p>
                                </div>
                                
                                <div class="mb-4">
                                    <h6><i class="fas fa-language text-info me-2"></i>Langues</h6>
                                    <p class="mb-1">🇫🇷 Français</p>
                                    <p class="mb-1">🇬🇧 English</p>
                                    <p class="mb-1">🇪🇸 Español</p>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-lightbulb me-2"></i>
                                    <strong>Astuce :</strong> Consultez d'abord notre forum communautaire, 
                                    vous y trouverez peut-être déjà la réponse à votre question !
                                    <a href="/forum" class="alert-link">Accéder au forum</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="text-center mb-5">Questions fréquentes</h2>
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                ProActiv est-il gratuit ?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Oui, ProActiv est entièrement gratuit pour tous les auto-entrepreneurs. 
                                Nous croyons que les outils de gestion doivent être accessibles à tous.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Les calculs sont-ils conformes à la législation 2025 ?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Absolument ! Nos calculateurs sont mis à jour régulièrement pour respecter 
                                les dernières évolutions de la législation française concernant les auto-entrepreneurs.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Mes données sont-elles sécurisées ?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Oui, nous respectons le RGPD et utilisons des protocoles de sécurité avancés. 
                                Vos données personnelles ne sont jamais partagées avec des tiers.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Comment signaler un bug ou proposer une amélioration ?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Utilisez le formulaire ci-dessus en sélectionnant "Signaler un bug" ou 
                                "Demande de fonctionnalité". Nous étudions toutes les suggestions avec attention.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
include PROACTIV_ROOT . '/templates/layouts/main.php';
?>

