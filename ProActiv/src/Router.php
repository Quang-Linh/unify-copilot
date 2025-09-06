<?php
/**
 * Routeur ProActiv
 * Gestion complète des routes avec middleware et authentification
 */

class Router
{
    private $app;
    private $routes = [];
    private $middleware = [];
    
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->defineRoutes();
    }
    
    /**
     * Définition de toutes les routes
     */
    private function defineRoutes()
    {
        // Routes publiques
        $this->addRoute('GET', '/', 'HomeController@index');
        $this->addRoute('GET', '/about', 'HomeController@about');
        $this->addRoute('GET', '/contact', 'HomeController@contact');
        $this->addRoute('POST', '/contact', 'HomeController@contact');
        
        // Routes d'authentification
        $this->addRoute('GET', '/login', 'AuthController@login');
        $this->addRoute('POST', '/login', 'AuthController@login');
        $this->addRoute('GET', '/logout', 'AuthController@logout');
        
        // Routes protégées (nécessitent authentification)
        $this->addRoute('GET', '/dashboard', 'DashboardController@index', ['auth']);
        $this->addRoute('GET', '/profile', 'ProfileController@index', ['auth']);
        $this->addRoute('POST', '/profile', 'ProfileController@update', ['auth']);
        
        // Routes publiques
        $this->addRoute('GET', '/about', 'AboutController@index');
        $this->addRoute('GET', '/contact', 'ContactController@index');
        $this->addRoute('POST', '/contact/send', 'ContactController@send');
        
        // Routes de changement de langue
        $this->addRoute('GET', '/language/{lang}', 'LanguageController@setLanguage');
        
        // Routes des calculateurs
        $this->addRoute('GET', '/calculators', 'CalculatorController@index', ['auth']);
        $this->addRoute('GET', '/calculators/charges-sociales', 'CalculatorController@chargesSociales', ['auth']);
        $this->addRoute('POST', '/calculators/charges-sociales', 'CalculatorController@calculateCharges', ['auth']);
        $this->addRoute('GET', '/calculators/impots', 'CalculatorController@impots', ['auth']);
        $this->addRoute('POST', '/calculators/impots', 'CalculatorController@calculateImpots', ['auth']);
        
        // Routes du forum
        $this->addRoute('GET', '/forum', 'ForumController@index', ['auth']);
        $this->addRoute('GET', '/forum/category/{id}', 'ForumController@category', ['auth']);
        $this->addRoute('GET', '/forum/topic/{id}', 'ForumController@topic', ['auth']);
        $this->addRoute('POST', '/forum/topic/{id}/reply', 'ForumController@reply', ['auth']);
        $this->addRoute('GET', '/forum/new-topic', 'ForumController@newTopic', ['auth']);
        $this->addRoute('POST', '/forum/new-topic', 'ForumController@createTopic', ['auth']);
        $this->addRoute('GET', '/forum/search', 'ForumController@search', ['auth']);
        
        // Routes des documents
        $this->addRoute("GET", "/documents", "DocumentController@index", ["auth"]);
        $this->addRoute("GET", "/documents/create", "DocumentController@create", ["auth"]);
        $this->addRoute("POST", "/documents/generate", "DocumentController@generate", ["auth"]);
        $this->addRoute("GET", "/documents/{id}/download", "DocumentController@download", ["auth"]);
        
        // API Routes
        $this->addRoute('GET', '/api/stats', 'APIController@stats');
        $this->addRoute('POST', '/api/calculate', 'APIController@calculate', ['api']);
        
        // Routes d'administration
        $this->addRoute('GET', '/admin', 'AdminController@index', ['auth', 'admin']);
        $this->addRoute('GET', '/admin/users', 'AdminController@users', ['auth', 'admin']);
        $this->addRoute('GET', '/admin/forum', 'AdminController@forum', ['auth', 'admin']);
        $this->addRoute('GET', '/admin/documents', 'AdminController@documents', ['auth', 'admin']);
        $this->addRoute('GET', '/admin/analytics', 'AdminController@analytics', ['auth', 'admin']);
        $this->addRoute('GET', '/admin/settings', 'AdminController@settings', ['auth', 'admin']);
        $this->addRoute('POST', '/admin/settings', 'AdminController@settings', ['auth', 'admin']);
        $this->addRoute('GET', '/admin/logs', 'AdminController@logs', ['auth', 'admin']);
        $this->addRoute('POST', '/admin/maintenance', 'AdminController@maintenance', ['auth', 'admin']);
        $this->addRoute('GET', '/admin/stats', 'AdminController@getStats', ['auth', 'admin']);
        $this->addRoute('GET', '/admin/audit', 'AdminController@audit', ['auth', 'admin']);
        
        // Routes SuperAdmin
        $this->addRoute('GET', '/superadmin', 'SuperAdminController@dashboard', ['auth', 'admin']);
        $this->addRoute('GET', '/superadmin/users', 'SuperAdminController@users', ['auth', 'admin']);
        $this->addRoute('GET', '/superadmin/subscriptions', 'SuperAdminController@subscriptions', ['auth', 'admin']);
        $this->addRoute('GET', '/superadmin/comments', 'SuperAdminController@comments', ['auth', 'admin']);
        $this->addRoute('GET', '/superadmin/analytics', 'SuperAdminController@analytics', ['auth', 'admin']);
        $this->addRoute('GET', '/superadmin/settings', 'SuperAdminController@settings', ['auth', 'admin']);
        $this->addRoute('POST', '/superadmin/settings', 'SuperAdminController@saveSettings', ['auth', 'admin']);
        $this->addRoute('GET', '/superadmin/logs', 'SuperAdminController@logs', ['auth', 'admin']);
        $this->addRoute('POST', '/superadmin/maintenance', 'SuperAdminController@maintenance', ['auth', 'admin']);
        $this->addRoute('POST', '/superadmin/users/toggle-status', 'SuperAdminController@toggleUserStatus', ['auth', 'admin']);
        $this->addRoute('POST', '/superadmin/users/force-password', 'SuperAdminController@forcePassword', ['auth', 'admin']);
        $this->addRoute('POST', '/superadmin/comments/moderate', 'SuperAdminController@moderateComment', ['auth', 'admin']);
        
        // Routes pour la gestion des pays
        $this->addRoute('GET', '/country/{country}', 'CountryController@change');
        $this->addRoute('GET', '/api/country/{country}', 'CountryController@getCountryInfo');
        $this->addRoute('GET', '/countries', 'CountryController@index');
        
        // Routes d'inscription
        $this->addRoute('GET', '/register', 'RegisterController@index');
        $this->addRoute('POST', '/register/process', 'RegisterController@process');
        $this->addRoute('GET', '/register/payment', 'RegisterController@payment');
        $this->addRoute('POST', '/register/process-payment', 'RegisterController@processPayment');
        $this->addRoute('GET', '/register/welcome', 'RegisterController@welcome');
        $this->addRoute('POST', '/register/check-email', 'RegisterController@checkEmail');
        
        // Routes des commentaires/témoignages
        $this->addRoute('GET', '/comments', 'CommentController@index');
        $this->addRoute('POST', '/comments/submit', 'CommentController@submit');
        $this->addRoute('GET', '/comments/api', 'CommentController@api');
        $this->addRoute('GET', '/comments/widget', 'CommentController@widget');
        $this->addRoute('POST', '/comments/moderate', 'CommentController@moderate', ['auth', 'admin']);
    }
    
    /**
     * Ajout d'une route
     */
    private function addRoute(string $method, string $path, string $handler, array $middleware = [])
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }
    
    /**
     * Dispatch de la requête
     */
    public function dispatch(Request $request)
    {
        $method = $request->getMethod();
        $path = $request->getPath();
        
        // Recherche de la route correspondante
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $path)) {
                
                // Application des middleware
                if (!$this->applyMiddleware($route['middleware'], $request)) {
                    return;
                }
                
                // Exécution du contrôleur
                $params = $this->extractParams($route['path'], $path);
                $this->executeRoute($route, $request, $params);
                return;
            }
        }
        
        // Route non trouvée
        $this->handleNotFound();
    }
    
    /**
     * Correspondance de chemin avec paramètres
     */
    private function matchPath(string $routePath, string $requestPath): bool
    {
        // Conversion des paramètres {id} en regex
        $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $requestPath);
    }
    
    /**
     * Extraction des paramètres de l'URL
     */
    private function extractParams(string $routePath, string $requestPath): array
    {
        $routeParts = explode('/', trim($routePath, '/'));
        $requestParts = explode('/', trim($requestPath, '/'));
        
        $params = [];
        foreach ($routeParts as $index => $part) {
            if (preg_match('/\{([^}]+)\}/', $part, $matches)) {
                $params[$matches[1]] = $requestParts[$index] ?? null;
            }
        }
        
        return $params;
    }
    
    /**
     * Application des middleware
     */
    private function applyMiddleware(array $middleware, Request $request): bool
    {
        foreach ($middleware as $middlewareName) {
            switch ($middlewareName) {
                case 'auth':
                    if (!isset($_SESSION['user_id'])) {
                        $_SESSION['flash_message'] = 'Vous devez être connecté';
                        $_SESSION['flash_type'] = 'warning';
                        header('Location: /login');
                        return false;
                    }
                    break;
                    
                case 'admin':
                    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
                        http_response_code(403);
                        include $this->app->getConfig()['app']['paths']['templates'] . '/errors/403.php';
                        return false;
                    }
                    break;
                    
                case 'api':
                    // Vérification token API
                    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
                    if (!$authHeader || !$this->verifyAPIToken($authHeader)) {
                        http_response_code(401);
                        echo json_encode(['error' => 'Token API invalide']);
                        return false;
                    }
                    break;
            }
        }
        
        return true;
    }
    
    /**
     * Vérification du token API
     */
    private function verifyAPIToken(string $authHeader): bool
    {
        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return false;
        }
        
        try {
            $jwtService = new JWTService($this->app->getConfig());
            $payload = $jwtService->verifyToken($matches[1]);
            return $payload['type'] === 'api';
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Exécution de la route
     */
    private function executeRoute(array $route, Request $request, array $params = [])
    {
        [$controllerName, $actionName] = explode('@', $route['handler']);
        
        $controllerFile = $this->app->getConfig()['app']['paths']['root'] . '/src/Controllers/' . $controllerName . '.php';
        
        if (!file_exists($controllerFile)) {
            throw new Exception("Contrôleur non trouvé: $controllerName");
        }
        
        require_once $controllerFile;
        
        if (!class_exists($controllerName)) {
            throw new Exception("Classe contrôleur non trouvée: $controllerName");
        }
        
        $controller = new $controllerName($this->app);
        
        if (!method_exists($controller, $actionName)) {
            throw new Exception("Action non trouvée: $actionName");
        }
        
        // Ajout des paramètres à la requête
        foreach ($params as $key => $value) {
            $request->setParam($key, $value);
        }
        
        $controller->$actionName($request);
    }
    
    /**
     * Gestion des routes non trouvées
     */
    private function handleNotFound()
    {
        http_response_code(404);
        include $this->app->getConfig()['app']['paths']['templates'] . '/errors/404.php';
    }
}
