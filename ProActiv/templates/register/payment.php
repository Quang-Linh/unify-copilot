<?php $lang = $lang ?? new LanguageService(); ?>
<div class="payment-page">
    <!-- En-tête de paiement -->
    <div class="payment-header">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <h1 class="payment-title">
                        <i class="fas fa-credit-card"></i>
                        <?= $lang->get('secure_payment', 'Paiement sécurisé') ?>
                    </h1>
                    <p class="payment-subtitle">
                        <?= $lang->get('complete_subscription', 'Finalisez votre abonnement Standard') ?>
                    </p>
                    
                    <!-- Indicateur d'étapes -->
                    <div class="steps-indicator">
                        <div class="step completed">
                            <span class="step-number"><i class="fas fa-check"></i></span>
                            <span class="step-label"><?= $lang->get('account_info', 'Informations') ?></span>
                        </div>
                        <div class="step active">
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
                <div class="row">
                    <!-- Récapitulatif de commande -->
                    <div class="col-md-4">
                        <div class="order-summary">
                            <h3><?= $lang->get('order_summary', 'Récapitulatif') ?></h3>
                            
                            <div class="plan-details">
                                <div class="plan-name">
                                    <i class="fas fa-crown text-warning"></i>
                                    <?= htmlspecialchars($plan['name']) ?>
                                </div>
                                <div class="plan-features">
                                    <ul>
                                        <li><i class="fas fa-check text-success"></i> <?= $lang->get('all_calculators', 'Tous les calculateurs') ?></li>
                                        <li><i class="fas fa-check text-success"></i> <?= $lang->get('pdf_generation', 'Génération PDF') ?></li>
                                        <li><i class="fas fa-check text-success"></i> <?= $lang->get('email_support', 'Support email') ?></li>
                                        <li><i class="fas fa-check text-success"></i> <?= $lang->get('unlimited_access', 'Accès illimité') ?></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="pricing-details">
                                <div class="price-line">
                                    <span><?= $lang->get('monthly_subscription', 'Abonnement mensuel') ?></span>
                                    <span class="price"><?= number_format($plan['price'], 2) ?>€</span>
                                </div>
                                <div class="price-line">
                                    <span><?= $lang->get('taxes', 'TVA (20%)') ?></span>
                                    <span class="price"><?= number_format($plan['price'] * 0.2, 2) ?>€</span>
                                </div>
                                <hr>
                                <div class="price-line total">
                                    <span><strong><?= $lang->get('total', 'Total') ?></strong></span>
                                    <span class="price"><strong><?= number_format($plan['price'] * 1.2, 2) ?>€</strong></span>
                                </div>
                            </div>
                            
                            <div class="billing-info">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    <?= $lang->get('billing_info', 'Facturation mensuelle automatique. Résiliable à tout moment.') ?>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire de paiement -->
                    <div class="col-md-8">
                        <div class="payment-form">
                            <div class="card">
                                <div class="card-header">
                                    <h3><?= $lang->get('payment_information', 'Informations de paiement') ?></h3>
                                    <div class="security-badges">
                                        <i class="fas fa-lock text-success"></i>
                                        <span><?= $lang->get('secure_ssl', 'Paiement sécurisé SSL') ?></span>
                                        <div class="payment-methods">
                                            <i class="fab fa-cc-visa"></i>
                                            <i class="fab fa-cc-mastercard"></i>
                                            <i class="fab fa-cc-paypal"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    
                                    <?php if (isset($_GET['error'])): ?>
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <?= htmlspecialchars($_GET['error']) ?>
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST" action="/ProActiv/register/process-payment" id="paymentForm">
                                        <input type="hidden" name="subscription_id" value="<?= htmlspecialchars($subscription['id']) ?>">
                                        
                                        <!-- Méthode de paiement -->
                                        <div class="payment-methods-selection">
                                            <h5><?= $lang->get('payment_method', 'Méthode de paiement') ?></h5>
                                            
                                            <div class="payment-method-option">
                                                <input type="radio" id="card" name="payment_method" value="card" checked>
                                                <label for="card" class="payment-method-label">
                                                    <i class="fas fa-credit-card"></i>
                                                    <?= $lang->get('credit_card', 'Carte bancaire') ?>
                                                    <div class="payment-icons">
                                                        <i class="fab fa-cc-visa"></i>
                                                        <i class="fab fa-cc-mastercard"></i>
                                                    </div>
                                                </label>
                                            </div>
                                            
                                            <div class="payment-method-option">
                                                <input type="radio" id="paypal" name="payment_method" value="paypal">
                                                <label for="paypal" class="payment-method-label">
                                                    <i class="fab fa-paypal"></i>
                                                    PayPal
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Informations de carte (affiché par défaut) -->
                                        <div id="card-details" class="payment-details">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="card_number"><?= $lang->get('card_number', 'Numéro de carte') ?></label>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               id="card_number" 
                                                               name="card_number" 
                                                               placeholder="1234 5678 9012 3456"
                                                               maxlength="19">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="card_expiry"><?= $lang->get('expiry_date', 'Date d\'expiration') ?></label>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               id="card_expiry" 
                                                               name="card_expiry" 
                                                               placeholder="MM/AA"
                                                               maxlength="5">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="card_cvc">CVC</label>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               id="card_cvc" 
                                                               name="card_cvc" 
                                                               placeholder="123"
                                                               maxlength="4">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="card_name"><?= $lang->get('cardholder_name', 'Nom du titulaire') ?></label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="card_name" 
                                                       name="card_name" 
                                                       placeholder="<?= $lang->get('name_on_card', 'Nom tel qu\'il apparaît sur la carte') ?>">
                                            </div>
                                        </div>

                                        <!-- Informations PayPal (masqué par défaut) -->
                                        <div id="paypal-details" class="payment-details" style="display: none;">
                                            <div class="paypal-info">
                                                <p><?= $lang->get('paypal_redirect', 'Vous serez redirigé vers PayPal pour finaliser le paiement.') ?></p>
                                                <div class="paypal-logo">
                                                    <i class="fab fa-paypal fa-3x text-primary"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Adresse de facturation -->
                                        <div class="billing-address">
                                            <h5><?= $lang->get('billing_address', 'Adresse de facturation') ?></h5>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="billing_country"><?= $lang->get('country', 'Pays') ?></label>
                                                        <select class="form-control" id="billing_country" name="billing_country">
                                                            <option value="FR" selected>France</option>
                                                            <option value="BE">Belgique</option>
                                                            <option value="CH">Suisse</option>
                                                            <option value="CA">Canada</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="billing_postal"><?= $lang->get('postal_code', 'Code postal') ?></label>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               id="billing_postal" 
                                                               name="billing_postal" 
                                                               placeholder="75001">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Conditions -->
                                        <div class="payment-terms">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" 
                                                       class="custom-control-input" 
                                                       id="payment_terms" 
                                                       name="payment_terms" 
                                                       required>
                                                <label class="custom-control-label" for="payment_terms">
                                                    <?= $lang->get('payment_terms_text', 'J\'accepte que mon abonnement soit renouvelé automatiquement chaque mois au tarif en vigueur. Je peux résilier à tout moment depuis mon compte.') ?>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Bouton de paiement -->
                                        <div class="payment-submit">
                                            <button type="submit" class="btn btn-success btn-lg btn-block" id="submitPayment">
                                                <i class="fas fa-lock"></i>
                                                <?= $lang->get('complete_payment', 'Finaliser le paiement') ?>
                                                <span class="amount"><?= number_format($plan['price'] * 1.2, 2) ?>€</span>
                                            </button>
                                            
                                            <div class="payment-security">
                                                <small class="text-muted">
                                                    <i class="fas fa-shield-alt"></i>
                                                    <?= $lang->get('payment_security', 'Vos données sont protégées par un cryptage SSL 256 bits') ?>
                                                </small>
                                            </div>
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
.payment-page {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.payment-header {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    padding: 2rem 0;
    margin-bottom: 2rem;
}

.payment-title {
    color: white;
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.payment-subtitle {
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

.step.completed {
    color: #28a745;
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
    background: #007bff;
}

.step.completed .step-number {
    background: #28a745;
}

.order-summary {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    height: fit-content;
    position: sticky;
    top: 2rem;
}

.order-summary h3 {
    margin-bottom: 1.5rem;
    color: #333;
}

.plan-details {
    margin-bottom: 2rem;
}

.plan-name {
    font-size: 1.2rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 1rem;
}

.plan-features ul {
    list-style: none;
    padding: 0;
}

.plan-features li {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.plan-features li:last-child {
    border-bottom: none;
}

.pricing-details {
    margin-bottom: 1.5rem;
}

.price-line {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
}

.price-line.total {
    font-size: 1.1rem;
    border-top: 2px solid #007bff;
    padding-top: 1rem;
    margin-top: 1rem;
}

.payment-form .card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: none;
}

.card-header {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    border-radius: 15px 15px 0 0 !important;
    padding: 1.5rem;
}

.security-badges {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: 1rem;
}

.payment-methods i {
    font-size: 1.5rem;
    margin-left: 0.5rem;
}

.payment-methods-selection {
    margin-bottom: 2rem;
}

.payment-method-option {
    margin-bottom: 1rem;
}

.payment-method-label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-method-label:hover {
    border-color: #007bff;
    background-color: #f8f9fa;
}

input[type="radio"]:checked + .payment-method-label {
    border-color: #007bff;
    background-color: #e3f2fd;
}

.payment-icons {
    display: flex;
    gap: 0.5rem;
}

.payment-icons i {
    font-size: 1.5rem;
}

.payment-details {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 10px;
}

.paypal-info {
    text-align: center;
    padding: 2rem;
}

.billing-address {
    margin-bottom: 2rem;
}

.payment-terms {
    margin-bottom: 2rem;
    padding: 1rem;
    background: #fff3cd;
    border-radius: 10px;
    border-left: 4px solid #ffc107;
}

.payment-submit {
    text-align: center;
}

.payment-submit .btn {
    font-size: 1.2rem;
    padding: 1rem 2rem;
    border-radius: 10px;
}

.payment-submit .amount {
    font-weight: bold;
    margin-left: 1rem;
}

.payment-security {
    margin-top: 1rem;
}

@media (max-width: 768px) {
    .order-summary {
        position: static;
        margin-bottom: 2rem;
    }
    
    .steps-indicator {
        flex-direction: column;
        gap: 1rem;
    }
    
    .step {
        flex-direction: row;
        gap: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des méthodes de paiement
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const cardDetails = document.getElementById('card-details');
    const paypalDetails = document.getElementById('paypal-details');
    
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            if (this.value === 'card') {
                cardDetails.style.display = 'block';
                paypalDetails.style.display = 'none';
            } else if (this.value === 'paypal') {
                cardDetails.style.display = 'none';
                paypalDetails.style.display = 'block';
            }
        });
    });
    
    // Formatage du numéro de carte
    const cardNumber = document.getElementById('card_number');
    if (cardNumber) {
        cardNumber.addEventListener('input', function() {
            let value = this.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            this.value = formattedValue;
        });
    }
    
    // Formatage de la date d'expiration
    const cardExpiry = document.getElementById('card_expiry');
    if (cardExpiry) {
        cardExpiry.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            this.value = value;
        });
    }
    
    // Validation du formulaire
    const paymentForm = document.getElementById('paymentForm');
    paymentForm.addEventListener('submit', function(e) {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
        
        if (selectedMethod === 'card') {
            // Validation des champs de carte
            const cardNum = document.getElementById('card_number').value.replace(/\s/g, '');
            const cardExp = document.getElementById('card_expiry').value;
            const cardCvc = document.getElementById('card_cvc').value;
            const cardName = document.getElementById('card_name').value;
            
            if (cardNum.length < 13 || cardNum.length > 19) {
                e.preventDefault();
                alert('Numéro de carte invalide');
                return;
            }
            
            if (!/^\d{2}\/\d{2}$/.test(cardExp)) {
                e.preventDefault();
                alert('Date d\'expiration invalide (MM/AA)');
                return;
            }
            
            if (cardCvc.length < 3 || cardCvc.length > 4) {
                e.preventDefault();
                alert('Code CVC invalide');
                return;
            }
            
            if (cardName.trim().length < 2) {
                e.preventDefault();
                alert('Nom du titulaire requis');
                return;
            }
        }
        
        // Désactiver le bouton pour éviter les doubles soumissions
        const submitBtn = document.getElementById('submitPayment');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement en cours...';
    });
});
</script>

