<?php $lang = $lang ?? new LanguageService(); ?>
<div class="register-page">
    <!-- En-tête d'inscription -->
    <div class="register-header">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <h1 class="register-title">
                        <i class="fas fa-user-plus"></i>
                        <?= $lang->get('join_proactiv', 'Rejoignez ProActiv') ?>
                    </h1>
                    <p class="register-subtitle">
                        <?= $lang->get('choose_plan_register', 'Choisissez votre formule et créez votre compte en quelques minutes') ?>
                    </p>
                    
                    <!-- Indicateur d'étapes -->
                    <div class="steps-indicator">
                        <div class="step active">
                            <span class="step-number">1</span>
                            <span class="step-label"><?= $lang->get('account_info', 'Informations') ?></span>
                        </div>
                        <div class="step">
                            <span class="step-number">2</span>
                            <span class="step-label"><?= $lang->get('payment', 'Paiement') ?></span>
                        </div>
                        <div class="step">
                            <span class="step-number">3</span>
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
                
                <!-- Sélection du plan -->
                <div class="plans-section mb-5">
                    <h2 class="text-center mb-4"><?= $lang->get('choose_plan', 'Choisissez votre formule') ?></h2>
                    
                    <div class="row">
                        <!-- Plan Communautaire -->
                        <div class="col-md-6">
                            <div class="plan-card <?= $selected_plan === 'community' ? 'selected' : '' ?>" data-plan="community">
                                <div class="plan-header community">
                                    <h3><?= $lang->get('community_plan', 'Communautaire') ?></h3>
                                    <div class="plan-price">
                                        <span class="price">0€</span>
                                        <span class="period"><?= $lang->get('free_trial', '/ Essai gratuit') ?></span>
                                    </div>
                                </div>
                                <div class="plan-features">
                                    <ul>
                                        <li><i class="fas fa-check"></i> <?= $lang->get('basic_calculators', 'Calculateurs de base') ?></li>
                                        <li><i class="fas fa-check"></i> <?= $lang->get('forum_access', 'Accès au forum') ?></li>
                                        <li><i class="fas fa-check"></i> <?= $lang->get('30_days_trial', '30 jours d\'essai') ?></li>
                                        <li><i class="fas fa-times text-muted"></i> <?= $lang->get('no_pdf_generation', 'Pas de génération PDF') ?></li>
                                        <li><i class="fas fa-times text-muted"></i> <?= $lang->get('limited_support', 'Support limité') ?></li>
                                    </ul>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-select-plan">
                                    <?= $lang->get('select_plan', 'Choisir ce plan') ?>
                                </button>
                            </div>
                        </div>

                        <!-- Plan Standard -->
                        <div class="col-md-6">
                            <div class="plan-card <?= $selected_plan === 'standard' ? 'selected' : '' ?>" data-plan="standard">
                                <div class="plan-header standard">
                                    <div class="plan-badge"><?= $lang->get('recommended', 'Recommandé') ?></div>
                                    <h3><?= $lang->get('standard_plan', 'Standard') ?></h3>
                                    <div class="plan-price">
                                        <span class="price">5€</span>
                                        <span class="period"><?= $lang->get('per_month', '/ mois') ?></span>
                                    </div>
                                </div>
                                <div class="plan-features">
                                    <ul>
                                        <li><i class="fas fa-check"></i> <?= $lang->get('all_calculators', 'Tous les calculateurs') ?></li>
                                        <li><i class="fas fa-check"></i> <?= $lang->get('pdf_generation', 'Génération de documents PDF') ?></li>
                                        <li><i class="fas fa-check"></i> <?= $lang->get('forum_access', 'Accès au forum') ?></li>
                                        <li><i class="fas fa-check"></i> <?= $lang->get('2_accounts', 'Jusqu\'à 2 comptes') ?></li>
                                        <li><i class="fas fa-check"></i> <?= $lang->get('email_support', 'Support par email') ?></li>
                                        <li><i class="fas fa-check"></i> <?= $lang->get('unlimited_access', 'Accès illimité') ?></li>
                                    </ul>
                                </div>
                                <button type="button" class="btn btn-primary btn-select-plan">
                                    <?= $lang->get('select_plan', 'Choisir ce plan') ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulaire d'inscription -->
                <div class="registration-form">
                    <div class="row">
                        <div class="col-lg-8 mx-auto">
                            <div class="card">
                                <div class="card-header">
                                    <h3><?= $lang->get('create_account', 'Créer votre compte') ?></h3>
                                    <p class="mb-0"><?= $lang->get('fill_info_below', 'Remplissez les informations ci-dessous') ?></p>
                                </div>
                                <div class="card-body">
                                    
                                    <?php if (!empty($errors)): ?>
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                <?php foreach ($errors as $error): ?>
                                                    <li><?= htmlspecialchars($error) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST" action="/ProActiv/register/process" id="registerForm">
                                        <input type="hidden" name="plan" id="selectedPlan" value="<?= htmlspecialchars($selected_plan) ?>">
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="first_name"><?= $lang->get('first_name', 'Prénom') ?> *</label>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="first_name" 
                                                           name="first_name" 
                                                           value="<?= htmlspecialchars($form_data['first_name'] ?? '') ?>"
                                                           required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="last_name"><?= $lang->get('last_name', 'Nom') ?> *</label>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="last_name" 
                                                           name="last_name" 
                                                           value="<?= htmlspecialchars($form_data['last_name'] ?? '') ?>"
                                                           required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="email"><?= $lang->get('email', 'Email') ?> *</label>
                                            <input type="email" 
                                                   class="form-control" 
                                                   id="email" 
                                                   name="email" 
                                                   value="<?= htmlspecialchars($form_data['email'] ?? '') ?>"
                                                   required>
                                            <div class="email-check-feedback"></div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="password"><?= $lang->get('password', 'Mot de passe') ?> *</label>
                                                    <input type="password" 
                                                           class="form-control" 
                                                           id="password" 
                                                           name="password" 
                                                           required>
                                                    <small class="form-text text-muted">
                                                        <?= $lang->get('password_min_chars', 'Au moins 6 caractères') ?>
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="password_confirm"><?= $lang->get('confirm_password', 'Confirmer le mot de passe') ?> *</label>
                                                    <input type="password" 
                                                           class="form-control" 
                                                           id="password_confirm" 
                                                           name="password_confirm" 
                                                           required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" 
                                                       class="custom-control-input" 
                                                       id="terms_accepted" 
                                                       name="terms_accepted" 
                                                       value="1" 
                                                       <?= !empty($form_data['terms_accepted']) ? 'checked' : '' ?>
                                                       required>
                                                <label class="custom-control-label" for="terms_accepted">
                                                    <?= $lang->get('accept_terms', 'J\'accepte les') ?> 
                                                    <a href="/ProActiv/terms" target="_blank"><?= $lang->get('terms_conditions', 'conditions d\'utilisation') ?></a>
                                                    <?= $lang->get('and', 'et la') ?>
                                                    <a href="/ProActiv/privacy" target="_blank"><?= $lang->get('privacy_policy', 'politique de confidentialité') ?></a>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group text-center">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="fas fa-user-plus"></i>
                                                <?= $lang->get('create_account', 'Créer mon compte') ?>
                                            </button>
                                        </div>

                                        <div class="text-center">
                                            <p><?= $lang->get('already_account', 'Vous avez déjà un compte ?') ?> 
                                               <a href="/ProActiv/login"><?= $lang->get('login_here', 'Connectez-vous ici') ?></a>
                                            </p>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.register-page {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.register-header {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    padding: 2rem 0;
    margin-bottom: 2rem;
}

.register-title {
    color: white;
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.register-subtitle {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.2rem;
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

.step.active {
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

.step.active .step-number {
    background: #28a745;
}

.plan-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}

.plan-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.plan-card.selected {
    border: 3px solid #007bff;
    transform: scale(1.02);
}

.plan-header {
    text-align: center;
    margin-bottom: 2rem;
    position: relative;
}

.plan-badge {
    position: absolute;
    top: -10px;
    right: -10px;
    background: #28a745;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: bold;
}

.plan-header h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.plan-price {
    margin-bottom: 1rem;
}

.price {
    font-size: 2.5rem;
    font-weight: 700;
    color: #007bff;
}

.period {
    color: #6c757d;
    font-size: 1rem;
}

.plan-features ul {
    list-style: none;
    padding: 0;
}

.plan-features li {
    padding: 0.5rem 0;
    display: flex;
    align-items: center;
}

.plan-features i {
    margin-right: 0.75rem;
    width: 16px;
}

.btn-select-plan {
    width: 100%;
    padding: 0.75rem;
    font-weight: 600;
    border-radius: 10px;
}

.registration-form .card {
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px 15px 0 0 !important;
    padding: 2rem;
}

.card-header h3 {
    margin-bottom: 0.5rem;
    font-weight: 700;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-control {
    border-radius: 10px;
    padding: 0.75rem 1rem;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.email-check-feedback {
    margin-top: 0.5rem;
    font-size: 0.875rem;
}

.email-check-feedback.available {
    color: #28a745;
}

.email-check-feedback.unavailable {
    color: #dc3545;
}

@media (max-width: 768px) {
    .steps-indicator {
        gap: 1rem;
    }
    
    .step-label {
        font-size: 0.8rem;
    }
    
    .register-title {
        font-size: 2rem;
    }
    
    .plan-card {
        margin-bottom: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la sélection des plans
    const planCards = document.querySelectorAll('.plan-card');
    const selectedPlanInput = document.getElementById('selectedPlan');
    
    planCards.forEach(card => {
        card.addEventListener('click', function() {
            planCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            selectedPlanInput.value = this.dataset.plan;
        });
    });
    
    // Boutons de sélection de plan
    document.querySelectorAll('.btn-select-plan').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const planCard = this.closest('.plan-card');
            planCard.click();
        });
    });
    
    // Vérification de la disponibilité de l'email
    const emailInput = document.getElementById('email');
    const emailFeedback = document.querySelector('.email-check-feedback');
    let emailCheckTimeout;
    
    emailInput.addEventListener('input', function() {
        clearTimeout(emailCheckTimeout);
        const email = this.value.trim();
        
        if (email && email.includes('@')) {
            emailCheckTimeout = setTimeout(() => {
                checkEmailAvailability(email);
            }, 500);
        } else {
            emailFeedback.innerHTML = '';
        }
    });
    
    function checkEmailAvailability(email) {
        fetch('/ProActiv/register/check-email', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'email=' + encodeURIComponent(email)
        })
        .then(response => response.json())
        .then(data => {
            if (data.available) {
                emailFeedback.innerHTML = '<i class="fas fa-check"></i> Email disponible';
                emailFeedback.className = 'email-check-feedback available';
            } else {
                emailFeedback.innerHTML = '<i class="fas fa-times"></i> ' + (data.error || 'Email déjà utilisé');
                emailFeedback.className = 'email-check-feedback unavailable';
            }
        })
        .catch(error => {
            console.error('Erreur lors de la vérification de l\'email:', error);
        });
    }
    
    // Validation du formulaire
    const form = document.getElementById('registerForm');
    form.addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirm').value;
        
        if (password !== passwordConfirm) {
            e.preventDefault();
            alert('Les mots de passe ne correspondent pas.');
            return false;
        }
        
        if (password.length < 6) {
            e.preventDefault();
            alert('Le mot de passe doit contenir au moins 6 caractères.');
            return false;
        }
    });
});
</script>

