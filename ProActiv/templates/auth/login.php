<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        <?= $this->lang->translate('login') ?>
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="/login">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="form-text">Utilisez demo@proactiv.fr pour tester</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="form-text">Utilisez demo123 pour tester</div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Se souvenir de moi</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                        </button>
                    </form>
                    
                    <hr>
                    <div class="text-center">
                        <a href="/register">Pas encore de compte ? S'inscrire</a><br>
                        <a href="/forgot-password">Mot de passe oublié ?</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
