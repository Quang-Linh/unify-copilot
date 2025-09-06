#!/bin/bash

echo "🎯 Finalisation complète de ProActiv..."

# Page d'inscription
cat > templates/auth/register.php << 'REGISTER_END'
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>
                        <?= $this->lang->translate('register') ?>
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="/register" id="registerForm">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        
                        <div class="mb-3">
                            <label for="firstname" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="lastname" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="8">
                            <div class="form-text">Minimum 8 caractères</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                J'accepte les <a href="/terms" target="_blank">conditions d'utilisation</a>
                            </label>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="newsletter" name="newsletter">
                            <label class="form-check-label" for="newsletter">
                                Je souhaite recevoir la newsletter ProActiv
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-user-plus me-2"></i>S'inscrire
                        </button>
                    </form>
                    
                    <hr>
                    <div class="text-center">
                        <a href="/login">Déjà un compte ? Se connecter</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;
    
    if (password !== passwordConfirm) {
        e.preventDefault();
        alert('Les mots de passe ne correspondent pas');
        return false;
    }
});
</script>
REGISTER_END

# Template calculateurs
cat > templates/calculators/index.php << 'CALCULATORS_END'
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
CALCULATORS_END

# Page Forum
cat > templates/forum/index.php << 'FORUM_END'
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-comments me-2"></i>
            <?= $this->lang->translate('forum') ?>
        </h1>
        <a href="/forum/new-topic" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouveau sujet
        </a>
    </div>
    
    <!-- Statistiques du forum -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="text-primary">156</h5>
                    <small>Sujets</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="text-success">1,247</h5>
                    <small>Messages</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="text-warning">89</h5>
                    <small>Membres actifs</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="text-info">24h</h5>
                    <small>Dernière activité</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Liste des sujets -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Sujets récents</h5>
        </div>
        <div class="list-group list-group-flush">
            <?php foreach ($topics as $topic): ?>
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">
                            <a href="/forum/topic/<?= $topic['id'] ?>" class="text-decoration-none">
                                <?= htmlspecialchars($topic['title']) ?>
                            </a>
                        </h6>
                        <small><?= date('d/m/Y', strtotime($topic['created_at'])) ?></small>
                    </div>
                    <p class="mb-1">
                        Par <strong><?= htmlspecialchars($topic['author']) ?></strong>
                    </p>
                    <small>
                        <i class="fas fa-comments me-1"></i><?= $topic['replies'] ?> réponses
                    </small>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
FORUM_END

echo "✅ Templates complets créés (Register, Calculators, Forum)"

