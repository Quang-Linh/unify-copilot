<?php
// Script de diagnostic ultra-simple pour identifier l'erreur 500

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Diagnostic ProActiv</title></head><body>";
echo "<h1>🔍 Diagnostic ProActiv</h1>";

// Test 1: PHP de base
echo "<h2>1. Test PHP de base</h2>";
echo "✅ PHP fonctionne<br>";
echo "Version PHP: " . PHP_VERSION . "<br>";

// Test 2: Chemins
echo "<h2>2. Test des chemins</h2>";
$appRoot = dirname(__DIR__);
echo "App Root: " . htmlspecialchars($appRoot) . "<br>";
echo "Existe: " . (is_dir($appRoot) ? "✅ Oui" : "❌ Non") . "<br>";

// Test 3: Dossiers
echo "<h2>3. Test des dossiers</h2>";
$dirs = ['config', 'database', 'src', 'public'];
foreach ($dirs as $dir) {
    $path = $appRoot . '/' . $dir;
    echo "$dir: " . (is_dir($path) ? "✅ Existe" : "❌ Manquant") . "<br>";
}

// Test 4: Fichiers critiques
echo "<h2>4. Test des fichiers</h2>";
$files = [
    'database/schema.sql',
    'public/index.php',
    'src/Application.php'
];
foreach ($files as $file) {
    $path = $appRoot . '/' . $file;
    echo "$file: " . (is_file($path) ? "✅ Existe" : "❌ Manquant") . "<br>";
}

// Test 5: Permissions
echo "<h2>5. Test des permissions</h2>";
echo "Écriture dans app root: " . (is_writable($appRoot) ? "✅ OK" : "❌ Non") . "<br>";
if (is_dir($appRoot . '/config')) {
    echo "Écriture dans config: " . (is_writable($appRoot . '/config') ? "✅ OK" : "❌ Non") . "<br>";
}

// Test 6: Extensions PHP
echo "<h2>6. Extensions PHP</h2>";
$extensions = ['pdo', 'pdo_mysql', 'json'];
foreach ($extensions as $ext) {
    echo "$ext: " . (extension_loaded($ext) ? "✅ Chargée" : "❌ Manquante") . "<br>";
}

// Test 7: Variables serveur
echo "<h2>7. Variables serveur</h2>";
echo "REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'Non défini') . "<br>";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'Non défini') . "<br>";

// Test 8: Test simple de classe
echo "<h2>8. Test de classe PHP</h2>";
try {
    $test = new stdClass();
    echo "✅ Classes PHP OK<br>";
} catch (Exception $e) {
    echo "❌ Erreur classe: " . $e->getMessage() . "<br>";
}

// Test 9: Test PDO basique
echo "<h2>9. Test PDO (sans connexion)</h2>";
try {
    $drivers = PDO::getAvailableDrivers();
    echo "Drivers PDO: " . implode(', ', $drivers) . "<br>";
    echo "✅ PDO disponible<br>";
} catch (Exception $e) {
    echo "❌ Erreur PDO: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Retour à l'installateur</a></p>";
echo "</body></html>";
?>

