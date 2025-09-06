<?php
/**
 * Vue d'un sujet individuel du forum
 */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/forum">Forum</a></li>
                    <li class="breadcrumb-item"><a href="/forum/category/1"><?= htmlspecialchars($topic['category']) ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($topic['title']) ?></li>
                </ol>
            </nav>

            <!-- Sujet principal -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4><i class="fas fa-comment me-2"></i><?= htmlspecialchars($topic['title']) ?></h4>
                            <small>
                                Par <strong><?= htmlspecialchars($topic['author']) ?></strong> 
                                le <?= date('d/m/Y à H:i', strtotime($topic['created_at'])) ?>
                                • <?= $topic['views'] ?> vues
                            </small>
                        </div>
                        <div>
                            <span class="badge bg-light text-dark"><?= htmlspecialchars($topic['category']) ?></span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-circle fa-3x text-muted"></i>
                                <div class="mt-2">
                                    <strong><?= htmlspecialchars($topic['author']) ?></strong>
                                    <br>
                                    <small class="text-muted">Membre</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="message-content">
                                <?= nl2br(htmlspecialchars($topic['content'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Réponses -->
            <?php if (!empty($posts)): ?>
                <h5><i class="fas fa-reply me-2"></i>Réponses (<?= count($posts) ?>)</h5>
                
                <?php foreach ($posts as $index => $post): ?>
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= htmlspecialchars($post['author']) ?></strong>
                                    <small class="text-muted">
                                        • Réponse #<?= $index + 1 ?> 
                                        • <?= date('d/m/Y à H:i', strtotime($post['created_at'])) ?>
                                    </small>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-outline-secondary" title="Citer ce message">
                                        <i class="fas fa-quote-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2 text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-user-circle fa-2x text-muted"></i>
                                        <div class="mt-2">
                                            <strong><?= htmlspecialchars($post['author']) ?></strong>
                                            <br>
                                            <small class="text-muted">Membre</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="message-content">
                                        <?= nl2br(htmlspecialchars($post['content'])) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Formulaire de réponse -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5><i class="fas fa-reply me-2"></i>Répondre à ce sujet</h5>
                </div>
                <div class="card-body">
                    <form id="replyForm" action="/forum/topic/<?= $topic['id'] ?>/reply" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">Votre réponse</label>
                            <textarea class="form-control" id="content" name="content" rows="6" 
                                      placeholder="Écrivez votre réponse ici..." required></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Soyez respectueux et constructif dans vos réponses
                                </small>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-eye me-1"></i>Aperçu
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-paper-plane me-1"></i>Publier la réponse
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('replyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Réponse publiée avec succès !');
            location.reload();
        } else {
            alert('Erreur : ' + (data.error || 'Une erreur est survenue'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors de la publication');
    });
});
</script>

