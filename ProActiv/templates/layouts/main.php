<!DOCTYPE html>
<html lang="<?= $lang->getCurrentLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'ProActiv') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="<?= $theme['css_class'] ?? 'theme-standard' ?>">

    <?php if (isset($theme['show_banner']) && $theme['show_banner']): ?>
    <!-- Bannière version communautaire -->
    <div class="community-banner">
        <div class="container">
            <i class="fas fa-info-circle me-2"></i>
            <?= $theme['banner_text'] ?>
            <span class="badge">Essai gratuit</span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="/ProActiv/">
                <img src="/ProActiv/assets/images/linhstudio-logo.png" alt="LinhStudio" height="32" class="me-2">
                ProActiv
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/ProActiv/"><?= $lang->translate('home') ?></a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/ProActiv/dashboard"><?= $lang->translate('dashboard') ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/ProActiv/calculators"><?= $lang->translate('calculators') ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/ProActiv/forum"><?= $lang->translate('forum') ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/ProActiv/documents"><?= $lang->translate('documents') ?></a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/ProActiv/about"><?= $lang->translate('about') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/ProActiv/contact"><?= $lang->translate('contact') ?></a>
                    </li>
                </ul>
                
                <!-- Switch de langue -->
                <div class="dropdown me-3">
                    <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <?= $lang->getSupportedLanguages()[$lang->getCurrentLanguage()]['flag'] ?? '🇫🇷' ?>
                        <?= strtoupper($lang->getCurrentLanguage()) ?>
                    </button>
                    <ul class="dropdown-menu">
                        <?php foreach ($lang->getSupportedLanguages() as $code => $info): ?>
                            <li>
                                <a class="dropdown-item" href="/ProActiv/language/<?= $code ?>">
                                    <?= $info['flag'] ?> <?= $info['name'] ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
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
                                <li><a class="dropdown-item" href="/ProActiv/profile">
                                    <i class="fas fa-user me-2"></i><?= $lang->translate('profile') ?>
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/ProActiv/logout">
                                    <i class="fas fa-sign-out-alt me-2"></i><?= $lang->translate('logout') ?>
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/ProActiv/login"><?= $lang->translate('login') ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/ProActiv/register"><?= $lang->translate('register') ?></a>
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
                <div class="col-md-4">
                    <div class="d-flex align-items-center mb-3">
                        <img src="/assets/images/linhstudio-logo.png" alt="LinhStudio" height="24" class="me-2">
                        <h5 class="mb-0">ProActiv</h5>
                    </div>
                    <p class="mb-2">Plateforme complète pour auto-entrepreneurs</p>
                    <p class="small text-muted mb-0">
                        <i class="fas fa-lightbulb me-1"></i>
                        <?= $this->lang->translate('designed_by') ?> <strong>LinhStudio</strong>
                    </p>
                </div>
                <div class="col-md-3">
                    <h6>Liens rapides</h6>
                    <ul class="list-unstyled">
                        <li><a href="/calculators" class="text-light"><?= $this->lang->translate('calculators') ?></a></li>
                        <li><a href="/forum" class="text-light"><?= $this->lang->translate('forum') ?></a></li>
                        <li><a href="/documents" class="text-light"><?= $this->lang->translate('documents') ?></a></li>
                        <li><a href="/dashboard" class="text-light"><?= $this->lang->translate('dashboard') ?></a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6><?= $this->lang->translate('support') ?></h6>
                    <ul class="list-unstyled">
                        <li><a href="/ProActiv/about" class="text-light"><?= $lang->translate('about') ?></a></li>
                        <li><a href="/ProActiv/contact" class="text-light"><?= $lang->translate('contact') ?></a></li>
                        <li><a href="mailto:support@linhstudio.click" class="text-light">
                            <i class="fas fa-envelope me-1"></i>support@linhstudio.click
                        </a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h6>Langues</h6>
                    <div class="d-flex flex-wrap">
                        <?php foreach (array_slice($lang->getSupportedLanguages(), 0, 6) as $code => $info): ?>
                            <a href="/ProActiv/language/<?= $code ?>" class="text-light me-2 mb-1" title="<?= $info['name'] ?>">
                                <?= $info['flag'] ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">
                        &copy; 2025 <strong>LinhStudio</strong>. <?= $lang->translate('all_rights_reserved') ?>
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 small text-muted">
                        <?= $lang->translate('support') ?> : 
                        <a href="mailto:support@linhstudio.click" class="text-light">support@linhstudio.click</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/ProActiv/assets/js/app.js"></script>
</body>
</html>
