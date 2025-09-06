<?php $lang = $lang ?? new LanguageService(); ?>

<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/ProActiv/superadmin">Super Admin</a></li>
                            <li class="breadcrumb-item active">Analytics</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-chart-line text-primary me-2"></i>
                        Analytics & Statistiques
                    </h1>
                    <p class="text-muted mb-0">Analyse détaillée des performances de la plateforme</p>
                </div>
                <div class="btn-group">
                    <button class="btn btn-outline-primary" onclick="refreshAnalytics()">
                        <i class="fas fa-sync-alt me-1"></i>Actualiser
                    </button>
                    <button class="btn btn-outline-secondary" onclick="exportAnalytics()">
                        <i class="fas fa-download me-1"></i>Exporter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres de période -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary active" data-period="7">7 jours</button>
                                <button type="button" class="btn btn-outline-primary" data-period="30">30 jours</button>
                                <button type="button" class="btn btn-outline-primary" data-period="90">3 mois</button>
                                <button type="button" class="btn btn-outline-primary" data-period="365">1 an</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="date" class="form-control" id="dateFrom" value="<?= date('Y-m-d', strtotime('-7 days')) ?>">
                                </div>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" id="dateTo" value="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs principaux -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="icon-circle bg-primary text-white me-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <h3 class="mb-0" id="totalUsers"><?= $analytics['total_users'] ?? 0 ?></h3>
                            <small class="text-muted">Utilisateurs totaux</small>
                        </div>
                    </div>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                    </div>
                    <small class="text-success">
                        <i class="fas fa-arrow-up"></i> +<?= $analytics['user_growth'] ?? 0 ?>% ce mois
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="icon-circle bg-success text-white me-3">
                            <i class="fas fa-euro-sign"></i>
                        </div>
                        <div>
                            <h3 class="mb-0" id="monthlyRevenue"><?= number_format($analytics['monthly_revenue'] ?? 0, 0) ?>€</h3>
                            <small class="text-muted">Revenus mensuels</small>
                        </div>
                    </div>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: 85%"></div>
                    </div>
                    <small class="text-success">
                        <i class="fas fa-arrow-up"></i> +<?= $analytics['revenue_growth'] ?? 0 ?>% vs mois dernier
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="icon-circle bg-warning text-white me-3">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <h3 class="mb-0" id="conversionRate"><?= number_format($analytics['conversion_rate'] ?? 0, 1) ?>%</h3>
                            <small class="text-muted">Taux de conversion</small>
                        </div>
                    </div>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-warning" style="width: 65%"></div>
                    </div>
                    <small class="text-info">
                        <i class="fas fa-arrow-right"></i> Stable
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="icon-circle bg-danger text-white me-3">
                            <i class="fas fa-user-times"></i>
                        </div>
                        <div>
                            <h3 class="mb-0" id="churnRate"><?= number_format($analytics['churn_rate'] ?? 0, 1) ?>%</h3>
                            <small class="text-muted">Taux de désabonnement</small>
                        </div>
                    </div>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-danger" style="width: 25%"></div>
                    </div>
                    <small class="text-success">
                        <i class="fas fa-arrow-down"></i> -<?= $analytics['churn_improvement'] ?? 0 ?>% vs mois dernier
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques principaux -->
    <div class="row mb-4">
        <!-- Croissance des utilisateurs -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-area text-primary me-2"></i>
                        Croissance des utilisateurs
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="userGrowthChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Répartition des abonnements -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie text-success me-2"></i>
                        Répartition des abonnements
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="subscriptionChart" height="300"></canvas>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="fas fa-circle text-primary"></i> Community</span>
                            <span class="fw-bold"><?= $analytics['community_percentage'] ?? 0 ?>%</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-circle text-warning"></i> Standard</span>
                            <span class="fw-bold"><?= $analytics['standard_percentage'] ?? 0 ?>%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenus et activité -->
    <div class="row mb-4">
        <!-- Évolution des revenus -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line text-success me-2"></i>
                        Évolution des revenus
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Activité des utilisateurs -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar text-info me-2"></i>
                        Activité des utilisateurs
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="activityChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Métriques détaillées -->
    <div class="row mb-4">
        <!-- Top fonctionnalités -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-star text-warning me-2"></i>
                        Fonctionnalités populaires
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <?php 
                        $features = $analytics['popular_features'] ?? [
                            ['name' => 'Calculateur charges sociales', 'usage' => 85],
                            ['name' => 'Génération PDF', 'usage' => 72],
                            ['name' => 'Forum communautaire', 'usage' => 68],
                            ['name' => 'Calculateur impôts', 'usage' => 61],
                            ['name' => 'Aide tarification', 'usage' => 45]
                        ];
                        foreach ($features as $feature): ?>
                            <div class="list-group-item px-0 py-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-medium"><?= htmlspecialchars($feature['name']) ?></span>
                                    <span class="badge bg-primary"><?= $feature['usage'] ?>%</span>
                                </div>
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar" style="width: <?= $feature['usage'] ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Géolocalisation -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-globe text-info me-2"></i>
                        Répartition géographique
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <?php 
                        $countries = $analytics['countries'] ?? [
                            ['name' => 'France', 'users' => 1245, 'percentage' => 78],
                            ['name' => 'Belgique', 'users' => 187, 'percentage' => 12],
                            ['name' => 'Suisse', 'users' => 98, 'percentage' => 6],
                            ['name' => 'Canada', 'users' => 64, 'percentage' => 4]
                        ];
                        foreach ($countries as $country): ?>
                            <div class="list-group-item px-0 py-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-medium"><?= htmlspecialchars($country['name']) ?></span>
                                    <span class="text-muted"><?= number_format($country['users']) ?> utilisateurs</span>
                                </div>
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-info" style="width: <?= $country['percentage'] ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Métriques de performance -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tachometer-alt text-danger me-2"></i>
                        Performance système
                    </h5>
                </div>
                <div class="card-body">
                    <div class="metric-item mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Temps de réponse moyen</span>
                            <span class="fw-bold text-success"><?= $analytics['avg_response_time'] ?? '245' ?>ms</span>
                        </div>
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-success" style="width: 85%"></div>
                        </div>
                    </div>
                    
                    <div class="metric-item mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Disponibilité</span>
                            <span class="fw-bold text-success"><?= $analytics['uptime'] ?? '99.8' ?>%</span>
                        </div>
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-success" style="width: 99%"></div>
                        </div>
                    </div>
                    
                    <div class="metric-item mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Erreurs 5xx</span>
                            <span class="fw-bold text-warning"><?= $analytics['error_rate'] ?? '0.2' ?>%</span>
                        </div>
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-warning" style="width: 15%"></div>
                        </div>
                    </div>
                    
                    <div class="metric-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Utilisation CPU</span>
                            <span class="fw-bold text-info"><?= $analytics['cpu_usage'] ?? '34' ?>%</span>
                        </div>
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-info" style="width: 34%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes et recommandations -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        Alertes et recommandations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Alertes</h6>
                            <div class="alert alert-warning" role="alert">
                                <strong>Taux de conversion en baisse</strong><br>
                                Le taux de conversion Community → Standard a diminué de 3% ce mois. 
                                <a href="#" class="alert-link">Analyser les causes</a>
                            </div>
                            <div class="alert alert-info" role="alert">
                                <strong>Pic d'activité détecté</strong><br>
                                Augmentation de 25% de l'utilisation des calculateurs cette semaine.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success"><i class="fas fa-chart-line me-2"></i>Recommandations</h6>
                            <div class="recommendation-item mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-arrow-up text-success me-2 mt-1"></i>
                                    <div>
                                        <strong>Optimiser l'onboarding</strong><br>
                                        <small class="text-muted">Les utilisateurs qui complètent le tutoriel ont 40% plus de chances de s'abonner.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="recommendation-item mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-target text-primary me-2 mt-1"></i>
                                    <div>
                                        <strong>Campagne de rétention</strong><br>
                                        <small class="text-muted">Cibler les utilisateurs inactifs depuis 7 jours avec une offre spéciale.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
    flex-shrink: 0;
}

.metric-item {
    padding: 0.5rem 0;
}

.recommendation-item {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #28a745;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.progress {
    background-color: #e9ecef;
}

.btn-group .btn.active {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données pour les graphiques
    const analyticsData = <?= json_encode($analytics ?? []) ?>;
    
    // Configuration des couleurs
    const colors = {
        primary: '#007bff',
        success: '#28a745',
        warning: '#ffc107',
        danger: '#dc3545',
        info: '#17a2b8'
    };
    
    // Graphique de croissance des utilisateurs
    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: analyticsData.user_growth_labels || ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'],
            datasets: [{
                label: 'Nouveaux utilisateurs',
                data: analyticsData.user_growth_data || [12, 19, 25, 32, 28, 35],
                borderColor: colors.primary,
                backgroundColor: colors.primary + '20',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Graphique de répartition des abonnements
    const subscriptionCtx = document.getElementById('subscriptionChart').getContext('2d');
    new Chart(subscriptionCtx, {
        type: 'doughnut',
        data: {
            labels: ['Community', 'Standard'],
            datasets: [{
                data: [
                    analyticsData.community_count || 75,
                    analyticsData.standard_count || 25
                ],
                backgroundColor: [colors.primary, colors.warning],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    
    // Graphique des revenus
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: analyticsData.revenue_labels || ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'],
            datasets: [{
                label: 'Revenus (€)',
                data: analyticsData.revenue_data || [1200, 1450, 1800, 2100, 1950, 2300],
                backgroundColor: colors.success,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Graphique d'activité
    const activityCtx = document.getElementById('activityChart').getContext('2d');
    new Chart(activityCtx, {
        type: 'line',
        data: {
            labels: analyticsData.activity_labels || ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
            datasets: [{
                label: 'Sessions actives',
                data: analyticsData.activity_data || [45, 52, 48, 61, 55, 32, 28],
                borderColor: colors.info,
                backgroundColor: colors.info + '20',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Gestion des filtres de période
    document.querySelectorAll('[data-period]').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('[data-period]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const period = this.dataset.period;
            updateAnalytics(period);
        });
    });
    
    // Gestion des dates personnalisées
    document.getElementById('dateFrom').addEventListener('change', updateCustomPeriod);
    document.getElementById('dateTo').addEventListener('change', updateCustomPeriod);
    
    function updateCustomPeriod() {
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        
        if (dateFrom && dateTo) {
            document.querySelectorAll('[data-period]').forEach(b => b.classList.remove('active'));
            updateAnalytics('custom', dateFrom, dateTo);
        }
    }
    
    function updateAnalytics(period, dateFrom = null, dateTo = null) {
        // Simulation de mise à jour des données
        console.log('Mise à jour des analytics pour la période:', period);
        
        // En production, faire un appel AJAX pour récupérer les nouvelles données
        // fetch('/ProActiv/superadmin/analytics/data', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify({ period, dateFrom, dateTo })
        // }).then(response => response.json())
        //   .then(data => updateCharts(data));
    }
    
    function refreshAnalytics() {
        location.reload();
    }
    
    function exportAnalytics() {
        // Simulation d'export
        const data = {
            period: document.querySelector('[data-period].active')?.dataset.period || '7',
            dateFrom: document.getElementById('dateFrom').value,
            dateTo: document.getElementById('dateTo').value
        };
        
        // En production, générer et télécharger le rapport
        console.log('Export des analytics:', data);
        alert('Export en cours... (fonctionnalité simulée)');
    }
    
    // Rendre les fonctions globales
    window.refreshAnalytics = refreshAnalytics;
    window.exportAnalytics = exportAnalytics;
});
</script>

