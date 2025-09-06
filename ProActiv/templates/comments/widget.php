<?php $lang = $lang ?? new LanguageService(); ?>
<!-- Widget Témoignages -->
<div class="testimonials-widget">
    <div class="testimonials-header">
        <h3>
            <i class="fas fa-star text-warning"></i>
            <?= $lang->get('customer_testimonials', 'Témoignages clients') ?>
        </h3>
        <div class="rating-summary">
            <div class="stars">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fas fa-star <?= $i <= floor($stats['average_rating']) ? 'text-warning' : 'text-muted' ?>"></i>
                <?php endfor; ?>
            </div>
            <span class="rating-text">
                <?= $stats['average_rating'] ?>/5 
                (<?= $stats['approved'] ?> <?= $lang->get('reviews', 'avis') ?>)
            </span>
        </div>
    </div>
    
    <div class="testimonials-list">
        <?php foreach ($comments as $comment): ?>
            <div class="testimonial-item">
                <div class="testimonial-header">
                    <div class="user-info">
                        <div class="user-avatar">
                            <?= strtoupper(substr($comment['user_name'], 0, 1)) ?>
                        </div>
                        <div class="user-details">
                            <h5><?= htmlspecialchars($comment['user_name']) ?></h5>
                            <small class="text-muted">
                                <?= htmlspecialchars($comment['activity'] ?? 'Auto-entrepreneur') ?>
                                <?php if (!empty($comment['location'])): ?>
                                    • <?= htmlspecialchars($comment['location']) ?>
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                    <div class="rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?= $i <= $comment['rating'] ? 'text-warning' : 'text-muted' ?>"></i>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="testimonial-content">
                    <p>"<?= htmlspecialchars($comment['comment']) ?>"</p>
                    <small class="text-muted">
                        <i class="fas fa-clock"></i>
                        <?= date('d/m/Y', strtotime($comment['created_at'])) ?>
                    </small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="testimonials-footer">
        <a href="/ProActiv/comments" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-comments"></i>
            <?= $lang->get('see_all_testimonials', 'Voir tous les témoignages') ?>
        </a>
        <a href="/ProActiv/comments#add-testimonial" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i>
            <?= $lang->get('add_testimonial', 'Ajouter un témoignage') ?>
        </a>
    </div>
</div>

<style>
.testimonials-widget {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    margin: 2rem 0;
}

.testimonials-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f8f9fa;
}

.testimonials-header h3 {
    margin: 0;
    color: #2c3e50;
    font-weight: 600;
}

.rating-summary {
    text-align: right;
}

.stars {
    margin-bottom: 0.25rem;
}

.rating-text {
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 500;
}

.testimonial-item {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border-left: 4px solid #007bff;
    transition: all 0.3s ease;
}

.testimonial-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.testimonial-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.user-info {
    display: flex;
    align-items: center;
}

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
    margin-right: 1rem;
}

.user-details h5 {
    margin: 0;
    color: #2c3e50;
    font-weight: 600;
}

.user-details small {
    line-height: 1.4;
}

.rating {
    flex-shrink: 0;
}

.testimonial-content p {
    color: #495057;
    line-height: 1.6;
    margin-bottom: 0.5rem;
    font-style: italic;
}

.testimonials-footer {
    text-align: center;
    padding-top: 1rem;
    border-top: 1px solid #e9ecef;
}

.testimonials-footer .btn {
    margin: 0 0.5rem;
    border-radius: 20px;
    padding: 0.5rem 1.5rem;
}

@media (max-width: 768px) {
    .testimonials-header {
        flex-direction: column;
        text-align: center;
    }
    
    .rating-summary {
        text-align: center;
        margin-top: 1rem;
    }
    
    .testimonial-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .rating {
        margin-top: 0.5rem;
    }
    
    .testimonials-footer .btn {
        display: block;
        width: 100%;
        margin: 0.25rem 0;
    }
}
</style>

