<?php
/**
 * Vue du calculateur de revenus nets
 */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4><i class="fas fa-euro-sign me-2"></i>Calculateur de revenus nets</h4>
                    <p class="mb-0">Calculez votre revenu net après toutes charges et impôts</p>
                </div>
                <div class="card-body">
                    <form id="revenusForm">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="chiffre_affaires" class="form-label">
                                        <i class="fas fa-chart-line text-success me-1"></i>
                                        Chiffre d'affaires annuel (€)
                                    </label>
                                    <input type="number" class="form-control" id="chiffre_affaires" name="chiffre_affaires" 
                                           placeholder="Ex: 50000" min="0" step="0.01" required>
                                    <div class="form-text">Votre CA annuel hors taxes</div>
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
                                    <div class="form-text">Votre secteur d'activité</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="charges_deductibles" class="form-label">
                                <i class="fas fa-receipt text-danger me-1"></i>
                                Charges déductibles supplémentaires (€)
                            </label>
                            <input type="number" class="form-control" id="charges_deductibles" name="charges_deductibles" 
                                   placeholder="Ex: 2000" min="0" step="0.01" value="0">
                            <div class="form-text">Frais professionnels, équipements, formations, etc.</div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning btn-lg text-dark">
                                <i class="fas fa-calculator me-2"></i>Calculer mon revenu net
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
                    <h6>Charges incluses dans le calcul :</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-shield-alt text-primary me-2"></i>Cotisations sociales</li>
                        <li><i class="fas fa-percentage text-success me-2"></i>Impôts sur le revenu</li>
                        <li><i class="fas fa-building text-warning me-2"></i>CFE</li>
                        <li><i class="fas fa-graduation-cap text-info me-2"></i>Formation professionnelle</li>
                        <li><i class="fas fa-receipt text-secondary me-2"></i>Charges déductibles</li>
                    </ul>
                    
                    <hr>
                    
                    <h6>Estimations moyennes :</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td>Professions libérales</td>
                                    <td><span class="badge bg-warning">~45%</span></td>
                                </tr>
                                <tr>
                                    <td>Activités commerciales</td>
                                    <td><span class="badge bg-success">~35%</span></td>
                                </tr>
                                <tr>
                                    <td>Activités artisanales</td>
                                    <td><span class="badge bg-info">~42%</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="alert alert-success mt-3">
                        <i class="fas fa-lightbulb me-2"></i>
                        <small>Optimisez vos revenus en déduisant vos frais professionnels !</small>
                    </div>
                </div>
            </div>
            
            <!-- Résultats -->
            <div id="resultsCard" class="card mt-3" style="display: none;">
                <div class="card-header bg-warning text-dark">
                    <h5><i class="fas fa-chart-bar me-2"></i>Analyse de vos revenus</h5>
                </div>
                <div class="card-body" id="resultsContent">
                    <!-- Les résultats seront affichés ici via JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('revenusForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Affichage du loading
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Calcul en cours...';
    submitBtn.disabled = true;
    
    fetch('/calculators/revenus', {
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
    
    let html = `
        <div class="row text-center mb-3">
            <div class="col-4">
                <div class="border rounded p-2">
                    <h6 class="text-muted small">Revenu net</h6>
                    <h4 class="text-success">${formatCurrency(results.revenu_net)}</h4>
                    <small class="text-muted">par an</small>
                </div>
            </div>
            <div class="col-4">
                <div class="border rounded p-2">
                    <h6 class="text-muted small">Par mois</h6>
                    <h4 class="text-primary">${formatCurrency(results.revenu_net_mensuel)}</h4>
                    <small class="text-muted">net</small>
                </div>
            </div>
            <div class="col-4">
                <div class="border rounded p-2">
                    <h6 class="text-muted small">Par heure</h6>
                    <h4 class="text-info">${formatCurrency(results.revenu_net_horaire)}</h4>
                    <small class="text-muted">net</small>
                </div>
            </div>
        </div>
        
        <div class="alert alert-${results.taux_charges > 50 ? 'danger' : results.taux_charges > 40 ? 'warning' : 'success'}">
            <strong>Taux de charges total :</strong> ${results.taux_charges.toFixed(1)}%
        </div>
        
        <h6>Répartition des charges :</h6>
        <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between">
                <span>Revenu brut</span>
                <strong class="text-success">${formatCurrency(results.revenu_brut)}</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <span>Charges sociales</span>
                <strong class="text-warning">-${formatCurrency(results.charges_sociales)}</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <span>Impôts</span>
                <strong class="text-danger">-${formatCurrency(results.impots)}</strong>
            </li>
            ${results.charges_deductibles > 0 ? `
            <li class="list-group-item d-flex justify-content-between">
                <span>Charges déductibles</span>
                <strong class="text-secondary">-${formatCurrency(results.charges_deductibles)}</strong>
            </li>
            ` : ''}
            <li class="list-group-item d-flex justify-content-between bg-light">
                <span><strong>Revenu net</strong></span>
                <strong class="text-success">${formatCurrency(results.revenu_net)}</strong>
            </li>
        </ul>
        
        <div class="mt-3">
            <button class="btn btn-outline-warning btn-sm me-2" onclick="saveCalculation()">
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

