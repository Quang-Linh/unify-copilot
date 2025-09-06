<?php
/**
 * Point d'entrée principal de ProActiv
 * Compatible hébergement mutualisé O2Switch
 */

// Configuration PHP
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);

// Constantes de base
define('PROACTIV_START_TIME', microtime(true));
define('PROACTIV_ROOT', dirname(__DIR__));
define('PROACTIV_PUBLIC', __DIR__);

// Autoloader amélioré - CORRECTION PRINCIPALE
spl_autoload_register(function ($class) {
    // Recherche dans src/ et ses sous-dossiers
    $paths = [
        PROACTIV_ROOT . '/src/' . $class . '.php',
        PROACTIV_ROOT . '/src/Controllers/' . $class . '.php',
        PROACTIV_ROOT . '/src/Services/' . $class . '.php',
        PROACTIV_ROOT . '/src/Models/' . $class . '.php',
        PROACTIV_ROOT . '/src/Middleware/' . $class . '.php',
        PROACTIV_ROOT . '/src/Utils/' . $class . '.php'
    ];
    
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Chargement de la configuration
$config = require PROACTIV_ROOT . '/config/app.php';

// Gestion des erreurs selon l'environnement
if ($config['app']['debug']) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

// Démarrage de la session
session_start();

// Chargement de l'application
require_once PROACTIV_ROOT . '/src/Application.php';

try {
    $app = new Application($config);
    $app->run();
    
} catch (Throwable $e) {
    // Log de l'erreur
    error_log("ProActiv Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    
    // Affichage selon le mode debug
    if ($config['app']['debug']) {
        echo "<h1>Erreur ProActiv</h1>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>Fichier:</strong> " . $e->getFile() . " ligne " . $e->getLine() . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        http_response_code(500);
        include PROACTIV_ROOT . '/templates/errors/500.php';
    }
}
