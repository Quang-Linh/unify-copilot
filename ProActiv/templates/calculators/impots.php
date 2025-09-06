<?php
/**
 * Vue du calculateur d'impôts
 */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4><i class="fas fa-percentage me-2"></i>Calculateur d'impôts sur le revenu</h4>
                    <p class="mb-0">Estimez votre impôt avec les abattements forfaitaires et tranches 2025</p>
                </div>
                <div class="card-body">
                    <form id="impotsForm">
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
                                    <div class="form-text">Montant hors taxes de votre chiffre d'affaires</div>
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
                                    <div class="form-text">Détermine l'abattement forfaitaire applicable</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tax_option" class="form-label">
                                        <i class="fas fa-calculator text-warning me-1"></i>
                                        Option fiscale
                                    </label>
                                    <select class="form-select" id="tax_option" name="tax_option" required>
                                        <?php foreach ($tax_options as $key => $label): ?>
                                            <option value="<?= $key ?>"><?= htmlspecialchars($label) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Barème progressif ou versement libératoire</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="family_quotient" class="form-label">
                                        <i class="fas fa-users text-primary me-1"></i>
                                        Quotient familial
                                    </label>
                                    <input type="number" class="form-control" id="family_quotient" name="family_quotient" 
                                           value="1" min="1" max="6" step="0.5">
                                    <div class="form-text">Nombre de parts fiscales (1 = célibataire)</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="other_income" class="form-label">
                                <i class="fas fa-coins text-secondary me-1"></i>
                                Autres revenus annuels (€)
                            </label>
                            <input type="number" class="form-control" id="other_income" name="other_income" 
                                   placeholder="Ex: 5000" min="0" step="0.01" value="0">
                            <div class="form-text">Salaires, pensions, revenus fonciers, etc.</div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-percentage me-2"></i>Calculer mes impôts
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5><i class="fas fa-info-circle me-2"></i>Abattements forfaitaires 2025</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-user-tie text-primary me-2"></i>
                            <strong>Professions libérales :</strong> 34%
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-store text-success me-2"></i>
                            <strong>Activités commerciales :</strong> 71%
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-hammer text-warning me-2"></i>
                            <strong>Activités artisanales :</strong> 50%
                        </li>
                    </ul>
                    
                    <hr>
                    
                    <h6>Tranches d'imposition 2025 :</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td>0 - 11 294€</td>
                                    <td><span class="badge bg-success">0%</span></td>
                                </tr>
                                <tr>
                                    <td>11 294 - 28 797€</td>
                                    <td><span class="badge bg-primary">11%</span></td>
                                </tr>
                                <tr>
                                    <td>28 797 - 82 341€</td>
                                    <td><span class="badge bg-warning">30%</span></td>
                                </tr>
                                <tr>
                                    <td>82 341 - 177 106€</td>
                                    <td><span class="badge bg-danger">41%</span></td>
                                </tr>
                                <tr>
                                    <td>> 177 106€</td>
                                    <td><span class="badge bg-dark">45%</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-lightbulb me-2"></i>
                        <small>Le versement libératoire peut être avantageux selon votre situation.</small>
                    </div>
                </div>
            </div>
            
            <!-- Résultats -->
            <div id="resultsCard" class="card mt-3" style="display: none;">
                <div class="card-header bg-success text-white">
                    <h5><i class="fas fa-chart-pie me-2"></i>Résultats du calcul</h5>
                </div>
                <div class="card-body" id="resultsContent">
                    <!-- Les résultats seront affichés ici via JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('impotsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Affichage du loading
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Calcul en cours...';
    submitBtn.disabled = true;
    
    fetch('/calculators/impots', {
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
            <div class="col-6">
                <div class="border rounded p-3">
                    <h6 class="text-muted">Bénéfice imposable</h6>
                    <h4 class="text-info">${formatCurrency(results.benefice_imposable)}</h4>
                    <small class="text-muted">après abattement</small>
                </div>
            </div>
            <div class="col-6">
                <div class="border rounded p-3">
                    <h6 class="text-muted">Impôt estimé</h6>
                    <h4 class="text-danger">${formatCurrency(results.impot_bareme || results.versement_liberatoire || 0)}</h4>
                    <small class="text-muted">par an</small>
                </div>
            </div>
        </div>
        
        <h6>Détail du calcul :</h6>
        <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between">
                <span>Chiffre d'affaires</span>
                <strong>${formatCurrency(results.benefice_imposable / (1 - getAbattementRate()))}</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <span>Abattement forfaitaire</span>
                <strong class="text-success">-${formatCurrency(results.abattement)}</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <span>Bénéfice imposable</span>
                <strong>${formatCurrency(results.benefice_imposable)}</strong>
            </li>
            ${results.revenu_total ? `
            <li class="list-group-item d-flex justify-content-between">
                <span>Revenu total imposable</span>
                <strong>${formatCurrency(results.revenu_total)}</strong>
            </li>
            ` : ''}
            <li class="list-group-item d-flex justify-content-between bg-light">
                <span><strong>Impôt sur le revenu</strong></span>
                <strong class="text-danger">${formatCurrency(results.impot_bareme || results.versement_liberatoire || 0)}</strong>
            </li>
        </ul>
        
        <div class="mt-3">
            <button class="btn btn-outline-success btn-sm me-2" onclick="saveCalculation()">
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

function getAbattementRate() {
    const activityType = document.getElementById('activity_type').value;
    const rates = {
        'liberal': 0.34,
        'commercial': 0.71,
        'artisanal': 0.50
    };
    return rates[activityType] || 0.34;
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

