<?php
/**
 * Dashboard utilisateur personnalisé - Vue moderne avec graphiques
 */
?>

<div class="container-fluid">
    <!-- En-tête du dashboard -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-tachometer-alt me-2 text-primary"></i>Tableau de bord</h2>
                    <p class="text-muted mb-0">Bienvenue, <strong><?= htmlspecialchars($user_name) ?></strong> • Dernière connexion: <?= $stats['last_login'] ?></p>
                </div>
                <div>
                    <button class="btn btn-outline-primary" onclick="refreshDashboard()">
                        <i class="fas fa-sync-alt me-1"></i>Actualiser
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Chiffre d'affaires</h6>
                            <h3 class="text-success mb-0"><?= number_format($stats['revenue_month'], 0, ',', ' ') ?>€</h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i>
                                +<?= number_format((($stats['revenue_month'] - $stats['revenue_previous_month']) / $stats['revenue_previous_month']) * 100, 1) ?>%
                            </small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-euro-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Charges sociales</h6>
                            <h3 class="text-warning mb-0"><?= number_format($stats['charges_month'], 0, ',', ' ') ?>€</h3>
                            <small class="text-warning">
                                <i class="fas fa-arrow-up me-1"></i>
                                +<?= number_format((($stats['charges_month'] - $stats['charges_previous_month']) / $stats['charges_previous_month']) * 100, 1) ?>%
                            </small>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-calculator fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Revenus nets</h6>
                            <h3 class="text-primary mb-0"><?= number_format($stats['net_income_month'], 0, ',', ' ') ?>€</h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i>
                                +<?= number_format((($stats['net_income_month'] - $stats['net_income_previous_month']) / $stats['net_income_previous_month']) * 100, 1) ?>%
                            </small>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Documents créés</h6>
                            <h3 class="text-info mb-0"><?= $stats['documents_count'] ?></h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i>
                                +<?= $stats['documents_count'] - $stats['documents_previous_month'] ?> ce mois
                            </small>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-file-invoice fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2 text-primary"></i>Actions rapides</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($quick_actions as $action): ?>
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                <a href="<?= $action['url'] ?>" class="btn btn-outline-<?= $action['color'] ?> w-100 h-100 d-flex flex-column justify-content-center text-decoration-none" style="min-height: 120px;">
                                    <i class="<?= $action['icon'] ?> fa-2x mb-2"></i>
                                    <strong><?= htmlspecialchars($action['title']) ?></strong>
                                    <small class="text-muted mt-1"><?= htmlspecialchars($action['description']) ?></small>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et activités -->
    <div class="row mb-4">
        <!-- Graphique d'évolution -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-chart-area me-2 text-primary"></i>Évolution financière</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Activités récentes -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-history me-2 text-primary"></i>Activités récentes</h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <?php foreach ($activities as $activity): ?>
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-<?= $activity['color'] ?> text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="<?= $activity['icon'] ?> fa-sm"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1"><?= htmlspecialchars($activity['action']) ?></h6>
                                <p class="text-muted mb-1 small"><?= htmlspecialchars($activity['description']) ?></p>
                                <small class="text-muted"><?= $activity['date'] ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications et alertes -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-bell me-2 text-primary"></i>Notifications et alertes</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($notifications as $notif): ?>
                            <div class="col-lg-6 mb-3">
                                <div class="alert alert-<?= $notif['type'] ?> border-0 shadow-sm">
                                    <div class="d-flex align-items-center">
                                        <i class="<?= $notif['icon'] ?> fa-lg me-3"></i>
                                        <div class="flex-grow-1">
                                            <h6 class="alert-heading mb-1"><?= htmlspecialchars($notif['title']) ?></h6>
                                            <p class="mb-2"><?= htmlspecialchars($notif['message']) ?></p>
                                            <small class="text-muted"><?= $notif['date'] ?></small>
                                        </div>
                                        <?php if (isset($notif['action'])): ?>
                                            <a href="<?= $notif['action'] ?>" class="btn btn-sm btn-outline-<?= $notif['type'] ?>">
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inclusion de Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Configuration du graphique d'évolution
const ctx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($chart_data['revenue_evolution']['labels']) ?>,
        datasets: [{
            label: 'Chiffre d\'affaires',
            data: <?= json_encode($chart_data['revenue_evolution']['data']) ?>,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Charges sociales',
            data: <?= json_encode($chart_data['charges_evolution']['data']) ?>,
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Évolution mensuelle 2025'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString() + '€';
                    }
                }
            }
        }
    }
});

// Fonction de rafraîchissement du dashboard
function refreshDashboard() {
    const btn = event.target.closest('button');
    const icon = btn.querySelector('i');
    
    icon.classList.add('fa-spin');
    btn.disabled = true;
    
    fetch('/dashboard/stats', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Dashboard actualisé:', data);
        setTimeout(() => {
            location.reload();
        }, 1000);
    })
    .catch(error => {
        console.error('Erreur:', error);
    })
    .finally(() => {
        setTimeout(() => {
            icon.classList.remove('fa-spin');
            btn.disabled = false;
        }, 1000);
    });
}

// Mise à jour automatique toutes les 5 minutes
setInterval(() => {
    fetch('/dashboard/stats', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Stats mises à jour automatiquement');
    })
    .catch(console.error);
}, 300000);
</script>
