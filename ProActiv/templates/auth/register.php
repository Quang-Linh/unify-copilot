<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>
                        <?= $this->lang->translate('register') ?>
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="/register" id="registerForm">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        
                        <div class="mb-3">
                            <label for="firstname" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="lastname" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="8">
                            <div class="form-text">Minimum 8 caractères</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                J'accepte les <a href="/terms" target="_blank">conditions d'utilisation</a>
                            </label>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="newsletter" name="newsletter">
                            <label class="form-check-label" for="newsletter">
                                Je souhaite recevoir la newsletter ProActiv
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-user-plus me-2"></i>S'inscrire
                        </button>
                    </form>
                    
                    <hr>
                    <div class="text-center">
                        <a href="/login">Déjà un compte ? Se connecter</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;
    
    if (password !== passwordConfirm) {
        e.preventDefault();
        alert('Les mots de passe ne correspondent pas');
        return false;
    }
});
</script>
