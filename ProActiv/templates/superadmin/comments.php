<?php $lang = $lang ?? new LanguageService(); ?>

<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-comments text-primary me-2"></i>
                        Gestion des commentaires
                    </h1>
                    <p class="text-muted mb-0">Modération et gestion des témoignages clients</p>
                </div>
                <div>
                    <a href="/ProActiv/comments" class="btn btn-outline-primary" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>
                        Voir la page publique
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="icon-circle bg-primary text-white me-3">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div>
                            <h3 class="mb-0"><?= $stats['total'] ?></h3>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="icon-circle bg-success text-white me-3">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h3 class="mb-0"><?= $stats['approved'] ?></h3>
                            <small class="text-muted">Approuvés</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="icon-circle bg-warning text-white me-3">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h3 class="mb-0"><?= $stats['pending'] ?></h3>
                            <small class="text-muted">En attente</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="icon-circle bg-warning text-white me-3">
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <h3 class="mb-0"><?= $stats['average_rating'] ?>/5</h3>
                            <small class="text-muted">Note moyenne</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="btn-group" role="group">
                                <a href="?status=" class="btn <?= !$current_status ? 'btn-primary' : 'btn-outline-primary' ?>">
                                    Tous (<?= $stats['total'] ?>)
                                </a>
                                <a href="?status=pending" class="btn <?= $current_status === 'pending' ? 'btn-warning' : 'btn-outline-warning' ?>">
                                    En attente (<?= $stats['pending'] ?>)
                                </a>
                                <a href="?status=approved" class="btn <?= $current_status === 'approved' ? 'btn-success' : 'btn-outline-success' ?>">
                                    Approuvés (<?= $stats['approved'] ?>)
                                </a>
                                <a href="?status=rejected" class="btn <?= $current_status === 'rejected' ? 'btn-danger' : 'btn-outline-danger' ?>">
                                    Rejetés (<?= $stats['rejected'] ?>)
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">
                                <?= $total_comments ?> commentaire(s) • Page <?= $current_page ?> sur <?= $total_pages ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des commentaires -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <?php if (empty($comments)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun commentaire trouvé</h5>
                            <p class="text-muted">
                                <?php if ($current_status): ?>
                                    Aucun commentaire avec le statut "<?= $current_status ?>"
                                <?php else: ?>
                                    Aucun commentaire n'a encore été soumis
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Utilisateur</th>
                                        <th>Commentaire</th>
                                        <th>Note</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($comments as $comment): ?>
                                        <tr id="comment-<?= $comment['id'] ?>">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="user-avatar me-3">
                                                        <?= strtoupper(substr($comment['user_name'], 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold"><?= htmlspecialchars($comment['user_name']) ?></div>
                                                        <small class="text-muted"><?= htmlspecialchars($comment['user_email']) ?></small>
                                                        <?php if (!empty($comment['user_activity'])): ?>
                                                            <br><small class="text-info"><?= htmlspecialchars($comment['user_activity']) ?></small>
                                                        <?php endif; ?>
                                                        <?php if (!empty($comment['user_location'])): ?>
                                                            <br><small class="text-secondary"><?= htmlspecialchars($comment['user_location']) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="comment-text" style="max-width: 300px;">
                                                    <p class="mb-1"><?= htmlspecialchars(substr($comment['comment'], 0, 150)) ?><?= strlen($comment['comment']) > 150 ? '...' : '' ?></p>
                                                    <?php if (strlen($comment['comment']) > 150): ?>
                                                        <button class="btn btn-sm btn-link p-0" onclick="showFullComment(<?= $comment['id'] ?>)">
                                                            Voir plus
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="rating">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fas fa-star <?= $i <= $comment['rating'] ? 'text-warning' : 'text-muted' ?>"></i>
                                                    <?php endfor; ?>
                                                    <br><small class="text-muted"><?= $comment['rating'] ?>/5</small>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = [
                                                    'pending' => 'warning',
                                                    'approved' => 'success',
                                                    'rejected' => 'danger'
                                                ][$comment['status']] ?? 'secondary';
                                                $statusText = [
                                                    'pending' => 'En attente',
                                                    'approved' => 'Approuvé',
                                                    'rejected' => 'Rejeté'
                                                ][$comment['status']] ?? $comment['status'];
                                                ?>
                                                <span class="badge bg-<?= $statusClass ?>">
                                                    <?= $statusText ?>
                                                </span>
                                                <?php if (isset($comment['is_featured']) && $comment['is_featured']): ?>
                                                    <br><span class="badge bg-info mt-1">
                                                        <i class="fas fa-star"></i> Mis en avant
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small>
                                                    <?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?>
                                                    <?php if (!empty($comment['moderated_at'])): ?>
                                                        <br><span class="text-muted">
                                                            Modéré le <?= date('d/m/Y H:i', strtotime($comment['moderated_at'])) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group-vertical btn-group-sm">
                                                    <?php if ($comment['status'] === 'pending'): ?>
                                                        <button class="btn btn-success btn-sm" onclick="moderateComment(<?= $comment['id'] ?>, 'approve')">
                                                            <i class="fas fa-check"></i> Approuver
                                                        </button>
                                                        <button class="btn btn-warning btn-sm" onclick="moderateComment(<?= $comment['id'] ?>, 'reject')">
                                                            <i class="fas fa-times"></i> Rejeter
                                                        </button>
                                                    <?php elseif ($comment['status'] === 'approved'): ?>
                                                        <button class="btn btn-warning btn-sm" onclick="moderateComment(<?= $comment['id'] ?>, 'reject')">
                                                            <i class="fas fa-times"></i> Rejeter
                                                        </button>
                                                        <button class="btn btn-info btn-sm" onclick="toggleFeature(<?= $comment['id'] ?>, <?= isset($comment['is_featured']) && $comment['is_featured'] ? 'false' : 'true' ?>)">
                                                            <i class="fas fa-star"></i> 
                                                            <?= isset($comment['is_featured']) && $comment['is_featured'] ? 'Retirer' : 'Mettre en avant' ?>
                                                        </button>
                                                    <?php elseif ($comment['status'] === 'rejected'): ?>
                                                        <button class="btn btn-success btn-sm" onclick="moderateComment(<?= $comment['id'] ?>, 'approve')">
                                                            <i class="fas fa-check"></i> Approuver
                                                        </button>
                                                    <?php endif; ?>
                                                    <button class="btn btn-danger btn-sm" onclick="deleteComment(<?= $comment['id'] ?>)">
                                                        <i class="fas fa-trash"></i> Supprimer
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="row mt-4">
            <div class="col-12">
                <nav aria-label="Pagination des commentaires">
                    <ul class="pagination justify-content-center">
                        <?php if ($current_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $current_page - 1 ?><?= $current_status ? '&status=' . $current_status : '' ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                            <li class="page-item <?= $i === $current_page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= $current_status ? '&status=' . $current_status : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($current_page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $current_page + 1 ?><?= $current_status ? '&status=' . $current_status : '' ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal pour voir le commentaire complet -->
<div class="modal fade" id="commentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Commentaire complet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="commentModalBody">
                <!-- Contenu du commentaire -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<style>
.icon-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1rem;
    flex-shrink: 0;
}

.comment-text {
    line-height: 1.4;
}

.rating i {
    font-size: 0.9rem;
}

.btn-group-vertical .btn {
    margin-bottom: 2px;
}

.table td {
    vertical-align: middle;
}
</style>

<script>
// Données des commentaires pour le modal
const commentsData = <?= json_encode(array_column($comments, null, 'id')) ?>;

function showFullComment(commentId) {
    const comment = commentsData[commentId];
    if (!comment) return;
    
    const modalBody = document.getElementById('commentModalBody');
    modalBody.innerHTML = `
        <div class="d-flex align-items-start mb-3">
            <div class="user-avatar me-3">
                ${comment.user_name.charAt(0).toUpperCase()}
            </div>
            <div class="flex-grow-1">
                <h6 class="mb-1">${comment.user_name}</h6>
                <small class="text-muted">${comment.user_email}</small>
                ${comment.user_activity ? `<br><small class="text-info">${comment.user_activity}</small>` : ''}
                ${comment.user_location ? `<br><small class="text-secondary">${comment.user_location}</small>` : ''}
            </div>
            <div class="rating">
                ${Array.from({length: 5}, (_, i) => 
                    `<i class="fas fa-star ${i < comment.rating ? 'text-warning' : 'text-muted'}"></i>`
                ).join('')}
            </div>
        </div>
        <blockquote class="blockquote">
            <p>"${comment.comment}"</p>
            <footer class="blockquote-footer">
                Soumis le ${new Date(comment.created_at).toLocaleDateString('fr-FR')}
            </footer>
        </blockquote>
    `;
    
    new bootstrap.Modal(document.getElementById('commentModal')).show();
}

function moderateComment(commentId, action) {
    const note = action === 'reject' ? prompt('Raison du rejet (optionnel):') : null;
    if (action === 'reject' && note === null) return; // Annulé
    
    const formData = new FormData();
    formData.append('comment_id', commentId);
    formData.append('action', action);
    if (note) formData.append('note', note);
    
    fetch('/ProActiv/superadmin/comments/moderate', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message || 'Action effectuée avec succès');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('danger', data.error || 'Erreur lors de l\'action');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur de communication avec le serveur');
    });
}

function toggleFeature(commentId, featured) {
    const order = featured === 'true' ? prompt('Ordre d\'affichage (0 = premier):') || '0' : '0';
    
    const formData = new FormData();
    formData.append('comment_id', commentId);
    formData.append('action', 'feature');
    formData.append('featured', featured);
    formData.append('order', order);
    
    fetch('/ProActiv/superadmin/comments/moderate', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message || 'Mise en avant mise à jour');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('danger', data.error || 'Erreur lors de la mise à jour');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur de communication avec le serveur');
    });
}

function deleteComment(commentId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer définitivement ce commentaire ?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('comment_id', commentId);
    formData.append('action', 'delete');
    
    fetch('/ProActiv/superadmin/comments/moderate', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message || 'Commentaire supprimé');
            document.getElementById(`comment-${commentId}`).remove();
        } else {
            showAlert('danger', data.error || 'Erreur lors de la suppression');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur de communication avec le serveur');
    });
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);
}
</script>

