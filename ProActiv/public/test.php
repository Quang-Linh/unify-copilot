<?php
/**
 * Test de fonctionnement ProActiv
 * À supprimer après déploiement
 */

echo "<h1>🎉 ProActiv - Test de fonctionnement</h1>";
echo "<p>✅ PHP " . PHP_VERSION . " détecté</p>";

// Test des extensions
$required = ['pdo', 'pdo_mysql', 'json', 'openssl', 'mbstring'];
foreach ($required as $ext) {
    $status = extension_loaded($ext) ? '✅' : '❌';
    echo "<p>$status Extension $ext</p>";
}

// Test de la configuration
if (file_exists('../config/app.php')) {
    echo "<p>✅ Fichier de configuration trouvé</p>";
    $config = require '../config/app.php';
    echo "<p>✅ Configuration chargée : " . $config['app']['name'] . "</p>";
} else {
    echo "<p>❌ Fichier de configuration manquant</p>";
}

// Test des dossiers
$dirs = ['../templates', '../src', '../assets', '../storage'];
foreach ($dirs as $dir) {
    $status = is_dir($dir) ? '✅' : '❌';
    echo "<p>$status Dossier " . basename($dir) . "</p>";
}

echo "<hr>";
echo "<p><strong>Prochaines étapes :</strong></p>";
echo "<ol>";
echo "<li>Importer la base de données (database/schema.sql)</li>";
echo "<li>Configurer les paramètres de BDD dans config/app.php</li>";
echo "<li>Tester l'accès à la page d'accueil</li>";
echo "<li>Supprimer ce fichier de test</li>";
echo "</ol>";

echo "<p><a href='index.php'>🏠 Accéder à ProActiv</a></p>";
?>
