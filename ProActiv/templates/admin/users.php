<?php
/**
 * Gestion des utilisateurs - Administration
 */
?>

<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-users me-2 text-primary"></i>Gestion des utilisateurs</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/admin">Administration</a></li>
                            <li class="breadcrumb-item active">Utilisateurs</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <button class="btn btn-success" onclick="exportUsers()">
                        <i class="fas fa-download me-1"></i>Exporter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques utilisateurs -->
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body text-center">
                    <h3 class="mb-1"><?= number_format($user_stats['total']) ?></h3>
                    <small>Total utilisateurs</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body text-center">
                    <h3 class="mb-1"><?= number_format($user_stats['active']) ?></h3>
                    <small>Actifs</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body text-center">
                    <h3 class="mb-1"><?= number_format($user_stats['inactive']) ?></h3>
                    <small>Inactifs</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body text-center">
                    <h3 class="mb-1"><?= number_format($user_stats['new_this_month']) ?></h3>
                    <small>Nouveaux ce mois</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm bg-secondary text-white">
                <div class="card-body text-center">
                    <h3 class="mb-1"><?= number_format($user_stats['premium']) ?></h3>
                    <small>Premium</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" placeholder="Rechercher un utilisateur..." id="searchUsers">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="filterStatus">
                                <option value="">Tous les statuts</option>
                                <option value="active">Actifs</option>
                                <option value="inactive">Inactifs</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="filterRole">
                                <option value="">Tous les rôles</option>
                                <option value="user">Utilisateur</option>
                                <option value="admin">Administrateur</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-primary w-100" onclick="applyFilters()">
                                <i class="fas fa-filter me-1"></i>Filtrer
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                                <i class="fas fa-times me-1"></i>Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des utilisateurs -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">Liste des utilisateurs</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Utilisateur</th>
                                    <th>Email</th>
                                    <th>Rôle</th>
                                    <th>Statut</th>
                                    <th>Dernière connexion</th>
                                    <th>Documents</th>
                                    <th>Calculs</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><strong>#<?= $user['id'] ?></strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                    <i class="fas fa-user fa-sm"></i>
                                                </div>
                                                <div>
                                                    <strong><?= htmlspecialchars($user['name']) ?></strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td>
                                            <?php if ($user['role'] === 'admin'): ?>
                                                <span class="badge bg-danger">Administrateur</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">Utilisateur</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($user['status'] === 'active'): ?>
                                                <span class="badge bg-success">Actif</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Inactif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('d/m/Y H:i', strtotime($user['last_login'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?= $user['documents_count'] ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning"><?= $user['calculations_count'] ?></span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" onclick="viewUser(<?= $user['id'] ?>)" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-warning" onclick="editUser(<?= $user['id'] ?>)" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <?php if ($user['status'] === 'active'): ?>
                                                    <button class="btn btn-outline-danger" onclick="suspendUser(<?= $user['id'] ?>)" title="Suspendre">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-outline-success" onclick="activateUser(<?= $user['id'] ?>)" title="Activer">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Affichage de <?= count($users) ?> utilisateurs sur <?= number_format($user_stats['total']) ?>
                        </small>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item disabled">
                                    <span class="page-link">Précédent</span>
                                </li>
                                <li class="page-item active">
                                    <span class="page-link">1</span>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">2</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">3</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Suivant</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewUser(userId) {
    window.location.href = `/admin/users/${userId}`;
}

function editUser(userId) {
    window.location.href = `/admin/users/${userId}/edit`;
}

function suspendUser(userId) {
    if (confirm('Êtes-vous sûr de vouloir suspendre cet utilisateur ?')) {
        fetch(`/admin/users/${userId}/suspend`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Utilisateur suspendu avec succès');
                location.reload();
            } else {
                alert('Erreur: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue');
        });
    }
}

function activateUser(userId) {
    if (confirm('Êtes-vous sûr de vouloir activer cet utilisateur ?')) {
        fetch(`/admin/users/${userId}/activate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Utilisateur activé avec succès');
                location.reload();
            } else {
                alert('Erreur: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue');
        });
    }
}

function exportUsers() {
    window.location.href = '/admin/users/export';
}

function applyFilters() {
    const search = document.getElementById('searchUsers').value;
    const status = document.getElementById('filterStatus').value;
    const role = document.getElementById('filterRole').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (status) params.append('status', status);
    if (role) params.append('role', role);
    
    window.location.href = '/admin/users?' + params.toString();
}

function resetFilters() {
    window.location.href = '/admin/users';
}

// Recherche en temps réel
document.getElementById('searchUsers').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>

