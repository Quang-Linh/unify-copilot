<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test Routage ProActiv</h1>";

echo "<h2>Variables serveur</h2>";
echo "<p><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'NON DÉFINI') . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'NON DÉFINI') . "</p>";
echo "<p><strong>REQUEST_METHOD:</strong> " . ($_SERVER['REQUEST_METHOD'] ?? 'NON DÉFINI') . "</p>";

// Simulation de parsePath
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH);

echo "<h2>Parsing du chemin</h2>";
echo "<p><strong>URI brute:</strong> $uri</p>";
echo "<p><strong>Path parsé:</strong> $path</p>";

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$scriptDir = dirname($scriptName);

echo "<p><strong>Script name:</strong> $scriptName</p>";
echo "<p><strong>Script dir:</strong> $scriptDir</p>";

if ($scriptDir !== '/' && strpos($path, $scriptDir) === 0) {
    $path = substr($path, strlen($scriptDir));
    echo "<p><strong>Path après suppression préfixe:</strong> $path</p>";
}

$finalPath = '/' . trim($path, '/');
$finalPath = $finalPath === '/' ? '/' : $finalPath;

echo "<p><strong>Path final:</strong> '$finalPath'</p>";

// Test des routes définies
define('PROACTIV_ROOT', dirname(__DIR__));
require_once PROACTIV_ROOT . '/src/Request.php';

$request = new Request();
echo "<h2>Objet Request</h2>";
echo "<p><strong>Method:</strong> " . $request->getMethod() . "</p>";
echo "<p><strong>Path:</strong> '" . $request->getPath() . "'</p>";

// Simulation de routes
$routes = [
    ['method' => 'GET', 'path' => '/', 'handler' => 'HomeController@index'],
    ['method' => 'GET', 'path' => '/about', 'handler' => 'HomeController@about'],
    ['method' => 'GET', 'path' => '/contact', 'handler' => 'HomeController@contact']
];

echo "<h2>Test correspondance routes</h2>";
$requestPath = $request->getPath();
$requestMethod = $request->getMethod();

foreach ($routes as $route) {
    $match = ($route['method'] === $requestMethod && $route['path'] === $requestPath);
    $status = $match ? '✅ MATCH' : '❌ NO MATCH';
    echo "<p>Route: {$route['method']} {$route['path']} → $status</p>";
}
?>

