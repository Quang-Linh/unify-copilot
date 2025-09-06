<?php
/**
 * Vue principale de la gestion des documents
 */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4><i class="fas fa-file-alt me-2"></i>Gestion des documents</h4>
                        <p class="mb-0">Créez, gérez et suivez vos factures et devis</p>
                    </div>
                    <div>
                        <a href="/documents/create?type=invoice" class="btn btn-light me-2">
                            <i class="fas fa-plus me-1"></i>Nouvelle facture
                        </a>
                        <a href="/documents/create?type=quote" class="btn btn-outline-light">
                            <i class="fas fa-plus me-1"></i>Nouveau devis
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($documents)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun document créé</h5>
                            <p class="text-muted">Vos factures et devis apparaîtront ici.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th><i class="fas fa-calendar me-1"></i>Date</th>
                                        <th><i class="fas fa-tag me-1"></i>Type</th>
                                        <th><i class="fas fa-user me-1"></i>Client</th>
                                        <th><i class="fas fa-euro-sign me-1"></i>Montant</th>
                                        <th><i class="fas fa-info-circle me-1"></i>Statut</th>
                                        <th><i class="fas fa-cogs me-1"></i>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($documents as $doc): ?>
                                        <?php 
                                        $data = is_string($doc["data"]) ? json_decode($doc["data"], true) : $doc["data"];
                                        $typeLabels = ["invoice" => "Facture", "quote" => "Devis"];
                                        $typeIcons = ["invoice" => "fas fa-file-invoice text-primary", "quote" => "fas fa-file-alt text-info"];
                                        $statusLabels = ["draft" => "Brouillon", "sent" => "Envoyé", "paid" => "Payé", "late" => "En retard"];
                                        $statusColors = ["draft" => "secondary", "sent" => "primary", "paid" => "success", "late" => "danger"];
                                        $status = $data["status"] ?? "draft";
                                        ?>
                                        <tr>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date("d/m/Y", strtotime($doc["created_at"])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <i class="<?= $typeIcons[$doc["type"]] ?? "fas fa-file" ?> me-1"></i>
                                                <?= $typeLabels[$doc["type"]] ?? ucfirst($doc["type"]) ?>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($data["client_name"] ?? "N/A") ?></strong>
                                            </td>
                                            <td>
                                                <strong><?= number_format($data["amount"] ?? 0, 2, ",", " ") ?>€</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $statusColors[$status] ?? "secondary" ?>">
                                                    <?= $statusLabels[$status] ?? ucfirst($status) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= $doc["path"] ?>" class="btn btn-sm btn-outline-primary me-1" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="/documents/edit?id=<?= $doc["id"] ?>" class="btn btn-sm btn-outline-success me-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteDocument(<?= $doc["id"] ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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
</div>

<script>
function deleteDocument(id) {
    if (confirm("Êtes-vous sûr de vouloir supprimer ce document ?")) {
        // Appel AJAX pour supprimer
        alert("Document supprimé ! (fonctionnalité à implémenter)");
    }
}
</script>

