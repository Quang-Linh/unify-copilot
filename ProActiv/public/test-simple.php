<?php
echo "<h1>Test ProActiv</h1>";
echo "<p>PHP fonctionne : " . phpversion() . "</p>";

// Test de chargement de la config
$configFile = dirname(__DIR__) . '/config/app.php';
echo "<p>Config existe : " . (file_exists($configFile) ? 'OUI' : 'NON') . "</p>";

if (file_exists($configFile)) {
    try {
        $config = require $configFile;
        echo "<p>Config chargée : OUI</p>";
        echo "<p>App name : " . ($config['app']['name'] ?? 'NON DÉFINI') . "</p>";
        echo "<p>Debug : " . ($config['app']['debug'] ? 'OUI' : 'NON') . "</p>";
        echo "<p>Templates path : " . ($config['app']['paths']['templates'] ?? 'NON DÉFINI') . "</p>";
    } catch (Exception $e) {
        echo "<p>Erreur config : " . $e->getMessage() . "</p>";
    }
}

// Test autoloader
echo "<h2>Test Autoloader</h2>";
$homeControllerFile = dirname(__DIR__) . '/src/Controllers/HomeController.php';
echo "<p>HomeController existe : " . (file_exists($homeControllerFile) ? 'OUI' : 'NON') . "</p>";

// Test de chargement des classes
spl_autoload_register(function ($class) {
    $paths = [
        dirname(__DIR__) . '/src/' . $class . '.php',
        dirname(__DIR__) . '/src/Controllers/' . $class . '.php',
        dirname(__DIR__) . '/src/Services/' . $class . '.php'
    ];
    
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            echo "<p>Chargé : $class depuis $file</p>";
            return;
        }
    }
    echo "<p>ERREUR : Impossible de charger $class</p>";
});

// Test des classes
echo "<h2>Test des classes</h2>";
try {
    if (class_exists('Application')) {
        echo "<p>Application : OK</p>";
    } else {
        echo "<p>Application : ERREUR</p>";
    }
} catch (Exception $e) {
    echo "<p>Application : ERREUR - " . $e->getMessage() . "</p>";
}
?>

