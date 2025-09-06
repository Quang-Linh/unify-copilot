<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$appRoot = dirname(__DIR__);
$configPhp = $appRoot . '/config/app.php';
$sqlFile = $appRoot . '/database/schema.sql';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['db_host'])) {
    $db_host = trim($_POST['db_host']);
    $db_name = trim($_POST['db_name']);
    $db_user = trim($_POST['db_user']);
    $db_pass = $_POST['db_pass'];
    $db_charset = trim($_POST['db_charset']);
    $base_url = rtrim($_POST['base_url'], '/');
    $env = $_POST['env'];

    $dsn = 'mysql:host=' . $db_host . ';dbname=' . $db_name . ';charset=' . $db_charset;
    try {
        $pdo = new PDO($dsn, $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
        echo '<h1>Erreur de connexion</h1>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<p><a href="simple.php">Retour</a></p>';
        exit;
    }

    if (!is_dir($appRoot . '/config')) {
        mkdir($appRoot . '/config', 0755, true);
    }

    if (is_file($configPhp)) {
        unlink($configPhp);
    }

    $debug = ($env != 'prod') ? 'true' : 'false';
    
    $configContent = '<?php
return [
    "app" => [
        "name" => "ProActiv",
        "version" => "1.0",
        "debug" => ' . $debug . ',
        "timezone" => "Europe/Paris",
        "locale" => "fr"
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
        "password_min_length" => 8
    ],
    "features" => [
        "comments" => true,
        "subscriptions" => true,
        "multi_country" => true,
        "analytics" => true
    ],
    "paths" => [
        "base_url" => "' . $base_url . '",
        "public" => "' . $base_url . '",
        "assets" => "' . $base_url . '/assets"
    ]
];';

    if (file_put_contents($configPhp, $configContent) === false) {
        echo '<h1>Erreur écriture config</h1>';
        echo '<p><a href="simple.php">Retour</a></p>';
        exit;
    }

    if (is_file($sqlFile)) {
        try {
            $sql = file_get_contents($sqlFile);
            $pdo->exec($sql);
        } catch (Exception $e) {
            echo '<h1>Erreur SQL</h1>';
            echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p><a href="simple.php">Retour</a></p>';
            exit;
        }
    }

    header('Location: ' . $base_url . '/index.php');
    exit;
}
?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Installation ProActiv Simple</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        input, select { width: 300px; padding: 5px; margin: 5px 0; }
        button { padding: 10px 20px; }
    </style>
</head>
<body>
    <h1>Installation ProActiv (Version Simple)</h1>
    <form method="post">
        <h3>Base de données</h3>
        <p>Hôte: <br><input name="db_host" value="localhost" required></p>
        <p>Base: <br><input name="db_name" value="proactiv_db" required></p>
        <p>User: <br><input name="db_user" value="proactiv_user" required></p>
        <p>Pass: <br><input type="password" name="db_pass" value=""></p>
        <p>Charset: <br><input name="db_charset" value="utf8mb4" required></p>
        
        <h3>Configuration</h3>
        <p>BASE_URL: <br><input name="base_url" value="/ProActiv/public" required></p>
        <p>Environnement: <br>
        <select name="env">
            <option value="community">Community</option>
            <option value="dev">Développement</option>
            <option value="staging">Staging</option>
            <option value="preprod">Préproduction</option>
            <option value="prod" selected>Production</option>
            <option value="formation">Formation</option>
        </select></p>
        
        <p><button type="submit">Installer</button></p>
    </form>
</body>
</html>

