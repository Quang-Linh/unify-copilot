<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-calculator me-2"></i>
                <?= $this->lang->translate('calculators') ?>
            </h1>
            <p class="lead">Outils de calcul pour auto-entrepreneurs</p>
        </div>
    </div>
    
    <div class="row">
        <!-- Calculateur charges sociales -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-percent me-2"></i>Charges sociales
                    </h5>
                </div>
                <div class="card-body">
                    <p>Calculez vos cotisations sociales selon votre régime et chiffre d'affaires.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Micro-entreprise</li>
                        <li><i class="fas fa-check text-success me-2"></i>Régime réel</li>
                        <li><i class="fas fa-check text-success me-2"></i>CFE incluse</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="/calculators/charges-sociales" class="btn btn-primary w-100">
                        <i class="fas fa-calculator me-2"></i>Calculer
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Calculateur impôts -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-coins me-2"></i>Impôts sur le revenu
                    </h5>
                </div>
                <div class="card-body">
                    <p>Estimez votre impôt sur le revenu et optimisez votre fiscalité.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Abattement forfaitaire</li>
                        <li><i class="fas fa-check text-success me-2"></i>Tranches d'imposition</li>
                        <li><i class="fas fa-check text-success me-2"></i>Simulateur avancé</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="/calculators/impots" class="btn btn-success w-100">
                        <i class="fas fa-calculator me-2"></i>Calculer
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Calculateur revenus nets -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-euro-sign me-2"></i>Revenus nets
                    </h5>
                </div>
                <div class="card-body">
                    <p>Calculez votre revenu net après charges et impôts.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Vue d'ensemble</li>
                        <li><i class="fas fa-check text-success me-2"></i>Prévisionnel annuel</li>
                        <li><i class="fas fa-check text-success me-2"></i>Optimisations</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="/calculators/revenus" class="btn btn-warning w-100">
                        <i class="fas fa-calculator me-2"></i>Calculer
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- API Documentation -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-code me-2"></i>API des calculateurs
                    </h5>
                </div>
                <div class="card-body">
                    <p>Intégrez nos calculateurs dans vos applications via notre API REST.</p>
                    
                    <h6>Endpoint de calcul :</h6>
                    <div class="bg-light p-3 rounded">
                        <code>POST /api/calculate</code>
                    </div>
                    
                    <h6 class="mt-3">Exemple de requête :</h6>
                    <pre class="bg-dark text-light p-3 rounded"><code>{
  "type": "charges",
  "ca": 50000,
  "regime": "micro"
}</code></pre>

                    <div class="mt-3">
                        <button class="btn btn-outline-primary" onclick="testAPI()">
                            <i class="fas fa-play me-2"></i>Tester l'API
                        </button>
                        <span class="ms-3">
                            <small class="text-muted">Token JWT requis pour l'authentification</small>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function testAPI() {
    try {
        const response = await fetch('/api/calculate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer <?= $_SESSION["jwt_token"] ?? "" ?>'
            },
            body: JSON.stringify({
                type: 'charges',
                ca: 50000,
                regime: 'micro'
            })
        });
        
        const result = await response.json();
        alert('Résultat API: ' + JSON.stringify(result, null, 2));
    } catch (error) {
        alert('Erreur API: ' + error.message);
    }
}
</script>
