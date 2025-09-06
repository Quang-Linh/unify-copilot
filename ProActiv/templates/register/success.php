<?php $lang = $lang ?? new LanguageService(); ?>
<div class="success-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="success-card">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    
                    <h1 class="success-title">
                        <?= $lang->get('welcome_to_proactiv', 'Bienvenue sur ProActiv !') ?>
                    </h1>
                    
                    <p class="success-subtitle">
                        <?= $lang->get('account_created_successfully', 'Votre compte a été créé avec succès') ?>
                    </p>
                    
                    <!-- Informations du compte -->
                    <div class="account-info">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <i class="fas fa-user"></i>
                                    <div>
                                        <strong><?= $lang->get('account_name', 'Nom du compte') ?></strong>
                                        <p><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <i class="fas fa-envelope"></i>
                                    <div>
                                        <strong><?= $lang->get('email', 'Email') ?></strong>
                                        <p><?= htmlspecialchars($user['email']) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <i class="fas fa-crown"></i>
                                    <div>
                                        <strong><?= $lang->get('subscription_plan', 'Formule') ?></strong>
                                        <p>
                                            <?php if ($subscription['plan'] === 'community'): ?>
                                                <span class="badge badge-warning"><?= $lang->get('community_plan', 'Communautaire') ?></span>
                                                <small class="text-muted d-block">
                                                    <?= $lang->get('trial_expires', 'Essai expire le') ?> 
                                                    <?= date('d/m/Y', strtotime($subscription['expires_at'])) ?>
                                                </small>
                                            <?php else: ?>
                                                <span class="badge badge-success"><?= $lang->get('standard_plan', 'Standard') ?></span>
                                                <small class="text-muted d-block">5€/mois</small>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <i class="fas fa-flag"></i>
                                    <div>
                                        <strong><?= $lang->get('country', 'Pays') ?></strong>
                                        <p><?= $current_country['flag'] ?> <?= $current_country['name'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Prochaines étapes -->
                    <div class="next-steps">
                        <h3><?= $lang->get('next_steps', 'Prochaines étapes') ?></h3>
                        
                        <div class="steps-list">
                            <?php if ($subscription['plan'] === 'community'): ?>
                                <div class="step-item">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h4><?= $lang->get('explore_features', 'Explorez les fonctionnalités') ?></h4>
                                        <p><?= $lang->get('trial_30_days', 'Vous avez 30 jours pour tester toutes les fonctionnalités de base') ?></p>
                                    </div>
                                </div>
                                
                                <div class="step-item">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h4><?= $lang->get('upgrade_anytime', 'Passez à Standard quand vous voulez') ?></h4>
                                        <p><?= $lang->get('unlock_all_features', 'Débloquez toutes les fonctionnalités pour seulement 5€/mois') ?></p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="step-item">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h4><?= $lang->get('payment_processing', 'Traitement du paiement') ?></h4>
                                        <p><?= $lang->get('payment_confirmation_email', 'Vous recevrez un email de confirmation de paiement') ?></p>
                                    </div>
                                </div>
                                
                                <div class="step-item">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h4><?= $lang->get('full_access', 'Accès complet') ?></h4>
                                        <p><?= $lang->get('all_features_available', 'Toutes les fonctionnalités sont maintenant disponibles') ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="step-item">
                                <div class="step-number"><?= $subscription['plan'] === 'community' ? '3' : '3' ?></div>
                                <div class="step-content">
                                    <h4><?= $lang->get('join_community', 'Rejoignez la communauté') ?></h4>
                                    <p><?= $lang->get('forum_help', 'Posez vos questions sur le forum et échangez avec d\'autres auto-entrepreneurs') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="action-buttons">
                        <a href="/ProActiv/dashboard" class="btn btn-primary btn-lg">
                            <i class="fas fa-tachometer-alt"></i>
                            <?= $lang->get('go_to_dashboard', 'Accéder au tableau de bord') ?>
                        </a>
                        
                        <?php if ($subscription['plan'] === 'community'): ?>
                            <a href="/ProActiv/register?upgrade=standard" class="btn btn-outline-success">
                                <i class="fas fa-arrow-up"></i>
                                <?= $lang->get('upgrade_to_standard', 'Passer à Standard') ?>
                            </a>
                        <?php endif; ?>
                        
                        <a href="/ProActiv/forum" class="btn btn-outline-primary">
                            <i class="fas fa-comments"></i>
                            <?= $lang->get('visit_forum', 'Visiter le forum') ?>
                        </a>
                    </div>
                    
                    <!-- Email de confirmation -->
                    <div class="email-notice">
                        <i class="fas fa-info-circle"></i>
                        <span>
                            <?= $lang->get('confirmation_email_sent', 'Un email de confirmation a été envoyé à') ?> 
                            <strong><?= htmlspecialchars($user['email']) ?></strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.success-page {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 2rem 0;
    display: flex;
    align-items: center;
}

.success-card {
    background: white;
    border-radius: 20px;
    padding: 3rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    text-align: center;
    position: relative;
    overflow: hidden;
}

.success-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #28a745, #20c997, #17a2b8);
}

.success-icon {
    font-size: 4rem;
    color: #28a745;
    margin-bottom: 1.5rem;
    animation: bounceIn 1s ease-out;
}

.success-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.success-subtitle {
    font-size: 1.2rem;
    color: #6c757d;
    margin-bottom: 2rem;
}

.account-info {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 2rem;
    margin: 2rem 0;
    text-align: left;
}

.info-item {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
}

.info-item i {
    font-size: 1.5rem;
    color: #007bff;
    margin-right: 1rem;
    width: 30px;
    text-align: center;
}

.info-item strong {
    display: block;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.info-item p {
    margin: 0;
    color: #6c757d;
}

.next-steps {
    text-align: left;
    margin: 2rem 0;
}

.next-steps h3 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 2rem;
    font-weight: 600;
}

.steps-list {
    max-width: 600px;
    margin: 0 auto;
}

.step-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    border-left: 4px solid #007bff;
}

.step-number {
    background: #007bff;
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 1.5rem;
    flex-shrink: 0;
}

.step-content h4 {
    color: #2c3e50;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.step-content p {
    color: #6c757d;
    margin: 0;
    line-height: 1.5;
}

.action-buttons {
    margin: 2rem 0;
}

.action-buttons .btn {
    margin: 0.5rem;
    padding: 0.75rem 2rem;
    border-radius: 10px;
    font-weight: 600;
}

.email-notice {
    background: #e3f2fd;
    border: 1px solid #bbdefb;
    border-radius: 10px;
    padding: 1rem;
    margin-top: 2rem;
    color: #1976d2;
    display: flex;
    align-items: center;
    justify-content: center;
}

.email-notice i {
    margin-right: 0.5rem;
}

@keyframes bounceIn {
    0% {
        transform: scale(0.3);
        opacity: 0;
    }
    50% {
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

@media (max-width: 768px) {
    .success-card {
        padding: 2rem 1.5rem;
        margin: 1rem;
    }
    
    .success-title {
        font-size: 2rem;
    }
    
    .step-item {
        flex-direction: column;
        text-align: center;
    }
    
    .step-number {
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .action-buttons .btn {
        display: block;
        width: 100%;
        margin: 0.5rem 0;
    }
}
</style>

