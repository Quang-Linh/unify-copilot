<?php $lang = $lang ?? new LanguageService(); ?>
<div class="welcome-page">
    <!-- En-tête de bienvenue -->
    <div class="welcome-header">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <div class="welcome-animation">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h1 class="welcome-title">
                        <?= $lang->get('welcome_to_proactiv', 'Bienvenue sur ProActiv !') ?>
                    </h1>
                    <p class="welcome-subtitle">
                        <?= sprintf($lang->get('welcome_message', 'Félicitations %s, votre compte a été créé avec succès !'), htmlspecialchars($user_name)) ?>
                    </p>
                    
                    <!-- Indicateur d'étapes -->
                    <div class="steps-indicator">
                        <div class="step completed">
                            <span class="step-number"><i class="fas fa-check"></i></span>
                            <span class="step-label"><?= $lang->get('account_info', 'Informations') ?></span>
                        </div>
                        <div class="step completed">
                            <span class="step-number"><i class="fas fa-check"></i></span>
                            <span class="step-label"><?= $lang->get('payment', 'Paiement') ?></span>
                        </div>
                        <div class="step completed">
                            <span class="step-number"><i class="fas fa-check"></i></span>
                            <span class="step-label"><?= $lang->get('welcome', 'Bienvenue') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <!-- Confirmation du plan -->
                <div class="plan-confirmation mb-5">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="plan-info">
                                <?php if ($user_plan === 'standard'): ?>
                                    <div class="plan-badge standard">
                                        <i class="fas fa-crown"></i>
                                        <?= $lang->get('standard_plan', 'Plan Standard') ?>
                                    </div>
                                    <h3><?= $lang->get('standard_activated', 'Votre abonnement Standard est maintenant actif !') ?></h3>
                                    <p><?= $lang->get('standard_benefits', 'Vous avez accès à toutes les fonctionnalités premium de ProActiv.') ?></p>
                                    
                                    <?php if ($payment_success): ?>
                                        <div class="payment-confirmation">
                                            <i class="fas fa-check-circle text-success"></i>
                                            <span><?= $lang->get('payment_confirmed', 'Paiement confirmé - Merci !') ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                <?php else: ?>
                                    <div class="plan-badge community">
                                        <i class="fas fa-users"></i>
                                        <?= $lang->get('community_plan', 'Plan Communautaire') ?>
                                    </div>
                                    <h3><?= $lang->get('community_activated', 'Votre essai gratuit de 30 jours a commencé !') ?></h3>
                                    <p><?= $lang->get('community_benefits', 'Découvrez ProActiv gratuitement pendant 30 jours.') ?></p>
                                    
                                    <div class="upgrade-notice">
                                        <i class="fas fa-info-circle"></i>
                                        <span><?= $lang->get('upgrade_anytime', 'Vous pourrez passer au plan Standard à tout moment depuis votre tableau de bord.') ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="plan-visual">
                                <?php if ($user_plan === 'standard'): ?>
                                    <i class="fas fa-crown fa-5x text-warning"></i>
                                <?php else: ?>
                                    <i class="fas fa-users fa-5x text-primary"></i>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Prochaines étapes -->
                <div class="next-steps">
                    <h2 class="text-center mb-4"><?= $lang->get('next_steps', 'Vos prochaines étapes') ?></h2>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="step-card">
                                <div class="step-icon">
                                    <i class="fas fa-user-cog"></i>
                                </div>
                                <h4><?= $lang->get('complete_profile', 'Complétez votre profil') ?></h4>
                                <p><?= $lang->get('profile_description', 'Ajoutez vos informations professionnelles pour une expérience personnalisée.') ?></p>
                                <a href="/ProActiv/profile" class="btn btn-outline-primary">
                                    <?= $lang->get('complete_profile', 'Compléter') ?>
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="step-card">
                                <div class="step-icon">
                                    <i class="fas fa-calculator"></i>
                                </div>
                                <h4><?= $lang->get('try_calculators', 'Testez les calculateurs') ?></h4>
                                <p><?= $lang->get('calculators_description', 'Calculez vos charges sociales et optimisez vos revenus.') ?></p>
                                <a href="/ProActiv/calculators" class="btn btn-outline-primary">
                                    <?= $lang->get('start_calculating', 'Commencer') ?>
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="step-card">
                                <div class="step-icon">
                                    <i class="fas fa-comments"></i>
                                </div>
                                <h4><?= $lang->get('join_community', 'Rejoignez la communauté') ?></h4>
                                <p><?= $lang->get('community_description', 'Échangez avec d\'autres auto-entrepreneurs sur le forum.') ?></p>
                                <a href="/ProActiv/forum" class="btn btn-outline-primary">
                                    <?= $lang->get('explore_forum', 'Explorer') ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fonctionnalités disponibles -->
                <div class="features-overview">
                    <h2 class="text-center mb-4"><?= $lang->get('available_features', 'Fonctionnalités disponibles') ?></h2>
                    
                    <div class="row">
                        <?php if ($user_plan === 'standard'): ?>
                            <!-- Fonctionnalités Standard -->
                            <div class="col-md-6">
                                <div class="feature-item">
                                    <i class="fas fa-calculator text-success"></i>
                                    <div>
                                        <h5><?= $lang->get('all_calculators', 'Tous les calculateurs') ?></h5>
                                        <p><?= $lang->get('calculators_full_access', 'Accès complet aux calculateurs de charges, impôts et revenus') ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="feature-item">
                                    <i class="fas fa-file-pdf text-success"></i>
                                    <div>
                                        <h5><?= $lang->get('pdf_generation', 'Génération de documents PDF') ?></h5>
                                        <p><?= $lang->get('pdf_description', 'Créez et téléchargez vos factures et devis en PDF') ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="feature-item">
                                    <i class="fas fa-headset text-success"></i>
                                    <div>
                                        <h5><?= $lang->get('email_support', 'Support par email') ?></h5>
                                        <p><?= $lang->get('support_description', 'Assistance prioritaire par email') ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="feature-item">
                                    <i class="fas fa-infinity text-success"></i>
                                    <div>
                                        <h5><?= $lang->get('unlimited_access', 'Accès illimité') ?></h5>
                                        <p><?= $lang->get('unlimited_description', 'Utilisez toutes les fonctionnalités sans restriction') ?></p>
                                    </div>
                                </div>
                            </div>
                            
                        <?php else: ?>
                            <!-- Fonctionnalités Community -->
                            <div class="col-md-6">
                                <div class="feature-item">
                                    <i class="fas fa-calculator text-warning"></i>
                                    <div>
                                        <h5><?= $lang->get('basic_calculators', 'Calculateurs de base') ?></h5>
                                        <p><?= $lang->get('basic_calculators_description', 'Calculateurs essentiels pour débuter') ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="feature-item">
                                    <i class="fas fa-comments text-warning"></i>
                                    <div>
                                        <h5><?= $lang->get('forum_access', 'Accès au forum') ?></h5>
                                        <p><?= $lang->get('forum_description', 'Participez aux discussions communautaires') ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="upgrade-banner">
                                    <div class="upgrade-content">
                                        <h4><?= $lang->get('upgrade_for_more', 'Passez au plan Standard pour débloquer toutes les fonctionnalités') ?></h4>
                                        <ul>
                                            <li><?= $lang->get('all_calculators', 'Tous les calculateurs') ?></li>
                                            <li><?= $lang->get('pdf_generation', 'Génération de documents PDF') ?></li>
                                            <li><?= $lang->get('email_support', 'Support par email') ?></li>
                                        </ul>
                                        <a href="/ProActiv/upgrade" class="btn btn-warning">
                                            <i class="fas fa-crown"></i>
                                            <?= $lang->get('upgrade_now', 'Passer au Standard') ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Actions principales -->
                <div class="main-actions text-center">
                    <h2 class="mb-4"><?= $lang->get('ready_to_start', 'Prêt à commencer ?') ?></h2>
                    <div class="action-buttons">
                        <a href="/ProActiv/dashboard" class="btn btn-primary btn-lg">
                            <i class="fas fa-tachometer-alt"></i>
                            <?= $lang->get('go_to_dashboard', 'Accéder au tableau de bord') ?>
                        </a>
                        <a href="/ProActiv/calculators" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-calculator"></i>
                            <?= $lang->get('start_calculating', 'Commencer les calculs') ?>
                        </a>
                    </div>
                </div>

                <!-- Aide et ressources -->
                <div class="help-resources">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="resource-card">
                                <i class="fas fa-book"></i>
                                <h5><?= $lang->get('user_guide', 'Guide utilisateur') ?></h5>
                                <p><?= $lang->get('guide_description', 'Apprenez à utiliser toutes les fonctionnalités') ?></p>
                                <a href="/ProActiv/help" class="btn btn-sm btn-outline-secondary">
                                    <?= $lang->get('read_guide', 'Lire le guide') ?>
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="resource-card">
                                <i class="fas fa-video"></i>
                                <h5><?= $lang->get('video_tutorials', 'Tutoriels vidéo') ?></h5>
                                <p><?= $lang->get('tutorials_description', 'Découvrez ProActiv en vidéo') ?></p>
                                <a href="/ProActiv/tutorials" class="btn btn-sm btn-outline-secondary">
                                    <?= $lang->get('watch_videos', 'Voir les vidéos') ?>
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="resource-card">
                                <i class="fas fa-question-circle"></i>
                                <h5><?= $lang->get('faq', 'Questions fréquentes') ?></h5>
                                <p><?= $lang->get('faq_description', 'Trouvez des réponses rapides') ?></p>
                                <a href="/ProActiv/faq" class="btn btn-sm btn-outline-secondary">
                                    <?= $lang->get('browse_faq', 'Consulter la FAQ') ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.welcome-page {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.welcome-header {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    padding: 3rem 0;
    margin-bottom: 3rem;
}

.welcome-animation {
    font-size: 5rem;
    color: white;
    margin-bottom: 2rem;
    animation: bounceIn 1s ease-out;
}

@keyframes bounceIn {
    0% { transform: scale(0.3); opacity: 0; }
    50% { transform: scale(1.05); }
    70% { transform: scale(0.9); }
    100% { transform: scale(1); opacity: 1; }
}

.welcome-title {
    color: white;
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.welcome-subtitle {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.3rem;
    margin-bottom: 2rem;
}

.steps-indicator {
    display: flex;
    justify-content: center;
    gap: 2rem;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    color: rgba(255, 255, 255, 0.6);
}

.step.completed {
    color: white;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.step.completed .step-number {
    background: #28a745;
}

.plan-confirmation {
    background: white;
    border-radius: 20px;
    padding: 3rem;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.plan-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-weight: bold;
    margin-bottom: 1rem;
}

.plan-badge.standard {
    background: linear-gradient(135deg, #ffc107, #ff8f00);
    color: white;
}

.plan-badge.community {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
}

.payment-confirmation {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1rem;
    padding: 1rem;
    background: #d4edda;
    border-radius: 10px;
    color: #155724;
}

.upgrade-notice {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1rem;
    padding: 1rem;
    background: #e3f2fd;
    border-radius: 10px;
    color: #1565c0;
}

.plan-visual {
    padding: 2rem;
}

.next-steps {
    margin: 4rem 0;
}

.step-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
    transition: transform 0.3s ease;
}

.step-card:hover {
    transform: translateY(-5px);
}

.step-icon {
    font-size: 3rem;
    color: #007bff;
    margin-bottom: 1rem;
}

.features-overview {
    background: white;
    border-radius: 20px;
    padding: 3rem;
    margin: 3rem 0;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.feature-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 2rem;
}

.feature-item i {
    font-size: 2rem;
    margin-top: 0.5rem;
}

.upgrade-banner {
    background: linear-gradient(135deg, #ffc107, #ff8f00);
    border-radius: 15px;
    padding: 2rem;
    color: white;
    text-align: center;
    margin-top: 2rem;
}

.upgrade-content ul {
    list-style: none;
    padding: 0;
    margin: 1rem 0;
}

.upgrade-content li {
    padding: 0.25rem 0;
}

.upgrade-content li:before {
    content: "✓ ";
    font-weight: bold;
    margin-right: 0.5rem;
}

.main-actions {
    margin: 4rem 0;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.help-resources {
    margin: 4rem 0;
}

.resource-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.resource-card i {
    font-size: 3rem;
    color: #6c757d;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .welcome-title {
        font-size: 2rem;
    }
    
    .welcome-subtitle {
        font-size: 1.1rem;
    }
    
    .steps-indicator {
        flex-direction: column;
        gap: 1rem;
    }
    
    .step {
        flex-direction: row;
        gap: 1rem;
    }
    
    .plan-confirmation {
        padding: 2rem;
    }
    
    .action-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .action-buttons .btn {
        width: 100%;
        max-width: 300px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation d'entrée pour les cartes
    const cards = document.querySelectorAll('.step-card, .resource-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 100);
            }
        });
    });
    
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
    
    // Confettis d'animation (optionnel)
    if (typeof confetti !== 'undefined') {
        confetti({
            particleCount: 100,
            spread: 70,
            origin: { y: 0.6 }
        });
    }
});
</script>

