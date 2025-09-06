<?php $this->layout('layouts/main', ['title' => $title]) ?>

<div class="container-fluid mt-4">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/ProActiv/superadmin">Super Admin</a></li>
                            <li class="breadcrumb-item active">Gestion des utilisateurs</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0"><i class="fas fa-users text-primary me-2"></i>Gestion des utilisateurs</h1>
                </div>
                <div class="btn-group">
                    <button class="btn btn-outline-primary" onclick="exportUsers()">
                        <i class="fas fa-download me-1"></i>Exporter
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fas fa-user-plus me-1"></i>Nouvel utilisateur
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= number_format($pagination['total_items']) ?></h4>
                            <p class="mb-0">Total utilisateurs</p>
                        </div>
                        <i class="fas fa-users fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= count(array_filter($users, fn($u) => $u['status'] === 'active')) ?></h4>
                            <p class="mb-0">Actifs</p>
                        </div>
                        <i class="fas fa-user-check fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= count(array_filter($users, fn($u) => $u['subscription']['plan_slug'] ?? '' === 'community')) ?></h4>
                            <p class="mb-0">Communautaire</p>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= count(array_filter($users, fn($u) => $u['subscription']['plan_slug'] ?? '' === 'standard')) ?></h4>
                            <p class="mb-0">Standard</p>
                        </div>
                        <i class="fas fa-star fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Recherche</label>
                            <input type="text" class="form-control" name="search" 
                                   value="<?= htmlspecialchars($pagination['search']) ?>" 
                                   placeholder="Email, nom, prénom...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Statut</label>
                            <select class="form-select" name="status">
                                <option value="">Tous</option>
                                <option value="active">Actif</option>
                                <option value="suspended">Suspendu</option>
                                <option value="pending">En attente</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Plan</label>
                            <select class="form-select" name="plan">
                                <option value="">Tous</option>
                                <option value="community">Communautaire</option>
                                <option value="standard">Standard</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary d-block">
                                <i class="fas fa-search me-1"></i>Filtrer
                            </button>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <a href="/ProActiv/superadmin/users" class="btn btn-outline-secondary d-block">
                                <i class="fas fa-times me-1"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des utilisateurs -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Liste des utilisateurs
                        <span class="badge bg-secondary ms-2"><?= $pagination['total_items'] ?></span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Utilisateur</th>
                                    <th>Statut</th>
                                    <th>Plan</th>
                                    <th>Inscription</th>
                                    <th>Activité</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle me-3">
                                                    <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h6>
                                                    <small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : ($user['status'] === 'suspended' ? 'danger' : 'warning') ?>">
                                                <?= ucfirst($user['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (isset($user['subscription']) && $user['subscription']): ?>
                                                <span class="badge bg-<?= $user['subscription']['plan_slug'] === 'standard' ? 'primary' : 'secondary' ?>">
                                                    <?= ucfirst($user['subscription']['plan_name'] ?? $user['subscription']['plan_slug']) ?>
                                                </span>
                                                <?php if ($user['subscription']['expires_at']): ?>
                                                    <br><small class="text-muted">
                                                        Expire le <?= date('d/m/Y', strtotime($user['subscription']['expires_at'])) ?>
                                                    </small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark">Aucun</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></small>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <div><i class="fas fa-file-alt me-1"></i><?= $user['documents_count'] ?? 0 ?> docs</div>
                                                <div><i class="fas fa-calculator me-1"></i><?= $user['calculations_count'] ?? 0 ?> calculs</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="/ProActiv/superadmin/users/<?= $user['id'] ?>" 
                                                   class="btn btn-outline-primary" title="Voir détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button class="btn btn-outline-warning" 
                                                        onclick="toggleUserStatus(<?= $user['id'] ?>, '<?= $user['status'] === 'active' ? 'suspended' : 'active' ?>')"
                                                        title="<?= $user['status'] === 'active' ? 'Suspendre' : 'Activer' ?>">
                                                    <i class="fas fa-<?= $user['status'] === 'active' ? 'ban' : 'check' ?>"></i>
                                                </button>
                                                <button class="btn btn-outline-info" 
                                                        onclick="showPasswordModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['email']) ?>')"
                                                        title="Changer mot de passe">
                                                    <i class="fas fa-key"></i>
                                                </button>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-secondary dropdown-toggle" 
                                                            data-bs-toggle="dropdown" title="Plus d'actions">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#" onclick="loginAsUser(<?= $user['id'] ?>)">
                                                            <i class="fas fa-sign-in-alt me-2"></i>Se connecter en tant que
                                                        </a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="sendEmail(<?= $user['id'] ?>)">
                                                            <i class="fas fa-envelope me-2"></i>Envoyer email
                                                        </a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteUser(<?= $user['id'] ?>)">
                                                            <i class="fas fa-trash me-2"></i>Supprimer
                                                        </a></li>
                                                    </ul>
                                                </div>
                                            </div>
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

    <!-- Pagination -->
    <?php if ($pagination['total_pages'] > 1): ?>
        <div class="row mt-4">
            <div class="col-12">
                <nav aria-label="Pagination utilisateurs">
                    <ul class="pagination justify-content-center">
                        <?php if ($pagination['current_page'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $pagination['current_page'] - 1 ?>&search=<?= urlencode($pagination['search']) ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                            <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($pagination['search']) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $pagination['current_page'] + 1 ?>&search=<?= urlencode($pagination['search']) ?>">
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

<!-- Modal changement de mot de passe -->
<div class="modal fade" id="passwordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Changer le mot de passe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="passwordForm">
                    <input type="hidden" id="userId" name="user_id">
                    <div class="mb-3">
                        <label class="form-label">Utilisateur</label>
                        <input type="text" class="form-control" id="userEmail" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="newPassword" name="new_password" required minlength="6">
                        <div class="form-text">Minimum 6 caractères</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="confirmPassword" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="changePassword()">Changer le mot de passe</button>
            </div>
        </div>
    </div>
</div>

<script>
// Changer le statut d'un utilisateur
function toggleUserStatus(userId, newStatus) {
    if (!confirm(`Êtes-vous sûr de vouloir ${newStatus === 'suspended' ? 'suspendre' : 'activer'} cet utilisateur ?`)) {
        return;
    }
    
    fetch('/ProActiv/superadmin/users/toggle-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `user_id=${userId}&status=${newStatus}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur: ' + data.error);
        }
    })
    .catch(error => {
        alert('Erreur de communication: ' + error);
    });
}

// Afficher le modal de changement de mot de passe
function showPasswordModal(userId, userEmail) {
    document.getElementById('userId').value = userId;
    document.getElementById('userEmail').value = userEmail;
    document.getElementById('newPassword').value = '';
    document.getElementById('confirmPassword').value = '';
    new bootstrap.Modal(document.getElementById('passwordModal')).show();
}

// Changer le mot de passe
function changePassword() {
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const userId = document.getElementById('userId').value;
    
    if (newPassword !== confirmPassword) {
        alert('Les mots de passe ne correspondent pas');
        return;
    }
    
    if (newPassword.length < 6) {
        alert('Le mot de passe doit contenir au moins 6 caractères');
        return;
    }
    
    fetch('/ProActiv/superadmin/users/force-password', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `user_id=${userId}&new_password=${encodeURIComponent(newPassword)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('passwordModal')).hide();
            alert('Mot de passe modifié avec succès');
        } else {
            alert('Erreur: ' + data.error);
        }
    })
    .catch(error => {
        alert('Erreur de communication: ' + error);
    });
}

// Exporter les utilisateurs
function exportUsers() {
    window.open('/ProActiv/superadmin/users/export', '_blank');
}

// Se connecter en tant qu'utilisateur
function loginAsUser(userId) {
    if (confirm('Êtes-vous sûr de vouloir vous connecter en tant que cet utilisateur ?')) {
        window.open(`/ProActiv/superadmin/users/login-as/${userId}`, '_blank');
    }
}

// Envoyer un email
function sendEmail(userId) {
    // TODO: Implémenter l'envoi d'email
    alert('Fonctionnalité à implémenter');
}

// Supprimer un utilisateur
function deleteUser(userId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer définitivement cet utilisateur ? Cette action est irréversible.')) {
        // TODO: Implémenter la suppression
        alert('Fonctionnalité à implémenter');
    }
}
</script>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 14px;
}

.table td {
    vertical-align: middle;
}

.opacity-75 {
    opacity: 0.75;
}

.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.badge {
    font-size: 0.75em;
}
</style>

