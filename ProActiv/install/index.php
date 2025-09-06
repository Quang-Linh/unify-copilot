<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$appRoot = dirname(__DIR__);
$configPhp = $appRoot . '/config/app.php';
$sqlFile = $appRoot . '/database/schema.sql';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['db_host'])) {
    $username = trim($_POST['username']);
    $db_host = trim($_POST['db_host']);
    $db_name_prefix = trim($_POST['db_name_prefix']);
    $db_name = $db_name_prefix . '_proactiv'; // Suffixe verrouillé
    $db_user = trim($_POST['db_user']);
    $db_pass = $_POST['db_pass'];
    $db_charset = trim($_POST['db_charset']);
    $base_url = '/ProActiv/public'; // URL fixe sans tilde
    $env = $_POST['env'];

    // Test connexion PDO
    $dsn = 'mysql:host=' . $db_host . ';dbname=' . $db_name . ';charset=' . $db_charset;
    try {
        $pdo = new PDO($dsn, $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
        http_response_code(500);
        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Erreur DB</title>';
        echo '<style>body{font-family:Arial;margin:2rem;color:#721c24;background:#f8d7da;padding:1rem;border-radius:8px}</style></head><body>';
        echo '<h1>🚫 Échec connexion MySQL</h1>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        echo '<p><a href="index.php" style="color:#0d6efd">&larr; Retour à l\'installateur</a></p>';
        echo '</body></html>';
        exit;
    }

    // Créer dossiers nécessaires
    if (!is_dir($appRoot . '/config')) {
        mkdir($appRoot . '/config', 0755, true);
    }
    if (!is_dir($appRoot . '/storage')) {
        mkdir($appRoot . '/storage', 0755, true);
    }
    if (!is_dir($appRoot . '/storage/logs')) {
        mkdir($appRoot . '/storage/logs', 0755, true);
    }
    if (!is_dir($appRoot . '/storage/cache')) {
        mkdir($appRoot . '/storage/cache', 0755, true);
    }
    if (!is_dir($appRoot . '/storage/sessions')) {
        mkdir($appRoot . '/storage/sessions', 0755, true);
    }
    if (!is_dir($appRoot . '/public/uploads')) {
        mkdir($appRoot . '/public/uploads', 0755, true);
    }

    // Supprimer config existant
    if (is_file($configPhp)) {
        unlink($configPhp);
    }

    // Générer config avec la structure attendue par Application.php
    $debug = ($env != 'prod') ? 'true' : 'false';
    
    $configContent = '<?php
return [
    "app" => [
        "name" => "ProActiv",
        "version" => "1.0",
        "debug" => ' . $debug . ',
        "timezone" => "Europe/Paris",
        "locale" => "fr",
        "maintenance" => false,
        "paths" => [
            "root" => "' . dirname(__DIR__) . '",
            "base_url" => "' . $base_url . '",
            "public" => "' . $base_url . '",
            "assets" => "' . $base_url . '/assets",
            "templates" => "' . dirname(__DIR__) . '/templates",
            "storage" => "' . dirname(__DIR__) . '/storage",
            "uploads" => "' . dirname(__DIR__) . '/public/uploads"
        ]
    ],
    "database" => [
        "enabled" => true,
        "host" => "' . $db_host . '",
        "port" => 3306,
        "name" => "' . $db_name . '",
        "username" => "' . $db_user . '",
        "password" => "' . $db_pass . '",
        "charset" => "' . $db_charset . '",
        "options" => [
            "PDO::ATTR_ERRMODE" => "PDO::ERRMODE_EXCEPTION",
            "PDO::ATTR_DEFAULT_FETCH_MODE" => "PDO::FETCH_ASSOC"
        ]
    ],
    "security" => [
        "csrf_protection" => true,
        "session_lifetime" => 3600,
        "password_min_length" => 8,
        "jwt_secret" => "' . bin2hex(random_bytes(32)) . '",
        "encryption_key" => "' . bin2hex(random_bytes(16)) . '"
    ],
    "features" => [
        "comments" => true,
        "subscriptions" => true,
        "multi_country" => true,
        "analytics" => true,
        "forum" => true,
        "documents" => true,
        "calculators" => true
    ],
    "paths" => [
        "root" => "' . dirname(__DIR__) . '",
        "base_url" => "' . $base_url . '",
        "public" => "' . $base_url . '",
        "assets" => "' . $base_url . '/assets",
        "templates" => "' . dirname(__DIR__) . '/templates",
        "storage" => "' . dirname(__DIR__) . '/storage",
        "uploads" => "' . dirname(__DIR__) . '/public/uploads"
    ],
    "mail" => [
        "driver" => "smtp",
        "host" => "localhost",
        "port" => 587,
        "username" => "",
        "password" => "",
        "encryption" => "tls",
        "from_address" => "noreply@proactiv.local",
        "from_name" => "ProActiv"
    ],
    "cache" => [
        "enabled" => false,
        "driver" => "file",
        "ttl" => 3600
    ],
    "logging" => [
        "enabled" => true,
        "level" => "error",
        "file" => "' . dirname(__DIR__) . '/storage/logs/app.log"
    ]
];';

    if (file_put_contents($configPhp, $configContent) === false) {
        http_response_code(500);
        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Erreur</title>';
        echo '<style>body{font-family:Arial;margin:2rem;color:#721c24;background:#f8d7da;padding:1rem;border-radius:8px}</style></head><body>';
        echo '<h1>🚫 Impossible d\'écrire config/app.php</h1>';
        echo '<p>Vérifiez les permissions du dossier config/</p>';
        echo '<p><a href="index.php" style="color:#0d6efd">&larr; Retour à l\'installateur</a></p>';
        echo '</body></html>';
        exit;
    }

    // Import SQL obligatoire
    if (!is_file($sqlFile)) {
        http_response_code(500);
        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Erreur</title>';
        echo '<style>body{font-family:Arial;margin:2rem;color:#721c24;background:#f8d7da;padding:1rem;border-radius:8px}</style></head><body>';
        echo '<h1>🚫 Fichier SQL introuvable</h1>';
        echo '<p>Le fichier <code>database/schema.sql</code> est requis pour l\'installation.</p>';
        echo '<p><a href="index.php" style="color:#0d6efd">&larr; Retour à l\'installateur</a></p>';
        echo '</body></html>';
        exit;
    }
    
    try {
        $sql = file_get_contents($sqlFile);
        if (trim($sql) === '') {
            throw new Exception('Le fichier SQL est vide.');
        }
        
        // Nettoyer la base avant import pour éviter les doublons
        $cleanupSql = "
        SET FOREIGN_KEY_CHECKS = 0;
        DROP TABLE IF EXISTS comments;
        DROP TABLE IF EXISTS subscriptions;
        DROP TABLE IF EXISTS users;
        DROP TABLE IF EXISTS countries;
        DROP TABLE IF EXISTS calculations;
        SET FOREIGN_KEY_CHECKS = 1;
        ";
        
        $pdo->exec($cleanupSql);
        $pdo->exec($sql);
    } catch (Exception $e) {
        http_response_code(500);
        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Erreur SQL</title>';
        echo '<style>body{font-family:Arial;margin:2rem;color:#721c24;background:#f8d7da;padding:1rem;border-radius:8px}</style></head><body>';
        echo '<h1>🚫 Import SQL échoué</h1>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        echo '<p><a href="index.php" style="color:#0d6efd">&larr; Retour à l\'installateur</a></p>';
        echo '</body></html>';
        exit;
    }

    // Succès - Redirection
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Installation réussie</title>';
    echo '<style>body{font-family:Arial;margin:2rem;color:#155724;background:#d4edda;padding:2rem;border-radius:8px;text-align:center}</style></head><body>';
    echo '<h1>✅ Installation ProActiv réussie !</h1>';
    echo '<p>La base de données a été configurée et le schéma importé avec succès.</p>';
    echo '<p><strong>Environnement :</strong> ' . htmlspecialchars($env) . '</p>';
    echo '<p><a href="/ProActiv/public/" style="background:#ff6b35;color:white;padding:10px 20px;text-decoration:none;border-radius:5px">🚀 Accéder à ProActiv</a></p>';
    echo '</body></html>';
    exit;
}
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Installation ProActiv</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #ff6b35;
            --primary-dark: #e55a2b;
            --secondary: #2c3e50;
            --success: #27ae60;
            --danger: #e74c3c;
            --warning: #f39c12;
            --info: #3498db;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 1rem;
            color: #333;
        }
        
        .container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }
        
        .header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .form-container {
            padding: 2rem;
        }
        
        .form-section {
            background: var(--light);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary);
        }
        
        .form-section h3 {
            color: var(--secondary);
            margin-bottom: 1rem;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--secondary);
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
        }
        
        .password-field {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--primary);
            font-size: 0.9rem;
            user-select: none;
            padding: 0.25rem;
        }
        
        .password-toggle:hover {
            color: var(--primary-dark);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
        
        .info-box {
            background: linear-gradient(135deg, #e8f4fd, #d1ecf1);
            border: 1px solid #bee5eb;
            border-radius: 10px;
            padding: 1rem;
            margin: 1.5rem 0;
            color: #0c5460;
        }
        
        .info-box strong {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .btn-install {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 15px;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .btn-install:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 107, 53, 0.3);
        }
        
        .btn-install:active {
            transform: translateY(0);
        }
        
        .small-text {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
        
        .required::after {
            content: " *";
            color: var(--danger);
            font-weight: bold;
        }
        
        .icon {
            width: 1.2rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-rocket"></i> ProActiv</h1>
            <p>Assistant d'installation pour auto-entrepreneurs</p>
        </div>
        
        <div class="form-container">
            <form method="post">
                <div class="form-section">
                    <h3><i class="fas fa-user icon"></i> Configuration utilisateur</h3>
                    
                    <div class="form-group">
                        <label for="username" class="required">Nom d'utilisateur du serveur</label>
                        <input type="text" id="username" name="username" class="form-control" value="" required placeholder="ex: user123">
                        <div class="small-text">Utilisé pour le chemin /home/<strong>username</strong>/public_html/ProActiv</div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-database icon"></i> Configuration de la base de données</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="db_host" class="required">Hôte de la base de données</label>
                            <input type="text" id="db_host" name="db_host" class="form-control" value="localhost" required>
                        </div>
                        <div class="form-group">
                            <label for="db_charset">Charset</label>
                            <input type="text" id="db_charset" name="db_charset" class="form-control" value="utf8mb4" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="db_name_prefix" class="required">Préfixe de la base de données</label>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="text" id="db_name_prefix" name="db_name_prefix" class="form-control" value="" required style="flex: 1;" placeholder="ex: user123">
                                <span style="font-weight: bold; color: var(--primary);">_proactiv</span>
                            </div>
                            <div class="small-text">La base sera nommée : <strong>préfixe_proactiv</strong></div>
                        </div>
                        <div class="form-group">
                            <label for="db_user" class="required">Utilisateur</label>
                            <input type="text" id="db_user" name="db_user" class="form-control" value="" required placeholder="ex: user123_proactiv">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="db_pass">Mot de passe</label>
                        <div class="password-field">
                            <input type="password" id="db_pass" name="db_pass" class="form-control" value="">
                            <span class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye"></i> Afficher
                            </span>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-cog icon"></i> Configuration du site</h3>
                    
                    <div class="form-group">
                        <label for="env" class="required">Environnement</label>
                        <select id="env" name="env" class="form-control" required>
                            <option value="community">Community</option>
                            <option value="dev">Développement</option>
                            <option value="staging">Staging/Qualification</option>
                            <option value="preprod">Préproduction</option>
                            <option value="prod" selected>Production</option>
                            <option value="formation">Formation</option>
                        </select>
                    </div>
                    
                    <div class="info-box" style="background: #fff3cd; border-color: #ffeaa7; color: #856404;">
                        <strong>
                            <i class="fas fa-info-circle"></i>
                            URL automatique
                        </strong>
                        L'URL de base sera automatiquement configurée comme : <code>/ProActiv/public</code>
                        <br><small>Chemin serveur : /home/<span id="path-preview">username</span>/public_html/ProActiv</small>
                    </div>
                </div>

                <div class="info-box">
                    <strong>
                        <i class="fas fa-info-circle"></i>
                        Import automatique
                    </strong>
                    Le schéma de base de données <code>database/schema.sql</code> sera importé automatiquement lors de l'installation. Cette étape est obligatoire pour le bon fonctionnement de ProActiv.
                </div>

                <button type="submit" class="btn-install">
                    <i class="fas fa-download"></i>
                    Installer ProActiv
                </button>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const field = document.getElementById('db_pass');
            const toggle = document.querySelector('.password-toggle');
            const icon = toggle.querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'fas fa-eye-slash';
                toggle.innerHTML = '<i class="fas fa-eye-slash"></i> Masquer';
            } else {
                field.type = 'password';
                icon.className = 'fas fa-eye';
                toggle.innerHTML = '<i class="fas fa-eye"></i> Afficher';
            }
        }
        
        // Synchroniser les champs basés sur le username
        document.getElementById('username').addEventListener('input', function() {
            const username = this.value;
            
            // Mettre à jour l'aperçu du chemin serveur
            document.getElementById('path-preview').textContent = username || 'username';
            
            // Synchroniser le préfixe de la base de données
            document.getElementById('db_name_prefix').value = username;
            
            // Synchroniser l'utilisateur de la base de données
            if (username) {
                document.getElementById('db_user').value = username + '_proactiv';
            }
        });
        
        // Synchroniser aussi quand on change le préfixe directement
        document.getElementById('db_name_prefix').addEventListener('input', function() {
            const prefix = this.value;
            if (prefix) {
                document.getElementById('db_user').value = prefix + '_proactiv';
            }
        });
    </script>
</body>
</html>

