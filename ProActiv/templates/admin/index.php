<?php
/**
 * Tableau de bord administrateur
 */
?>

<div class="container-fluid">
    <!-- En-tête administration -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-cogs me-2 text-danger"></i>Administration</h2>
                    <p class="text-muted mb-0">Tableau de bord administrateur • Accès complet à la plateforme</p>
                </div>
                <div>
                    <div class="btn-group">
                        <button class="btn btn-outline-danger" onclick="toggleMaintenanceMode()">
                            <i class="fas fa-tools me-1"></i>Maintenance
                        </button>
                        <button class="btn btn-outline-primary" onclick="refreshAdminStats()">
                            <i class="fas fa-sync-alt me-1"></i>Actualiser
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation admin -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="/admin/users" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center text-decoration-none" style="min-height: 100px;">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <strong>Utilisateurs</strong>
                                <small class="text-muted"><?= number_format($stats['total_users']) ?> inscrits</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="/admin/forum" class="btn btn-outline-success w-100 h-100 d-flex flex-column justify-content-center text-decoration-none" style="min-height: 100px;">
                                <i class="fas fa-comments fa-2x mb-2"></i>
                                <strong>Forum</strong>
                                <small class="text-muted"><?= $stats['forum_topics'] ?> sujets</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="/admin/documents" class="btn btn-outline-warning w-100 h-100 d-flex flex-column justify-content-center text-decoration-none" style="min-height: 100px;">
                                <i class="fas fa-file-invoice fa-2x mb-2"></i>
                                <strong>Documents</strong>
                                <small class="text-muted"><?= number_format($stats['total_documents']) ?> créés</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="/admin/analytics" class="btn btn-outline-info w-100 h-100 d-flex flex-column justify-content-center text-decoration-none" style="min-height: 100px;">
                                <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                <strong>Analytics</strong>
                                <small class="text-muted">Rapports détaillés</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="/admin/settings" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column justify-content-center text-decoration-none" style="min-height: 100px;">
                                <i class="fas fa-cog fa-2x mb-2"></i>
                                <strong>Configuration</strong>
                                <small class="text-muted">Paramètres système</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="/admin/logs" class="btn btn-outline-dark w-100 h-100 d-flex flex-column justify-content-center text-decoration-none" style="min-height: 100px;">
                                <i class="fas fa-list-alt fa-2x mb-2"></i>
                                <strong>Logs</strong>
                                <small class="text-muted">Surveillance système</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Utilisateurs actifs</h6>
                            <h3 class="mb-0"><?= number_format($stats['active_users_today']) ?></h3>
                            <small>aujourd'hui</small>
                        </div>
                        <div>
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Documents générés</h6>
                            <h3 class="mb-0"><?= $stats['documents_today'] ?></h3>
                            <small>aujourd'hui</small>
                        </div>
                        <div>
                            <i class="fas fa-file-invoice fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Calculs effectués</h6>
                            <h3 class="mb-0"><?= $stats['calculations_today'] ?></h3>
                            <small>aujourd'hui</small>
                        </div>
                        <div>
                            <i class="fas fa-calculator fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Uptime serveur</h6>
                            <h6 class="mb-0"><?= $stats['server_uptime'] ?></h6>
                            <small>disponibilité</small>
                        </div>
                        <div>
                            <i class="fas fa-server fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activité récente et informations système -->
    <div class="row">
        <!-- Activité récente -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-clock me-2 text-primary"></i>Activité récente</h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <?php foreach ($recent_activity as $activity): ?>
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-<?= $activity['color'] ?> text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="<?= $activity['icon'] ?> fa-sm"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1"><?= htmlspecialchars($activity['message']) ?></h6>
                                <small class="text-muted"><?= date('d/m/Y H:i:s', strtotime($activity['timestamp'])) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Informations système -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Informations système</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Version de l'application</strong>
                        <div class="text-muted"><?= $system_info['app_version'] ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Version PHP</strong>
                        <div class="text-muted"><?= $system_info['php_version'] ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Base de données</strong>
                        <div class="text-muted">MySQL <?= $system_info['database_version'] ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Utilisation disque</strong>
                        <div class="text-muted"><?= $stats['disk_usage'] ?></div>
                        <div class="progress mt-1" style="height: 5px;">
                            <div class="progress-bar bg-success" style="width: 23%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Utilisation mémoire</strong>
                        <div class="text-muted"><?= $stats['memory_usage'] ?></div>
                        <div class="progress mt-1" style="height: 5px;">
                            <div class="progress-bar bg-warning" style="width: 25%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Dernière sauvegarde</strong>
                        <div class="text-muted"><?= date('d/m/Y H:i', strtotime($system_info['last_backup'])) ?></div>
                    </div>
                    
                    <div class="mb-0">
                        <strong>Mode maintenance</strong>
                        <div class="text-muted">
                            <?php if ($system_info['maintenance_mode']): ?>
                                <span class="badge bg-danger">Activé</span>
                            <?php else: ?>
                                <span class="badge bg-success">Désactivé</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleMaintenanceMode() {
    if (confirm('Êtes-vous sûr de vouloir activer/désactiver le mode maintenance ?')) {
        fetch('/admin/maintenance', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Mode maintenance mis à jour');
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

function refreshAdminStats() {
    const btn = event.target.closest('button');
    const icon = btn.querySelector('i');
    
    icon.classList.add('fa-spin');
    btn.disabled = true;
    
    fetch('/admin/stats', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Stats admin actualisées:', data);
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

// Actualisation automatique toutes les 2 minutes
setInterval(() => {
    fetch('/admin/stats', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Stats admin mises à jour automatiquement');
    })
    .catch(console.error);
}, 120000);
</script>

