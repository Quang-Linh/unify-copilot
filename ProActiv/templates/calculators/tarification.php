<?php
/**
 * Vue de l'aide à la tarification
 */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4><i class="fas fa-tags me-2"></i>Aide à la tarification</h4>
                    <p class="mb-0">Déterminez vos tarifs pour atteindre vos objectifs de revenus</p>
                </div>
                <div class="card-body">
                    <form id="tarificationForm">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="target_revenu" class="form-label">
                                        <i class="fas fa-bullseye text-success me-1"></i>
                                        Revenu net cible annuel (€)
                                    </label>
                                    <input type="number" class="form-control" id="target_revenu" name="target_revenu" 
                                           placeholder="Ex: 30000" min="0" step="0.01" required>
                                    <div class="form-text">Le revenu net que vous souhaitez atteindre</div>
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
                                    <div class="form-text">Détermine le taux de charges estimé</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="working_days" class="form-label">
                                        <i class="fas fa-calendar-day text-primary me-1"></i>
                                        Jours travaillés par an
                                    </label>
                                    <input type="number" class="form-control" id="working_days" name="working_days" 
                                           value="220" min="1" max="365" required>
                                    <div class="form-text">Nombre de jours ouvrés (220 = standard)</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="hours_per_day" class="form-label">
                                        <i class="fas fa-clock text-warning me-1"></i>
                                        Heures par jour
                                    </label>
                                    <input type="number" class="form-control" id="hours_per_day" name="hours_per_day" 
                                           value="7" min="1" max="24" step="0.5" required>
                                    <div class="form-text">Heures de travail facturables par jour</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-info btn-lg">
                                <i class="fas fa-magic me-2"></i>Calculer mes tarifs optimaux
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5><i class="fas fa-lightbulb me-2"></i>Conseils tarification</h5>
                </div>
                <div class="card-body">
                    <h6>Facteurs à considérer :</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-star text-warning me-2"></i>
                            <strong>Expérience :</strong> +20-50%
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-certificate text-success me-2"></i>
                            <strong>Spécialisation :</strong> +30-100%
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt text-info me-2"></i>
                            <strong>Localisation :</strong> ±20%
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-users text-primary me-2"></i>
                            <strong>Clientèle :</strong> Variable
                        </li>
                    </ul>
                    
                    <hr>
                    
                    <h6>Tarifs moyens par secteur :</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td>Conseil</td>
                                    <td><span class="badge bg-success">400-800€/j</span></td>
                                </tr>
                                <tr>
                                    <td>Développement</td>
                                    <td><span class="badge bg-primary">300-600€/j</span></td>
                                </tr>
                                <tr>
                                    <td>Design</td>
                                    <td><span class="badge bg-warning">250-500€/j</span></td>
                                </tr>
                                <tr>
                                    <td>Formation</td>
                                    <td><span class="badge bg-info">200-400€/j</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <small>Ces tarifs sont indicatifs et varient selon le marché.</small>
                    </div>
                </div>
            </div>
            
            <!-- Résultats -->
            <div id="resultsCard" class="card mt-3" style="display: none;">
                <div class="card-header bg-info text-white">
                    <h5><i class="fas fa-target me-2"></i>Vos tarifs recommandés</h5>
                </div>
                <div class="card-body" id="resultsContent">
                    <!-- Les résultats seront affichés ici via JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('tarificationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Affichage du loading
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Calcul en cours...';
    submitBtn.disabled = true;
    
    fetch('/calculators/tarification', {
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
        <div class="alert alert-info mb-3">
            <strong>CA nécessaire :</strong> ${formatCurrency(results.ca_necessaire)}<br>
            <small>Taux de charges estimé : ${results.taux_charges_estime.toFixed(1)}%</small>
        </div>
        
        <div class="row text-center mb-3">
            <div class="col-4">
                <div class="border rounded p-2 bg-light">
                    <h6 class="text-muted small">Tarif horaire</h6>
                    <h4 class="text-primary">${formatCurrency(results.tarif_horaire)}</h4>
                    <small class="text-muted">HT</small>
                </div>
            </div>
            <div class="col-4">
                <div class="border rounded p-2 bg-light">
                    <h6 class="text-muted small">Tarif journalier</h6>
                    <h4 class="text-success">${formatCurrency(results.tarif_journalier)}</h4>
                    <small class="text-muted">HT</small>
                </div>
            </div>
            <div class="col-4">
                <div class="border rounded p-2 bg-light">
                    <h6 class="text-muted small">Tarif mensuel</h6>
                    <h4 class="text-info">${formatCurrency(results.tarif_mensuel)}</h4>
                    <small class="text-muted">HT</small>
                </div>
            </div>
        </div>
        
        <h6>Objectifs de chiffre d'affaires :</h6>
        <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between">
                <span>Par jour</span>
                <strong>${formatCurrency(results.objectif_ca_journalier)}</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <span>Par semaine</span>
                <strong>${formatCurrency(results.objectif_ca_hebdomadaire)}</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <span>Par mois</span>
                <strong>${formatCurrency(results.objectif_ca_mensuel)}</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between bg-light">
                <span><strong>Par an</strong></span>
                <strong class="text-success">${formatCurrency(results.ca_necessaire)}</strong>
            </li>
        </ul>
        
        <div class="alert alert-success mt-3">
            <i class="fas fa-chart-line me-2"></i>
            <strong>Volume de travail :</strong> ${results.heures_annuelles} heures par an
        </div>
        
        <div class="mt-3">
            <button class="btn btn-outline-info btn-sm me-2" onclick="saveCalculation()">
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

