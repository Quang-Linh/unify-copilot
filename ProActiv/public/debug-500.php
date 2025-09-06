<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug ProActiv - Erreurs 500</h1>";

// Test 1: PHP de base
echo "<h2>1. Test PHP de base</h2>";
echo "<p>✅ PHP fonctionne : " . phpversion() . "</p>";

// Test 2: Fichiers critiques
echo "<h2>2. Fichiers critiques</h2>";
$files = [
    'Config' => dirname(__DIR__) . '/config/app.php',
    'Application' => dirname(__DIR__) . '/src/Application.php',
    'Router' => dirname(__DIR__) . '/src/Router.php',
    'HomeController' => dirname(__DIR__) . '/src/Controllers/HomeController.php'
];

foreach ($files as $name => $file) {
    if (file_exists($file)) {
        echo "<p>✅ $name : OK</p>";
    } else {
        echo "<p>❌ $name : MANQUANT ($file)</p>";
    }
}

// Test 3: Chargement config
echo "<h2>3. Test configuration</h2>";
try {
    $config = require dirname(__DIR__) . '/config/app.php';
    echo "<p>✅ Config chargée</p>";
    echo "<p>App name: " . ($config['app']['name'] ?? 'NON DÉFINI') . "</p>";
    echo "<p>Debug: " . ($config['app']['debug'] ? 'OUI' : 'NON') . "</p>";
} catch (Exception $e) {
    echo "<p>❌ Erreur config: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p>❌ Erreur fatale config: " . $e->getMessage() . "</p>";
}

// Test 4: Autoloader
echo "<h2>4. Test autoloader</h2>";
define('PROACTIV_ROOT', dirname(__DIR__));

spl_autoload_register(function ($class) {
    $paths = [
        PROACTIV_ROOT . '/src/' . $class . '.php',
        PROACTIV_ROOT . '/src/Controllers/' . $class . '.php',
        PROACTIV_ROOT . '/src/Services/' . $class . '.php',
        PROACTIV_ROOT . '/src/Models/' . $class . '.php'
    ];
    
    foreach ($paths as $file) {
        if (file_exists($file)) {
            echo "<p>Chargement: $class depuis $file</p>";
            require_once $file;
            return;
        }
    }
    echo "<p>❌ Impossible de charger: $class</p>";
});

// Test 5: Classes principales
echo "<h2>5. Test classes</h2>";
$classes = ['Application', 'Router', 'HomeController'];

foreach ($classes as $class) {
    try {
        if (class_exists($class)) {
            echo "<p>✅ $class : OK</p>";
        } else {
            echo "<p>❌ $class : NON TROUVÉE</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ $class : ERREUR - " . $e->getMessage() . "</p>";
    } catch (Error $e) {
        echo "<p>❌ $class : ERREUR FATALE - " . $e->getMessage() . "</p>";
    }
}

// Test 6: Instanciation Application
echo "<h2>6. Test instanciation</h2>";
try {
    if (class_exists('Application') && isset($config)) {
        $app = new Application($config);
        echo "<p>✅ Application instanciée</p>";
    } else {
        echo "<p>❌ Impossible d'instancier Application</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Erreur instanciation: " . $e->getMessage() . "</p>";
    echo "<p>Trace: " . $e->getTraceAsString() . "</p>";
} catch (Error $e) {
    echo "<p>❌ Erreur fatale instanciation: " . $e->getMessage() . "</p>";
    echo "<p>Trace: " . $e->getTraceAsString() . "</p>";
}

echo "<h2>Conclusion</h2>";
echo "<p>Si vous voyez ce message, PHP fonctionne. Les erreurs 500 viennent d'un problème spécifique identifié ci-dessus.</p>";
?>

