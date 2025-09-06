<?php
/**
 * Vue de l'historique des calculs
 */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h4><i class="fas fa-history me-2"></i>Historique des calculs</h4>
                    <p class="mb-0">Retrouvez tous vos calculs précédents</p>
                </div>
                <div class="card-body">
                    <?php if (empty($calculations)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calculator fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun calcul effectué</h5>
                            <p class="text-muted">Vos calculs apparaîtront ici une fois que vous aurez utilisé nos calculateurs.</p>
                            <a href="/calculators" class="btn btn-primary">
                                <i class="fas fa-calculator me-2"></i>Commencer un calcul
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th><i class="fas fa-calendar me-1"></i>Date</th>
                                        <th><i class="fas fa-tag me-1"></i>Type</th>
                                        <th><i class="fas fa-euro-sign me-1"></i>CA</th>
                                        <th><i class="fas fa-briefcase me-1"></i>Activité</th>
                                        <th><i class="fas fa-cogs me-1"></i>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($calculations as $calc): ?>
                                        <?php 
                                        $data = is_string($calc['data']) ? json_decode($calc['data'], true) : $calc['data'];
                                        $typeLabels = [
                                            'charges_sociales' => 'Charges sociales',
                                            'impots' => 'Impôts',
                                            'revenus' => 'Revenus nets',
                                            'tarification' => 'Tarification'
                                        ];
                                        $typeIcons = [
                                            'charges_sociales' => 'fas fa-calculator text-primary',
                                            'impots' => 'fas fa-percentage text-success',
                                            'revenus' => 'fas fa-euro-sign text-warning',
                                            'tarification' => 'fas fa-tags text-info'
                                        ];
                                        ?>
                                        <tr>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('d/m/Y H:i', strtotime($calc['created_at'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <i class="<?= $typeIcons[$calc['type']] ?? 'fas fa-file' ?> me-1"></i>
                                                <?= $typeLabels[$calc['type']] ?? ucfirst($calc['type']) ?>
                                            </td>
                                            <td>
                                                <strong><?= number_format($data['chiffre_affaires'] ?? $data['target_revenu'] ?? 0, 0, ',', ' ') ?>€</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?php
                                                    $activityLabels = [
                                                        'liberal' => 'Libérale',
                                                        'commercial' => 'Commerciale',
                                                        'artisanal' => 'Artisanale'
                                                    ];
                                                    echo $activityLabels[$data['activity_type']] ?? 'N/A';
                                                    ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-1" 
                                                        onclick="viewCalculation(<?= $calc['id'] ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-success me-1" 
                                                        onclick="duplicateCalculation(<?= $calc['id'] ?>)">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteCalculation(<?= $calc['id'] ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <small class="text-muted">
                                    <?= count($calculations) ?> calcul(s) au total
                                </small>
                            </div>
                            <div>
                                <button class="btn btn-outline-danger btn-sm" onclick="clearHistory()">
                                    <i class="fas fa-trash me-1"></i>Vider l'historique
                                </button>
                                <a href="/calculators" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i>Nouveau calcul
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewCalculation(id) {
    alert('Affichage du calcul #' + id + ' (fonctionnalité à implémenter)');
}

function duplicateCalculation(id) {
    if (confirm('Dupliquer ce calcul ?')) {
        alert('Calcul dupliqué ! (fonctionnalité à implémenter)');
    }
}

function deleteCalculation(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce calcul ?')) {
        // Ici on ferait un appel AJAX pour supprimer
        alert('Calcul supprimé ! (fonctionnalité à implémenter)');
    }
}

function clearHistory() {
    if (confirm('Êtes-vous sûr de vouloir vider tout l\'historique ?')) {
        alert('Historique vidé ! (fonctionnalité à implémenter)');
    }
}
</script>

