<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur serveur - ProActiv</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 text-center">
                <div class="error-page">
                    <h1 class="display-1 fw-bold text-danger">500</h1>
                    <h2 class="mb-4">Erreur serveur</h2>
                    <p class="lead mb-4">
                        Une erreur technique est survenue. Nos équipes ont été prévenues et travaillent à la résoudre.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="/ProActiv/" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Retour à l'accueil
                        </a>
                        <button onclick="window.location.reload()" class="btn btn-outline-secondary">
                            <i class="fas fa-refresh me-2"></i>Réessayer
                        </button>
                    </div>
                    <div class="mt-4">
                        <small class="text-muted">
                            Si le problème persiste, contactez le support : 
                            <a href="mailto:support@proactiv.fr">support@proactiv.fr</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
