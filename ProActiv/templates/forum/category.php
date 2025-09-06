<?php
/**
 * Vue d'une catégorie du forum
 */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/forum">Forum</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($category['name']) ?></li>
                </ol>
            </nav>

            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4><i class="<?= $category['icon'] ?> me-2"></i><?= htmlspecialchars($category['name']) ?></h4>
                        <p class="mb-0"><?= htmlspecialchars($category['description']) ?></p>
                    </div>
                    <div>
                        <a href="/forum/new-topic?category=<?= $category['id'] ?>" class="btn btn-light">
                            <i class="fas fa-plus me-1"></i>Nouveau sujet
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistiques de la catégorie -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center bg-light">
                                <div class="card-body">
                                    <h5 class="text-primary"><?= $category['topics_count'] ?></h5>
                                    <small>Sujets</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center bg-light">
                                <div class="card-body">
                                    <h5 class="text-success"><?= count($topics) * 5 ?></h5>
                                    <small>Messages</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center bg-light">
                                <div class="card-body">
                                    <h5 class="text-warning"><?= count($topics) * 2 ?></h5>
                                    <small>Participants</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center bg-light">
                                <div class="card-body">
                                    <h5 class="text-info">Aujourd'hui</h5>
                                    <small>Dernière activité</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des sujets -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-comment me-1"></i>Sujet</th>
                                    <th><i class="fas fa-user me-1"></i>Auteur</th>
                                    <th><i class="fas fa-reply me-1"></i>Réponses</th>
                                    <th><i class="fas fa-eye me-1"></i>Vues</th>
                                    <th><i class="fas fa-clock me-1"></i>Dernière activité</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topics as $topic): ?>
                                    <tr>
                                        <td>
                                            <div>
                                                <a href="/forum/topic/<?= $topic['id'] ?>" class="text-decoration-none fw-bold">
                                                    <?= htmlspecialchars($topic['title']) ?>
                                                </a>
                                                <br>
                                                <small class="text-muted">
                                                    Créé le <?= date('d/m/Y à H:i', strtotime($topic['created_at'])) ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?= htmlspecialchars($topic['author']) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success"><?= $topic['replies'] ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?= $topic['views'] ?></span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('d/m/Y H:i', strtotime($topic['last_activity'])) ?>
                                            </small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (empty($topics)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun sujet dans cette catégorie</h5>
                            <p class="text-muted">Soyez le premier à créer un sujet !</p>
                            <a href="/forum/new-topic?category=<?= $category['id'] ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Créer le premier sujet
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

