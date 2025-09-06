<?php
/**
 * Vue principale du forum communautaire
 */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4><i class="fas fa-comments me-2"></i>Forum communautaire</h4>
                        <p class="mb-0">Échangez avec d'autres auto-entrepreneurs</p>
                    </div>
                    <div>
                        <a href="/forum/new-topic" class="btn btn-light">
                            <i class="fas fa-plus me-1"></i>Nouveau sujet
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Barre de recherche -->
                    <div class="row mb-4">
                        <div class="col-md-6 offset-md-3">
                            <form action="/forum/search" method="GET">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="q" placeholder="Rechercher dans le forum...">
                                    <button class="btn btn-outline-success" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Catégories -->
                    <h5><i class="fas fa-folder me-2"></i>Catégories</h5>
                    <div class="row mb-4">
                        <?php foreach ($categories as $category): ?>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="<?= $category['icon'] ?> fa-2x text-primary me-3"></i>
                                            <div>
                                                <h6 class="card-title mb-0">
                                                    <a href="/forum/category/<?= $category['id'] ?>" class="text-decoration-none">
                                                        <?= htmlspecialchars($category['name']) ?>
                                                    </a>
                                                </h6>
                                                <small class="text-muted"><?= $category['topics_count'] ?> sujets</small>
                                            </div>
                                        </div>
                                        <p class="card-text small text-muted">
                                            <?= htmlspecialchars($category['description']) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Sujets récents -->
                    <h5><i class="fas fa-clock me-2"></i>Sujets récents</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-comment me-1"></i>Sujet</th>
                                    <th><i class="fas fa-user me-1"></i>Auteur</th>
                                    <th><i class="fas fa-folder me-1"></i>Catégorie</th>
                                    <th><i class="fas fa-reply me-1"></i>Réponses</th>
                                    <th><i class="fas fa-calendar me-1"></i>Créé le</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_topics as $topic): ?>
                                    <tr>
                                        <td>
                                            <a href="/forum/topic/<?= $topic['id'] ?>" class="text-decoration-none fw-bold">
                                                <?= htmlspecialchars($topic['title']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?= htmlspecialchars($topic['author']) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary"><?= htmlspecialchars($topic['category']) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success"><?= $topic['replies'] ?></span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('d/m/Y H:i', strtotime($topic['created_at'])) ?>
                                            </small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
