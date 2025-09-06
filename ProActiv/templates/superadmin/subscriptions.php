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
                            <li class="breadcrumb-item active">Gestion des abonnements</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0"><i class="fas fa-credit-card text-primary me-2"></i>Gestion des abonnements</h1>
                </div>
                <div class="btn-group">
                    <button class="btn btn-outline-primary" onclick="exportSubscriptions()">
                        <i class="fas fa-download me-1"></i>Exporter
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubscriptionModal">
                        <i class="fas fa-plus me-1"></i>Nouvel abonnement
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques des abonnements -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= $stats['community_count'] ?></h4>
                            <p class="mb-0">Communautaire</p>
                            <small>Gratuit</small>
                        </div>
                        <i class="fas fa-users fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= $stats['standard_count'] ?></h4>
                            <p class="mb-0">Standard</p>
                            <small>5€/mois</small>
                        </div>
                        <i class="fas fa-star fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= number_format($stats['monthly_revenue'], 2) ?>€</h4>
                            <p class="mb-0">Revenus mensuels</p>
                            <small>Récurrents</small>
                        </div>
                        <i class="fas fa-euro-sign fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= $stats['expiring_soon'] ?></h4>
                            <p class="mb-0">Expirent bientôt</p>
                            <small>< 7 jours</small>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique des revenus -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Évolution des revenus
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Répartition des plans
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="plansChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Plan</label>
                            <select class="form-select" name="plan">
                                <option value="">Tous les plans</option>
                                <option value="community" <?= $filters['plan'] === 'community' ? 'selected' : '' ?>>Communautaire</option>
                                <option value="standard" <?= $filters['plan'] === 'standard' ? 'selected' : '' ?>>Standard</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Statut</label>
                            <select class="form-select" name="status">
                                <option value="">Tous les statuts</option>
                                <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Actif</option>
                                <option value="expired" <?= $filters['status'] === 'expired' ? 'selected' : '' ?>>Expiré</option>
                                <option value="cancelled" <?= $filters['status'] === 'cancelled' ? 'selected' : '' ?>>Annulé</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Expiration</label>
                            <select class="form-select" name="expiring">
                                <option value="">Toutes</option>
                                <option value="7" <?= $filters['expiring'] === '7' ? 'selected' : '' ?>>< 7 jours</option>
                                <option value="30" <?= $filters['expiring'] === '30' ? 'selected' : '' ?>>< 30 jours</option>
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
                            <a href="/ProActiv/superadmin/subscriptions" class="btn btn-outline-secondary d-block">
                                <i class="fas fa-times me-1"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des abonnements -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Abonnements
                        <span class="badge bg-secondary ms-2"><?= count($subscriptions) ?></span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Utilisateur</th>
                                    <th>Plan</th>
                                    <th>Statut</th>
                                    <th>Début</th>
                                    <th>Expiration</th>
                                    <th>Revenus</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($subscriptions as $sub): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle me-3">
                                                    <?= strtoupper(substr($sub['user']['first_name'], 0, 1) . substr($sub['user']['last_name'], 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0"><?= htmlspecialchars($sub['user']['first_name'] . ' ' . $sub['user']['last_name']) ?></h6>
                                                    <small class="text-muted"><?= htmlspecialchars($sub['user']['email']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $sub['plan_slug'] === 'standard' ? 'primary' : 'secondary' ?> fs-6">
                                                <?= ucfirst($sub['plan_name']) ?>
                                            </span>
                                            <br><small class="text-muted"><?= $sub['price'] ?>€/mois</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $sub['status'] === 'active' ? 'success' : 
                                                ($sub['status'] === 'expired' ? 'danger' : 'warning') 
                                            ?>">
                                                <?= ucfirst($sub['status']) ?>
                                            </span>
                                            <?php if ($sub['status'] === 'active' && $sub['expires_at'] && strtotime($sub['expires_at']) < strtotime('+7 days')): ?>
                                                <br><small class="text-warning">
                                                    <i class="fas fa-exclamation-triangle"></i> Expire bientôt
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small><?= date('d/m/Y', strtotime($sub['starts_at'])) ?></small>
                                        </td>
                                        <td>
                                            <?php if ($sub['expires_at']): ?>
                                                <small class="<?= strtotime($sub['expires_at']) < time() ? 'text-danger' : '' ?>">
                                                    <?= date('d/m/Y', strtotime($sub['expires_at'])) ?>
                                                </small>
                                                <?php if (strtotime($sub['expires_at']) < time()): ?>
                                                    <br><small class="text-danger">Expiré</small>
                                                <?php elseif (strtotime($sub['expires_at']) < strtotime('+7 days')): ?>
                                                    <br><small class="text-warning">
                                                        <?= ceil((strtotime($sub['expires_at']) - time()) / 86400) ?> jours
                                                    </small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <small class="text-muted">Illimité</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?= number_format($sub['total_revenue'], 2) ?>€</strong>
                                            <?php if ($sub['payments_count'] > 0): ?>
                                                <br><small class="text-muted"><?= $sub['payments_count'] ?> paiements</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" 
                                                        onclick="viewSubscription(<?= $sub['id'] ?>)" title="Voir détails">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <?php if ($sub['status'] === 'active'): ?>
                                                    <button class="btn btn-outline-success" 
                                                            onclick="extendSubscription(<?= $sub['id'] ?>)" title="Prolonger">
                                                        <i class="fas fa-calendar-plus"></i>
                                                    </button>
                                                    <button class="btn btn-outline-warning" 
                                                            onclick="cancelSubscription(<?= $sub['id'] ?>)" title="Annuler">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                <?php elseif ($sub['status'] === 'expired' || $sub['status'] === 'cancelled'): ?>
                                                    <button class="btn btn-outline-success" 
                                                            onclick="renewSubscription(<?= $sub['id'] ?>)" title="Renouveler">
                                                        <i class="fas fa-redo"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-secondary dropdown-toggle" 
                                                            data-bs-toggle="dropdown" title="Plus d'actions">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#" onclick="changePlan(<?= $sub['id'] ?>)">
                                                            <i class="fas fa-exchange-alt me-2"></i>Changer de plan
                                                        </a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="addPayment(<?= $sub['id'] ?>)">
                                                            <i class="fas fa-plus me-2"></i>Ajouter paiement
                                                        </a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item" href="#" onclick="viewPayments(<?= $sub['id'] ?>)">
                                                            <i class="fas fa-history me-2"></i>Historique paiements
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
</div>

<!-- Modal prolongation d'abonnement -->
<div class="modal fade" id="extendModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Prolonger l'abonnement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="extendForm">
                    <input type="hidden" id="extendSubId" name="subscription_id">
                    <div class="mb-3">
                        <label class="form-label">Durée de prolongation</label>
                        <select class="form-select" name="duration" required>
                            <option value="">Choisir...</option>
                            <option value="1">1 mois</option>
                            <option value="3">3 mois</option>
                            <option value="6">6 mois</option>
                            <option value="12">12 mois</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Raison</label>
                        <textarea class="form-control" name="reason" rows="3" placeholder="Raison de la prolongation (optionnel)"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success" onclick="confirmExtend()">Prolonger</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Graphique des revenus
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($chart_data['revenue']['labels']) ?>,
        datasets: [{
            label: 'Revenus (€)',
            data: <?= json_encode($chart_data['revenue']['data']) ?>,
            borderColor: 'rgb(54, 162, 235)',
            backgroundColor: 'rgba(54, 162, 235, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value + '€';
                    }
                }
            }
        }
    }
});

// Graphique des plans
const plansCtx = document.getElementById('plansChart').getContext('2d');
new Chart(plansCtx, {
    type: 'doughnut',
    data: {
        labels: ['Communautaire', 'Standard'],
        datasets: [{
            data: [<?= $stats['community_count'] ?>, <?= $stats['standard_count'] ?>],
            backgroundColor: ['#6c757d', '#0d6efd']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Fonctions d'actions
function viewSubscription(id) {
    window.location.href = `/ProActiv/superadmin/subscriptions/${id}`;
}

function extendSubscription(id) {
    document.getElementById('extendSubId').value = id;
    new bootstrap.Modal(document.getElementById('extendModal')).show();
}

function confirmExtend() {
    const form = document.getElementById('extendForm');
    const formData = new FormData(form);
    
    fetch('/ProActiv/superadmin/subscriptions/extend', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('extendModal')).hide();
            location.reload();
        } else {
            alert('Erreur: ' + data.error);
        }
    })
    .catch(error => {
        alert('Erreur de communication: ' + error);
    });
}

function cancelSubscription(id) {
    if (confirm('Êtes-vous sûr de vouloir annuler cet abonnement ?')) {
        fetch('/ProActiv/superadmin/subscriptions/cancel', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `subscription_id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + data.error);
            }
        });
    }
}

function renewSubscription(id) {
    if (confirm('Renouveler cet abonnement pour 1 mois ?')) {
        fetch('/ProActiv/superadmin/subscriptions/renew', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `subscription_id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + data.error);
            }
        });
    }
}

function exportSubscriptions() {
    window.open('/ProActiv/superadmin/subscriptions/export', '_blank');
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

.badge.fs-6 {
    font-size: 0.875rem !important;
}
</style>

