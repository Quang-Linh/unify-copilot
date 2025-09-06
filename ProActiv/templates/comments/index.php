<?php $lang = $lang ?? new LanguageService(); ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- En-tête de la page -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center py-5">
                    <h1 class="display-4 text-primary mb-3">
                        <i class="fas fa-comments"></i>
                        <?= $lang->get('customer_testimonials', 'Témoignages clients') ?>
                    </h1>
                    <p class="lead text-muted mb-4">
                        <?= $lang->get('testimonials_subtitle', 'Découvrez ce que nos utilisateurs pensent de ProActiv') ?>
                    </p>
                    
                    <!-- Statistiques globales -->
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="row text-center">
                                <div class="col-md-4 mb-3">
                                    <div class="stat-item">
                                        <h3 class="text-primary mb-1"><?= $stats['approved'] ?></h3>
                                        <small class="text-muted"><?= $lang->get('total_reviews', 'Avis clients') ?></small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="stat-item">
                                        <h3 class="text-warning mb-1">
                                            <?= $stats['average_rating'] ?>/5
                                            <div class="stars mt-1">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star <?= $i <= floor($stats['average_rating']) ? 'text-warning' : 'text-muted' ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </h3>
                                        <small class="text-muted"><?= $lang->get('average_rating', 'Note moyenne') ?></small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="stat-item">
                                        <h3 class="text-success mb-1"><?= round(($stats['rating_distribution'][5] + $stats['rating_distribution'][4]) / max($stats['approved'], 1) * 100) ?>%</h3>
                                        <small class="text-muted"><?= $lang->get('satisfied_customers', 'Clients satisfaits') ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Colonne des témoignages -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h4 class="mb-0">
                        <i class="fas fa-star text-warning me-2"></i>
                        <?= $lang->get('latest_testimonials', 'Derniers témoignages') ?>
                    </h4>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($comments)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted"><?= $lang->get('no_testimonials', 'Aucun témoignage pour le moment') ?></h5>
                            <p class="text-muted"><?= $lang->get('be_first_testimonial', 'Soyez le premier à partager votre expérience !') ?></p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($comments as $index => $comment): ?>
                            <div class="testimonial-item <?= $index < count($comments) - 1 ? 'border-bottom' : '' ?>">
                                <div class="p-4">
                                    <div class="d-flex align-items-start">
                                        <div class="user-avatar me-3">
                                            <?= strtoupper(substr($comment['user_name'], 0, 1)) ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="mb-1 fw-bold"><?= htmlspecialchars($comment['user_name']) ?></h6>
                                                    <div class="text-muted small">
                                                        <?php if (!empty($comment['user_activity'])): ?>
                                                            <i class="fas fa-briefcase me-1"></i>
                                                            <?= htmlspecialchars($comment['user_activity']) ?>
                                                        <?php endif; ?>
                                                        <?php if (!empty($comment['user_location'])): ?>
                                                            <i class="fas fa-map-marker-alt ms-2 me-1"></i>
                                                            <?= htmlspecialchars($comment['user_location']) ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="rating mb-1">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <i class="fas fa-star <?= $i <= $comment['rating'] ? 'text-warning' : 'text-muted' ?>"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock me-1"></i>
                                                        <?= date('d/m/Y', strtotime($comment['created_at'])) ?>
                                                    </small>
                                                </div>
                                            </div>
                                            <blockquote class="mb-0">
                                                <p class="mb-0 text-dark">"<?= htmlspecialchars($comment['comment']) ?>"</p>
                                            </blockquote>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Colonne du formulaire -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" id="add-testimonial">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-plus me-2"></i>
                        <?= $lang->get('add_testimonial', 'Ajouter un témoignage') ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= htmlspecialchars($_SESSION['success_message']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['success_message']); ?>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/ProActiv/comments/submit" id="testimonialForm">
                        <div class="mb-3">
                            <label for="user_name" class="form-label">
                                <?= $lang->get('your_name', 'Votre nom') ?> <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="user_name" name="user_name" 
                                   value="<?= htmlspecialchars($form_data['user_name'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="user_email" class="form-label">
                                <?= $lang->get('your_email', 'Votre email') ?> <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control" id="user_email" name="user_email" 
                                   value="<?= htmlspecialchars($form_data['user_email'] ?? '') ?>" required>
                            <div class="form-text"><?= $lang->get('email_not_published', 'Votre email ne sera pas publié') ?></div>
                        </div>

                        <div class="mb-3">
                            <label for="user_activity" class="form-label">
                                <?= $lang->get('your_activity', 'Votre activité') ?>
                            </label>
                            <input type="text" class="form-control" id="user_activity" name="activity" 
                                   value="<?= htmlspecialchars($form_data['activity'] ?? '') ?>"
                                   placeholder="Ex: Consultant, Développeur, Graphiste...">
                        </div>

                        <div class="mb-3">
                            <label for="user_location" class="form-label">
                                <?= $lang->get('your_location', 'Votre localisation') ?>
                            </label>
                            <input type="text" class="form-control" id="user_location" name="location" 
                                   value="<?= htmlspecialchars($form_data['location'] ?? '') ?>"
                                   placeholder="Ex: Paris, France">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <?= $lang->get('your_rating', 'Votre note') ?> <span class="text-danger">*</span>
                            </label>
                            <div class="rating-input">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" 
                                           <?= (isset($form_data['rating']) && $form_data['rating'] == $i) ? 'checked' : '' ?>>
                                    <label for="star<?= $i ?>" class="star-label">
                                        <i class="fas fa-star"></i>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="comment" class="form-label">
                                <?= $lang->get('your_testimonial', 'Votre témoignage') ?> <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="comment" name="comment" rows="4" 
                                      placeholder="<?= $lang->get('testimonial_placeholder', 'Partagez votre expérience avec ProActiv...') ?>" 
                                      required><?= htmlspecialchars($form_data['comment'] ?? '') ?></textarea>
                            <div class="form-text">
                                <span id="charCount">0</span>/1000 <?= $lang->get('characters', 'caractères') ?>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane me-2"></i>
                            <?= $lang->get('submit_testimonial', 'Envoyer le témoignage') ?>
                        </button>
                    </form>

                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            <?= $lang->get('moderation_notice', 'Votre témoignage sera publié après modération') ?>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Répartition des notes -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar text-info me-2"></i>
                        <?= $lang->get('rating_distribution', 'Répartition des notes') ?>
                    </h6>
                </div>
                <div class="card-body">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <?php 
                        $count = $stats['rating_distribution'][$i] ?? 0;
                        $percentage = $stats['approved'] > 0 ? ($count / $stats['approved']) * 100 : 0;
                        ?>
                        <div class="d-flex align-items-center mb-2">
                            <div class="rating-label me-2" style="min-width: 60px;">
                                <?php for ($j = 1; $j <= $i; $j++): ?>
                                    <i class="fas fa-star text-warning small"></i>
                                <?php endfor; ?>
                            </div>
                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                <div class="progress-bar bg-warning" style="width: <?= $percentage ?>%"></div>
                            </div>
                            <small class="text-muted" style="min-width: 30px;"><?= $count ?></small>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.user-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.testimonial-item {
    transition: all 0.3s ease;
}

.testimonial-item:hover {
    background-color: #f8f9fa;
}

.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}

.rating-input input[type="radio"] {
    display: none;
}

.star-label {
    cursor: pointer;
    font-size: 1.5rem;
    color: #ddd;
    transition: color 0.2s;
    margin-right: 5px;
}

.rating-input input[type="radio"]:checked ~ .star-label,
.rating-input input[type="radio"]:hover ~ .star-label,
.star-label:hover {
    color: #ffc107;
}

.stat-item {
    padding: 1rem;
    border-radius: 10px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

blockquote {
    border-left: 4px solid #007bff;
    padding-left: 1rem;
    margin-left: 0;
    font-style: italic;
}

.stars i {
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .user-avatar {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .rating-input {
        justify-content: center;
    }
    
    .star-label {
        font-size: 1.2rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Compteur de caractères
    const commentTextarea = document.getElementById('comment');
    const charCount = document.getElementById('charCount');
    
    function updateCharCount() {
        const count = commentTextarea.value.length;
        charCount.textContent = count;
        charCount.className = count > 1000 ? 'text-danger' : count > 800 ? 'text-warning' : '';
    }
    
    commentTextarea.addEventListener('input', updateCharCount);
    updateCharCount();
    
    // Animation des étoiles
    const starLabels = document.querySelectorAll('.star-label');
    starLabels.forEach(label => {
        label.addEventListener('mouseenter', function() {
            const rating = this.previousElementSibling.value;
            starLabels.forEach((star, index) => {
                if (index >= starLabels.length - rating) {
                    star.style.color = '#ffc107';
                } else {
                    star.style.color = '#ddd';
                }
            });
        });
    });
    
    // Validation du formulaire
    const form = document.getElementById('testimonialForm');
    form.addEventListener('submit', function(e) {
        const rating = document.querySelector('input[name="rating"]:checked');
        if (!rating) {
            e.preventDefault();
            alert('Veuillez sélectionner une note');
            return false;
        }
        
        const comment = commentTextarea.value.trim();
        if (comment.length < 10) {
            e.preventDefault();
            alert('Le commentaire doit contenir au moins 10 caractères');
            return false;
        }
        
        if (comment.length > 1000) {
            e.preventDefault();
            alert('Le commentaire ne peut pas dépasser 1000 caractères');
            return false;
        }
    });
});
</script>

