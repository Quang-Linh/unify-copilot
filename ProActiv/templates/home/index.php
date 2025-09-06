<?php
// Contenu de la page d'accueil
?>

<!-- Hero Section -->
<section class="hero-section bg-gradient text-white py-5">
    <div class="container">
        <div class="row align-items-center min-vh-75">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">
                    Simplifiez votre gestion d'auto-entrepreneur
                </h1>
                <p class="lead mb-4">
                    ProActiv vous accompagne dans tous les aspects de votre activité : calculs de charges, 
                    génération de documents, suivi de revenus et entraide communautaire.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <?php if (!isset($user) || !$user): ?>
                        <a href="/ProActiv/register" class="btn btn-light btn-lg px-4">
                            <i class="fas fa-rocket me-2"></i>Commencer gratuitement
                        </a>
                        <a href="/ProActiv/about" class="btn btn-outline-light btn-lg px-4">
                            <i class="fas fa-info-circle me-2"></i>En savoir plus
                        </a>
                    <?php else: ?>
                        <a href="/ProActiv/dashboard" class="btn btn-light btn-lg px-4">
                            <i class="fas fa-tachometer-alt me-2"></i>Mon tableau de bord
                        </a>
                        <a href="/ProActiv/calculators" class="btn btn-outline-light btn-lg px-4">
                            <i class="fas fa-calculator me-2"></i>Calculateurs
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-image">
                    <i class="fas fa-chart-line" style="font-size: 15rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistiques -->
<section class="stats-section py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <i class="fas fa-users text-primary mb-3" style="font-size: 3rem;"></i>
                    <h3 class="fw-bold"><?= number_format($stats['users_count'] ?? 1250) ?></h3>
                    <p class="text-muted">Auto-entrepreneurs inscrits</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <i class="fas fa-file-alt text-success mb-3" style="font-size: 3rem;"></i>
                    <h3 class="fw-bold"><?= number_format($stats['documents_count'] ?? 3420) ?></h3>
                    <p class="text-muted">Documents générés</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <i class="fas fa-calculator text-warning mb-3" style="font-size: 3rem;"></i>
                    <h3 class="fw-bold"><?= $stats['calculators_used'] ?? 8 ?></h3>
                    <p class="text-muted">Calculateurs disponibles</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <i class="fas fa-comments text-info mb-3" style="font-size: 3rem;"></i>
                    <h3 class="fw-bold"><?= number_format($stats['forum_posts_count'] ?? 890) ?></h3>
                    <p class="text-muted">Messages d'entraide</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Fonctionnalités principales -->
<section class="features-section py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold mb-4">Tout ce dont vous avez besoin</h2>
                <p class="lead">ProActiv centralise tous vos outils de gestion auto-entrepreneur en une seule plateforme intuitive.</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="feature-card h-100 p-4 text-center border rounded-3 shadow-sm">
                    <i class="fas fa-calculator text-primary mb-3" style="font-size: 3rem;"></i>
                    <h4>Calculateurs intelligents</h4>
                    <p>Calculez vos charges sociales, impôts et revenus nets en temps réel selon votre activité et vos revenus.</p>
                    <a href="/ProActiv/calculateurs" class="btn btn-outline-primary">Découvrir</a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="feature-card h-100 p-4 text-center border rounded-3 shadow-sm">
                    <i class="fas fa-file-pdf text-success mb-3" style="font-size: 3rem;"></i>
                    <h4>Génération de documents</h4>
                    <p>Créez facilement vos factures, devis et attestations avec nos modèles professionnels personnalisables.</p>
                    <a href="/ProActiv/documents" class="btn btn-outline-success">Créer un document</a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="feature-card h-100 p-4 text-center border rounded-3 shadow-sm">
                    <i class="fas fa-users text-info mb-3" style="font-size: 3rem;"></i>
                    <h4>Communauté active</h4>
                    <p>Échangez avec d'autres auto-entrepreneurs, posez vos questions et partagez vos expériences.</p>
                    <a href="/ProActiv/forum" class="btn btn-outline-info">Rejoindre</a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="feature-card h-100 p-4 text-center border rounded-3 shadow-sm">
                    <i class="fas fa-chart-line text-warning mb-3" style="font-size: 3rem;"></i>
                    <h4>Suivi d'activité</h4>
                    <p>Analysez vos performances, suivez vos revenus et anticipez vos échéances fiscales et sociales.</p>
                    <a href="/ProActiv/dashboard" class="btn btn-outline-warning">Voir mes stats</a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="feature-card h-100 p-4 text-center border rounded-3 shadow-sm">
                    <i class="fas fa-shield-alt text-danger mb-3" style="font-size: 3rem;"></i>
                    <h4>Sécurisé & conforme</h4>
                    <p>Vos données sont protégées et nos calculs respectent la législation française en vigueur.</p>
                    <a href="/ProActiv/about" class="btn btn-outline-danger">En savoir plus</a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="feature-card h-100 p-4 text-center border rounded-3 shadow-sm">
                    <i class="fas fa-mobile-alt text-purple mb-3" style="font-size: 3rem;"></i>
                    <h4>Accessible partout</h4>
                    <p>Interface responsive adaptée à tous vos appareils : ordinateur, tablette et smartphone.</p>
                    <a href="/ProActiv/calculators" class="btn btn-outline-secondary">Tester</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Témoignages -->
<?php if (isset($testimonials) && !empty($testimonials)): ?>
<section class="testimonials-section py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold mb-4">Ils nous font confiance</h2>
                <p class="lead">Découvrez les témoignages de nos utilisateurs auto-entrepreneurs.</p>
            </div>
        </div>
        
        <div class="row">
            <?php 
            $default_testimonials = [
                ['name' => 'Sophie Martin', 'business' => 'Graphiste freelance', 'content' => 'ProActiv m\'a fait gagner un temps précieux dans la gestion de mon activité. Les calculateurs sont très précis !', 'rating' => 5],
                ['name' => 'Thomas Dubois', 'business' => 'Consultant IT', 'content' => 'Interface claire et fonctionnalités complètes. Je recommande vivement cette plateforme.', 'rating' => 5],
                ['name' => 'Marie Leroy', 'business' => 'Coach bien-être', 'content' => 'La communauté est très active et les conseils sont précieux pour débuter en auto-entrepreneur.', 'rating' => 4]
            ];
            $testimonials_to_show = $testimonials ?? $default_testimonials;
            foreach ($testimonials_to_show as $testimonial): ?>
            <div class="col-lg-4 mb-4">
                <div class="testimonial-card bg-white p-4 rounded-3 shadow-sm h-100">
                    <div class="stars mb-3">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?= $i <= ($testimonial['rating'] ?? 5) ? 'text-warning' : 'text-muted' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="mb-4">"<?= htmlspecialchars($testimonial['content'] ?? $testimonial['text'] ?? 'Excellent service !') ?>"</p>
                    <div class="author">
                        <strong><?= htmlspecialchars($testimonial['name'] ?? 'Utilisateur') ?></strong><br>
                        <small class="text-muted"><?= htmlspecialchars($testimonial['business'] ?? $testimonial['profession'] ?? 'Auto-entrepreneur') ?></small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Activité récente du forum -->
<?php if (isset($latest_topics) && !empty($latest_topics)): ?>
<section class="forum-activity py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="text-center mb-5">Activité récente de la communauté</h2>
                <div class="list-group">
                    <?php foreach ($latest_topics as $topic): ?>
                    <a href="/ProActiv/forum/topic/<?= $topic['id'] ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1"><?= htmlspecialchars($topic['title']) ?></h6>
                            <small><?= date('d/m/Y H:i', strtotime($topic['created_at'])) ?></small>
                        </div>
                        <p class="mb-1">
                            <small class="text-muted">
                                Par <?= htmlspecialchars(($topic['firstname'] ?? '') . ' ' . ($topic['lastname'] ?? $topic['author'] ?? 'Utilisateur')) ?> 
                                dans <?= htmlspecialchars($topic['category_name'] ?? 'Forum') ?>
                            </small>
                        </p>
                    </a>
                    <?php endforeach; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="/ProActiv/forum" class="btn btn-primary">Voir tout le forum</a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Témoignages clients -->
<section class="testimonials-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <h2 class="text-center mb-5">Ce que disent nos utilisateurs</h2>
                <?php
                // Inclusion du widget des commentaires
                $commentController = new CommentController($config ?? []);
                $commentController->widget();
                ?>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<?php if (!isset($user) || !$user): ?>
<section class="cta-section py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="display-5 fw-bold mb-4">Prêt à simplifier votre gestion ?</h2>
        <p class="lead mb-4">Rejoignez des milliers d'auto-entrepreneurs qui font confiance à ProActiv.</p>
        <a href="/ProActiv/register" class="btn btn-light btn-lg">
            <i class="fas fa-user-plus me-2"></i>Créer mon compte gratuit
        </a>
    </div>
</section>
<?php endif; ?>

