<?php
/**
 * Vue de création de documents (factures/devis)
 */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4><i class="fas fa-plus-circle me-2"></i>Créer un nouveau <?= $type === "invoice" ? "facture" : "devis" ?></h4>
                </div>
                <div class="card-body">
                    <form id="documentForm">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
                        
                        <h5>Informations client</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="client_name" class="form-label">Nom du client</label>
                                    <input type="text" class="form-control" id="client_name" name="client_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="client_email" class="form-label">Email du client</label>
                                    <input type="email" class="form-control" id="client_email" name="client_email">
                                </div>
                            </div>
                        </div>
                        
                        <h5>Lignes de prestation</h5>
                        <div id="itemsContainer">
                            <!-- Lignes de prestation ajoutées dynamiquement -->
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="addItemBtn">
                            <i class="fas fa-plus me-1"></i>Ajouter une ligne
                        </button>
                        
                        <hr>
                        
                        <div class="row justify-content-end">
                            <div class="col-md-4">
                                <div class="mb-2 d-flex justify-content-between">
                                    <span>Sous-total HT</span>
                                    <strong id="subtotal">0,00€</strong>
                                </div>
                                <div class="mb-2 d-flex justify-content-between">
                                    <span>TVA (20%)</span>
                                    <strong id="vat">0,00€</strong>
                                </div>
                                <div class="mb-2 d-flex justify-content-between bg-light p-2 rounded">
                                    <h5>Total TTC</h5>
                                    <h5 id="total">0,00€</h5>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-file-pdf me-2"></i>Générer le PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const itemsContainer = document.getElementById("itemsContainer");
    const addItemBtn = document.getElementById("addItemBtn");
    let itemIndex = 0;

    function addItem() {
        itemIndex++;
        const itemHtml = `
            <div class="row item-row mb-2">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="items[${itemIndex}][description]" placeholder="Description" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control quantity" name="items[${itemIndex}][quantity]" placeholder="Qté" value="1" min="0" step="0.01">
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control price" name="items[${itemIndex}][price]" placeholder="Prix U." min="0" step="0.01">
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control total-item" readonly>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm removeItemBtn"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        `;
        itemsContainer.insertAdjacentHTML("beforeend", itemHtml);
    }

    addItemBtn.addEventListener("click", addItem);

    itemsContainer.addEventListener("click", function(e) {
        if (e.target.classList.contains("removeItemBtn") || e.target.closest(".removeItemBtn")) {
            e.target.closest(".item-row").remove();
            updateTotals();
        }
    });

    itemsContainer.addEventListener("input", function(e) {
        if (e.target.classList.contains("quantity") || e.target.classList.contains("price")) {
            const row = e.target.closest(".item-row");
            const quantity = parseFloat(row.querySelector(".quantity").value) || 0;
            const price = parseFloat(row.querySelector(".price").value) || 0;
            const totalItem = row.querySelector(".total-item");
            totalItem.value = formatCurrency(quantity * price);
            updateTotals();
        }
    });

    function updateTotals() {
        let subtotal = 0;
        document.querySelectorAll(".item-row").forEach(row => {
            const quantity = parseFloat(row.querySelector(".quantity").value) || 0;
            const price = parseFloat(row.querySelector(".price").value) || 0;
            subtotal += quantity * price;
        });

        const vat = subtotal * 0.20;
        const total = subtotal + vat;

        document.getElementById("subtotal").textContent = formatCurrency(subtotal);
        document.getElementById("vat").textContent = formatCurrency(vat);
        document.getElementById("total").textContent = formatCurrency(total);
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat("fr-FR", { style: "currency", currency: "EUR" }).format(amount);
    }

    // Ajout de la première ligne
    addItem();
});

document.getElementById("documentForm").addEventListener("submit", function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector("button[type=\"submit\"]");
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = 
        `<i class="fas fa-spinner fa-spin me-2"></i>Génération en cours...`;
    submitBtn.disabled = true;
    
    fetch("/documents/generate", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Document généré avec succès ! Chemin : " + data.path);
            window.location.href = "/documents";
        } else {
            alert("Erreur : " + (data.error || "Erreur inconnue"));
        }
    })
    .catch(error => {
        console.error("Erreur:", error);
        alert("Erreur de communication avec le serveur");
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
</script>

