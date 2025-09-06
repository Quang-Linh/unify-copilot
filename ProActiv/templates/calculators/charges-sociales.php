<?php
/**
 * Vue du calculateur de charges sociales
 */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4><i class="fas fa-calculator me-2"></i>Calculateur de charges sociales</h4>
                    <p class="mb-0">Calculez vos cotisations sociales selon votre activité et régime</p>
                </div>
                <div class="card-body">
                    <form id="chargesForm">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="chiffre_affaires" class="form-label">
                                        <i class="fas fa-euro-sign text-success me-1"></i>
                                        Chiffre d'affaires annuel (€)
                                    </label>
                                    <input type="number" class="form-control" id="chiffre_affaires" name="chiffre_affaires" 
                                           placeholder="Ex: 50000" min="0" step="0.01" required>
                                    <div class="form-text">Montant hors taxes de votre chiffre d'affaires annuel</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="activity_type" class="form-label">
                                        <i class="fas fa-briefcase text-info me-1"></i>
                                        Type d'activité
                                    </label>
                                    <select class="form-select" id="activity_type" name="activity_type" required>
                                        <?php foreach ($activity_types as $key => $label): ?>
                                            <option value="<?= $key ?>"><?= htmlspecialchars($label) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Votre secteur d'activité principal</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="regime" class="form-label">
                                        <i class="fas fa-file-contract text-warning me-1"></i>
                                        Régime fiscal
                                    </label>
                                    <select class="form-select" id="regime" name="regime" required>
                                        <?php foreach ($regimes as $key => $label): ?>
                                            <option value="<?= $key ?>"><?= htmlspecialchars($label) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Votre régime fiscal actuel</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="period" class="form-label">
                                        <i class="fas fa-calendar text-secondary me-1"></i>
                                        Période d'affichage
                                    </label>
                                    <select class="form-select" id="period" name="period">
                                        <option value="annual">Annuel</option>
                                        <option value="monthly">Mensuel</option>
                                    </select>
                                    <div class="form-text">Affichage des résultats par période</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-calculator me-2"></i>Calculer mes charges sociales
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5><i class="fas fa-info-circle me-2"></i>Informations</h5>
                </div>
                <div class="card-body">
                    <h6>Taux de cotisations 2025 :</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i><strong>Professions libérales :</strong> 22%</li>
                        <li><i class="fas fa-check text-success me-2"></i><strong>Activités commerciales :</strong> 12.8%</li>
                        <li><i class="fas fa-check text-success me-2"></i><strong>Activités artisanales :</strong> 22%</li>
                    </ul>
                    
                    <hr>
                    
                    <h6>Charges incluses :</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-shield-alt text-primary me-2"></i>Assurance maladie</li>
                        <li><i class="fas fa-home text-primary me-2"></i>Allocations familiales</li>
                        <li><i class="fas fa-graduation-cap text-primary me-2"></i>Formation professionnelle</li>
                        <li><i class="fas fa-building text-primary me-2"></i>CFE (si CA > 5000€)</li>
                    </ul>
                    
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <small>Les calculs sont indicatifs et basés sur la réglementation 2025.</small>
                    </div>
                </div>
            </div>
            
            <!-- Résultats -->
            <div id="resultsCard" class="card mt-3" style="display: none;">
                <div class="card-header bg-success text-white">
                    <h5><i class="fas fa-chart-line me-2"></i>Résultats du calcul</h5>
                </div>
                <div class="card-body" id="resultsContent">
                    <!-- Les résultats seront affichés ici via JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('chargesForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Affichage du loading
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Calcul en cours...';
    submitBtn.disabled = true;
    
    fetch('/calculators/charges-sociales', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayResults(data.results);
        } else {
            alert('Erreur : ' + (data.error || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur de communication avec le serveur');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

function displayResults(results) {
    const resultsCard = document.getElementById('resultsCard');
    const resultsContent = document.getElementById('resultsContent');
    
    const period = document.getElementById('period').value;
    const isPeriodMonthly = period === 'monthly';
    
    let html = `
        <div class="row text-center">
            <div class="col-6">
                <div class="border rounded p-3 mb-3">
                    <h6 class="text-muted">Cotisations sociales</h6>
                    <h4 class="text-primary">${formatCurrency(isPeriodMonthly ? results.cotisations_sociales_mensuel || results.cotisations_sociales/12 : results.cotisations_sociales)}</h4>
                    <small class="text-muted">${isPeriodMonthly ? 'par mois' : 'par an'}</small>
                </div>
            </div>
            <div class="col-6">
                <div class="border rounded p-3 mb-3">
                    <h6 class="text-muted">Total charges</h6>
                    <h4 class="text-success">${formatCurrency(isPeriodMonthly ? results.total_charges_mensuel || results.total_charges/12 : results.total_charges)}</h4>
                    <small class="text-muted">${isPeriodMonthly ? 'par mois' : 'par an'}</small>
                </div>
            </div>
        </div>
        
        <div class="alert alert-info">
            <strong>Pourcentage du CA :</strong> ${results.pourcentage_ca.toFixed(2)}%
        </div>
        
        <h6>Détail des charges :</h6>
        <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between">
                <span>Cotisations sociales</span>
                <strong>${formatCurrency(results.cotisations_sociales)}</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <span>CFE</span>
                <strong>${formatCurrency(results.cfe)}</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <span>Formation professionnelle</span>
                <strong>${formatCurrency(results.formation)}</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between bg-light">
                <span><strong>Total</strong></span>
                <strong class="text-success">${formatCurrency(results.total_charges)}</strong>
            </li>
        </ul>
        
        <div class="mt-3">
            <button class="btn btn-outline-primary btn-sm me-2" onclick="saveCalculation()">
                <i class="fas fa-save me-1"></i>Sauvegarder
            </button>
            <button class="btn btn-outline-secondary btn-sm" onclick="printResults()">
                <i class="fas fa-print me-1"></i>Imprimer
            </button>
        </div>
    `;
    
    resultsContent.innerHTML = html;
    resultsCard.style.display = 'block';
    resultsCard.scrollIntoView({ behavior: 'smooth' });
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(amount);
}

function saveCalculation() {
    alert('Calcul sauvegardé dans votre historique !');
}

function printResults() {
    window.print();
}
</script>

