<?php $this->layout('layouts/main', ['title' => $title]) ?>

<div class="container-fluid mt-4">
    <!-- En-tête Super Admin -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0"><i class="fas fa-crown text-warning me-2"></i>Super Administration</h1>
                    <p class="text-muted mb-0">Gestion complète de la plateforme ProActiv</p>
                </div>
                <div class="btn-group">
                    <button class="btn btn-outline-primary" onclick="refreshStats()">
                        <i class="fas fa-sync-alt me-1"></i>Actualiser
                    </button>
                    <a href="/superadmin/maintenance" class="btn btn-outline-warning">
                        <i class="fas fa-tools me-1"></i>Maintenance
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= number_format($stats['total_users']) ?></h4>
                            <p class="mb-0">Utilisateurs totaux</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= number_format($stats['active_users']) ?></h4>
                            <p class="mb-0">Utilisateurs actifs</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-check fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= number_format($stats['trial_users']) ?></h4>
                            <p class="mb-0">Comptes d'essai</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= number_format($stats['monthly_revenue'], 2) ?>€</h4>
                            <p class="mb-0">Revenus mensuels</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-euro-sign fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Actions rapides -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>Actions rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="/superadmin/users" class="btn btn-outline-primary">
                            <i class="fas fa-users me-2"></i>Gérer les utilisateurs
                        </a>
                        <a href="/superadmin/subscriptions" class="btn btn-outline-success">
                            <i class="fas fa-credit-card me-2"></i>Gérer les abonnements
                        </a>
                        <a href="/superadmin/comments" class="btn btn-outline-warning">
                            <i class="fas fa-comments me-2"></i>Gérer les commentaires
                        </a>
                        <a href="/superadmin/analytics" class="btn btn-outline-info">
                            <i class="fas fa-chart-bar me-2"></i>Voir les analytics
                        </a>
                        <a href="/superadmin/settings" class="btn btn-outline-secondary">
                            <i class="fas fa-cog me-2"></i>Paramètres système
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Utilisateurs récents -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-plus me-2"></i>Utilisateurs récents
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_users)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach (array_slice($recent_users, 0, 5) as $user): ?>
                                <div class="list-group-item px-0 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h6>
                                            <small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>
                                        </div>
                                        <div>
                                            <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'warning' ?>">
                                                <?= ucfirst($user['status']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-center mt-3">
                            <a href="/superadmin/users" class="btn btn-sm btn-outline-primary">Voir tous</a>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center">Aucun utilisateur récent</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Abonnements expirant -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Abonnements expirant
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($expiring_subscriptions)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach (array_slice($expiring_subscriptions, 0, 5) as $sub): ?>
                                <div class="list-group-item px-0 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0"><?= htmlspecialchars($sub['first_name'] . ' ' . $sub['last_name']) ?></h6>
                                            <small class="text-muted"><?= htmlspecialchars($sub['plan_name']) ?></small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-warning">
                                                <?= $sub['days_remaining'] ?> jours
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-center mt-3">
                            <a href="/superadmin/subscriptions" class="btn btn-sm btn-outline-warning">Voir tous</a>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center">Aucun abonnement expirant</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques des abonnements -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Répartition des abonnements
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="subscriptionChart" width="400" height="200"></canvas>
                        </div>
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Plan</th>
                                            <th>Nombre</th>
                                            <th>Revenus</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($subscription_stats['by_plan'])): ?>
                                            <?php foreach ($subscription_stats['by_plan'] as $plan): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($plan['name'] ?? $plan['slug']) ?></td>
                                                    <td><?= number_format($plan['count']) ?></td>
                                                    <td>
                                                        <?php if ($plan['slug'] === 'standard'): ?>
                                                            <?= number_format($plan['count'] * 5, 2) ?>€
                                                        <?php else: ?>
                                                            0.00€
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td>Communautaire</td>
                                                <td>1</td>
                                                <td>0.00€</td>
                                            </tr>
                                            <tr>
                                                <td>Standard</td>
                                                <td>1</td>
                                                <td>5.00€</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Graphique des abonnements
const ctx = document.getElementById('subscriptionChart').getContext('2d');
const subscriptionChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Communautaire', 'Standard'],
        datasets: [{
            data: [1, 1],
            backgroundColor: [
                '#ff6b35',
                '#0d6efd'
            ],
            borderWidth: 2
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

// Actualiser les statistiques
function refreshStats() {
    location.reload();
}

// Animation des cartes au chargement
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>

<style>
.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.bg-primary { background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%) !important; }
.bg-success { background: linear-gradient(135deg, #198754 0%, #146c43 100%) !important; }
.bg-warning { background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important; }
.bg-info { background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%) !important; }

.list-group-item {
    border: none;
    border-bottom: 1px solid #dee2e6;
}

.list-group-item:last-child {
    border-bottom: none;
}

.opacity-75 {
    opacity: 0.75;
}
</style>

