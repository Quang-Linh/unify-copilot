<?php $lang = $lang ?? new LanguageService(); ?>

<div class="container py-5">
    <!-- En-tête -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 mb-3">
                <i class="fas fa-globe text-primary me-3"></i>
                Support Multi-Pays
            </h1>
            <p class="lead text-muted">
                ProActiv s'adapte à la législation de votre pays pour vous offrir des calculs précis et conformes
            </p>
        </div>
    </div>

    <!-- Pays actuellement sélectionné -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-primary shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Pays actuellement sélectionné
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <span class="display-1"><?= $current_country['flag'] ?></span>
                        </div>
                        <div class="col-md-6">
                            <h3 class="mb-2"><?= htmlspecialchars($current_country['name']) ?></h3>
                            <p class="text-muted mb-2">
                                <strong>Statut juridique :</strong> <?= htmlspecialchars($current_country['legal_status']) ?>
                            </p>
                            <p class="text-muted mb-0">
                                <strong>Devise :</strong> <?= htmlspecialchars($current_country['currency']) ?> (<?= $current_country['currency_symbol'] ?>)
                            </p>
                        </div>
                        <div class="col-md-4">
                            <div class="d-grid">
                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#countryModal">
                                    <i class="fas fa-exchange-alt me-2"></i>
                                    Changer de pays
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparaison des pays -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-chart-bar text-success me-2"></i>
                Comparaison des législations
            </h2>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Pays</th>
                            <th>Statut juridique</th>
                            <th>Charges sociales (Services)</th>
                            <th>Seuil auto-entrepreneur</th>
                            <th>Devise</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($countries as $country): ?>
                            <tr class="<?= $country['code'] === $current_country['code'] ? 'table-primary' : '' ?>">
                                <td>
                                    <span class="me-2"><?= $country['flag'] ?></span>
                                    <strong><?= htmlspecialchars($country['name']) ?></strong>
                                    <?php if ($country['code'] === $current_country['code']): ?>
                                        <span class="badge bg-primary ms-2">Actuel</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($country['legal_status']) ?></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= number_format($country['social_charges']['services_bic'] * 100, 1) ?>%
                                    </span>
                                </td>
                                <td>
                                    <?= number_format($country['thresholds']['services']) ?> <?= $country['currency_symbol'] ?>
                                </td>
                                <td>
                                    <?= $country['currency'] ?> (<?= $country['currency_symbol'] ?>)
                                </td>
                                <td>
                                    <?php if ($country['code'] !== $current_country['code']): ?>
                                        <a href="/ProActiv/country/<?= $country['code'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-arrow-right me-1"></i>Sélectionner
                                        </a>
                                    <?php else: ?>
                                        <span class="text-success">
                                            <i class="fas fa-check-circle"></i> Sélectionné
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Détails par pays -->
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-info-circle text-info me-2"></i>
                Détails par pays
            </h2>
            
            <div class="accordion" id="countryAccordion">
                <?php foreach ($countries as $index => $country): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?= $country['code'] ?>">
                            <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#collapse<?= $country['code'] ?>" 
                                    aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" 
                                    aria-controls="collapse<?= $country['code'] ?>">
                                <span class="me-3"><?= $country['flag'] ?></span>
                                <strong><?= htmlspecialchars($country['name']) ?></strong>
                                <?php if ($country['code'] === $current_country['code']): ?>
                                    <span class="badge bg-primary ms-auto me-3">Pays actuel</span>
                                <?php endif; ?>
                            </button>
                        </h2>
                        <div id="collapse<?= $country['code'] ?>" 
                             class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" 
                             aria-labelledby="heading<?= $country['code'] ?>" 
                             data-bs-parent="#countryAccordion">
                            <div class="accordion-body">
                                <div class="row">
                                    <!-- Informations générales -->
                                    <div class="col-md-6">
                                        <h5><i class="fas fa-info me-2"></i>Informations générales</h5>
                                        <ul class="list-unstyled">
                                            <li><strong>Statut juridique :</strong> <?= htmlspecialchars($country['legal_status']) ?></li>
                                            <li><strong>Devise :</strong> <?= $country['currency'] ?> (<?= $country['currency_symbol'] ?>)</li>
                                            <li><strong>Seuil services :</strong> <?= number_format($country['thresholds']['services']) ?> <?= $country['currency_symbol'] ?></li>
                                            <li><strong>Seuil commerce :</strong> <?= number_format($country['thresholds']['commerce']) ?> <?= $country['currency_symbol'] ?></li>
                                        </ul>
                                    </div>
                                    
                                    <!-- Charges sociales -->
                                    <div class="col-md-6">
                                        <h5><i class="fas fa-calculator me-2"></i>Charges sociales</h5>
                                        <ul class="list-unstyled">
                                            <li><strong>Services BIC :</strong> 
                                                <span class="badge bg-info"><?= number_format($country['social_charges']['services_bic'] * 100, 1) ?>%</span>
                                            </li>
                                            <li><strong>Services BNC :</strong> 
                                                <span class="badge bg-info"><?= number_format($country['social_charges']['services_bnc'] * 100, 1) ?>%</span>
                                            </li>
                                            <li><strong>Commerce :</strong> 
                                                <span class="badge bg-success"><?= number_format($country['social_charges']['commerce'] * 100, 1) ?>%</span>
                                            </li>
                                            <li><strong>Artisanat :</strong> 
                                                <span class="badge bg-warning"><?= number_format($country['social_charges']['artisanat'] * 100, 1) ?>%</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <!-- Tranches d'imposition -->
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h5><i class="fas fa-chart-line me-2"></i>Tranches d'imposition</h5>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>De</th>
                                                        <th>À</th>
                                                        <th>Taux</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($country['tax_brackets'] as $bracket): ?>
                                                        <tr>
                                                            <td><?= number_format($bracket['min']) ?> <?= $country['currency_symbol'] ?></td>
                                                            <td>
                                                                <?= $bracket['max'] ? number_format($bracket['max']) . ' ' . $country['currency_symbol'] : 'Illimité' ?>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-secondary">
                                                                    <?= number_format($bracket['rate'] * 100, 1) ?>%
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if ($country['code'] !== $current_country['code']): ?>
                                    <div class="text-center mt-3">
                                        <a href="/ProActiv/country/<?= $country['code'] ?>" class="btn btn-primary">
                                            <i class="fas fa-arrow-right me-2"></i>
                                            Utiliser la législation de <?= htmlspecialchars($country['name']) ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal de sélection de pays -->
<div class="modal fade" id="countryModal" tabindex="-1" aria-labelledby="countryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="countryModalLabel">
                    <i class="fas fa-globe me-2"></i>
                    Choisir votre pays
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">
                    Sélectionnez votre pays pour adapter les calculs à votre législation locale.
                </p>
                
                <div class="row">
                    <?php foreach ($countries as $country): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 <?= $country['code'] === $current_country['code'] ? 'border-primary' : '' ?>">
                                <div class="card-body text-center">
                                    <div class="display-4 mb-3"><?= $country['flag'] ?></div>
                                    <h5 class="card-title"><?= htmlspecialchars($country['name']) ?></h5>
                                    <p class="card-text text-muted small">
                                        <?= htmlspecialchars($country['legal_status']) ?>
                                    </p>
                                    <?php if ($country['code'] === $current_country['code']): ?>
                                        <span class="badge bg-primary">Pays actuel</span>
                                    <?php else: ?>
                                        <a href="/ProActiv/country/<?= $country['code'] ?>" class="btn btn-outline-primary btn-sm">
                                            Sélectionner
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<style>
.accordion-button:not(.collapsed) {
    background-color: #e7f3ff;
    color: #0d6efd;
}

.table-primary {
    --bs-table-bg: #cfe2ff;
}

.display-1 {
    font-size: 4rem;
}

.card:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease-in-out;
}

.badge {
    font-size: 0.8em;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-détection du pays si pas encore sélectionné
    if (!localStorage.getItem('country_detected')) {
        detectUserCountry();
    }
    
    // Gestion des liens de changement de pays
    document.querySelectorAll('a[href*="/country/"]').forEach(link => {
        link.addEventListener('click', function(e) {
            const countryCode = this.href.split('/').pop();
            
            // Confirmation avant changement
            if (!confirm(`Voulez-vous vraiment changer pour la législation de ce pays ?`)) {
                e.preventDefault();
                return false;
            }
            
            // Affichage d'un loader
            showLoader('Changement de pays en cours...');
        });
    });
});

function detectUserCountry() {
    // Détection basée sur la langue du navigateur
    const userLang = navigator.language || navigator.userLanguage;
    const langCode = userLang.split('-')[0];
    
    const countryMapping = {
        'fr': 'FR',
        'de': 'DE', 
        'es': 'ES',
        'it': 'IT',
        'en': 'GB'
    };
    
    const detectedCountry = countryMapping[langCode] || 'FR';
    
    // Marquer comme détecté
    localStorage.setItem('country_detected', 'true');
    
    // Si différent du pays actuel, proposer le changement
    const currentCountry = '<?= $current_country['code'] ?>';
    if (detectedCountry !== currentCountry) {
        showCountryDetectionModal(detectedCountry);
    }
}

function showCountryDetectionModal(detectedCountry) {
    const countries = <?= json_encode($countries) ?>;
    const country = countries[detectedCountry];
    
    if (country) {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-globe me-2"></i>
                            Pays détecté
                        </h5>
                    </div>
                    <div class="modal-body text-center">
                        <div class="display-4 mb-3">${country.flag}</div>
                        <h4>${country.name}</h4>
                        <p class="text-muted">
                            Nous avons détecté que vous pourriez être en ${country.name}. 
                            Voulez-vous utiliser la législation de ce pays ?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Non, garder ${currentCountry}
                        </button>
                        <a href="/ProActiv/country/${detectedCountry}" class="btn btn-primary">
                            Oui, utiliser ${country.name}
                        </a>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        modal.addEventListener('hidden.bs.modal', function() {
            document.body.removeChild(modal);
        });
    }
}

function showLoader(message) {
    const loader = document.createElement('div');
    loader.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
    loader.style.cssText = 'background: rgba(0,0,0,0.8); z-index: 9999;';
    loader.innerHTML = `
        <div class="text-center text-white">
            <div class="spinner-border mb-3" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
            <div>${message}</div>
        </div>
    `;
    
    document.body.appendChild(loader);
}
</script>

