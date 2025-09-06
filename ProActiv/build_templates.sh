#!/bin/bash

echo "🎨 Création des templates multilingues..."

# Layout principal avec switch de langue
cat > templates/layouts/main.php << 'LAYOUT_END'
<!DOCTYPE html>
<html lang="<?= $this->lang->getCurrentLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'ProActiv') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="fas fa-chart-line me-2"></i>ProActiv
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/"><?= $this->lang->translate('home') ?></a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/dashboard"><?= $this->lang->translate('dashboard') ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/calculators"><?= $this->lang->translate('calculators') ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/forum"><?= $this->lang->translate('forum') ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/documents"><?= $this->lang->translate('documents') ?></a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/about"><?= $this->lang->translate('about') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact"><?= $this->lang->translate('contact') ?></a>
                    </li>
                </ul>
                
                <!-- Switch de langue -->
                <div class="navbar-nav me-3">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-globe me-1"></i>
                            <?= strtoupper($this->lang->getCurrentLanguage()) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?lang=fr">🇫🇷 Français</a></li>
                            <li><a class="dropdown-item" href="?lang=en">🇬🇧 English</a></li>
                            <li><a class="dropdown-item" href="?lang=es">��🇸 Español</a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Menu utilisateur -->
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i>
                                <?= htmlspecialchars($_SESSION['user_email'] ?? 'Utilisateur') ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/profile">
                                    <i class="fas fa-user me-2"></i><?= $this->lang->translate('profile') ?>
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/logout">
                                    <i class="fas fa-sign-out-alt me-2"></i><?= $this->lang->translate('logout') ?>
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/login"><?= $this->lang->translate('login') ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/register"><?= $this->lang->translate('register') ?></a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Messages flash -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['flash_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
    <?php endif; ?>

    <!-- Contenu principal -->
    <main class="container-fluid px-0">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>ProActiv</h5>
                    <p>Plateforme complète pour auto-entrepreneurs</p>
                </div>
                <div class="col-md-3">
                    <h6>Liens rapides</h6>
                    <ul class="list-unstyled">
                        <li><a href="/calculators" class="text-light"><?= $this->lang->translate('calculators') ?></a></li>
                        <li><a href="/forum" class="text-light"><?= $this->lang->translate('forum') ?></a></li>
                        <li><a href="/documents" class="text-light"><?= $this->lang->translate('documents') ?></a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="/about" class="text-light"><?= $this->lang->translate('about') ?></a></li>
                        <li><a href="/contact" class="text-light"><?= $this->lang->translate('contact') ?></a></li>
                        <li><a href="/help" class="text-light">Aide</a></li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; 2025 ProActiv. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/app.js"></script>
</body>
</html>
LAYOUT_END

# Template de connexion
cat > templates/auth/login.php << 'LOGIN_END'
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        <?= $this->lang->translate('login') ?>
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="/login">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="form-text">Utilisez demo@proactiv.fr pour tester</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="form-text">Utilisez demo123 pour tester</div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Se souvenir de moi</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                        </button>
                    </form>
                    
                    <hr>
                    <div class="text-center">
                        <a href="/register">Pas encore de compte ? S'inscrire</a><br>
                        <a href="/forgot-password">Mot de passe oublié ?</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
LOGIN_END

echo "✅ Templates Auth et Layout créés"

