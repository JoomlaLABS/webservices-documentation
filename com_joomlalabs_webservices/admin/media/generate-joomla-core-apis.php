<?php
/**
 * Joomla Core APIs OpenAPI Generator
 * 
 * Dynamically generates OpenAPI 3.1.0 YAML specification by scanning:
 * - /api/components/* for available API components
 * - /plugins/webservices/* for active web services plugins
 * - Controllers for available filters and parameters
 * - Views for response field schemas
 * 
 * Usage:
 *   CLI: php generate-joomla-core-apis.php [--all]
 *   Web: generate-joomla-core-apis.php?showAll=true
 * 
 * @package     Joomla.Administrator
 * @subpackage  com_joomlalabs_webservices
 * @copyright   (C) 2025 Joomla!LABS. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

// Detect execution context (CLI or Web)
$isWeb = php_sapi_name() !== 'cli' && php_sapi_name() !== 'phpdbg';

// Bootstrap Joomla if running from web
if ($isWeb) {
    // This file is in media/com_joomlalabs_webservices/generate-joomla-core-apis.php
    // Go up 2 levels to reach Joomla root: media/ -> root/
    define('_JEXEC', 1);
    define('JPATH_BASE', dirname(__DIR__, 2));
    
    require_once JPATH_BASE . '/includes/defines.php';
    require_once JPATH_BASE . '/includes/framework.php';
    
    // Set appropriate headers
    header('Content-Type: application/yaml; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    header('X-Content-Type-Options: nosniff');
    
    // Use Joomla constants for paths
    define('JOOMLA_ROOT', JPATH_ROOT);
    
    // Parameter from query string
    $showAll = isset($_GET['showAll']) && $_GET['showAll'] === 'true';
} else {
    // CLI mode
    // Configuration - adjust path relative to script location
    define('JOOMLA_ROOT', dirname(__DIR__, 3) . '/Joomla_6.0.1-Stable-Full_Package');
    
    // Parameter from CLI args
    $showAll = in_array('--all', $argv ?? []);
}

define('API_COMPONENTS_PATH', JOOMLA_ROOT . '/api/components');
define('WEBSERVICES_PLUGINS_PATH', JOOMLA_ROOT . '/plugins/webservices');

/**
 * Main generator class
 */
class JoomlaCoreApisGenerator
{
    private array $components = [];
    private array $plugins = [];
    private array $tags = [];
    private array $paths = [];
    private array $schemas = [];
    
    public function __construct(private bool $includeAll = false)
    {
    }
    
    /**
     * Scan API components directory
     */
    public function scanComponents(): void
    {
        if (!is_dir(API_COMPONENTS_PATH)) {
            throw new RuntimeException('API components path not found: ' . API_COMPONENTS_PATH);
        }
        
        $dirs = glob(API_COMPONENTS_PATH . '/com_*', GLOB_ONLYDIR);
        
        foreach ($dirs as $dir) {
            $componentName = basename($dir);
            $shortName = str_replace('com_', '', $componentName);
            
            // Check for Controller directory
            $controllerPath = $dir . '/src/Controller';
            if (!is_dir($controllerPath)) {
                continue;
            }
            
            $this->components[$shortName] = [
                'name' => $componentName,
                'path' => $dir,
                'controllers' => $this->findControllers($controllerPath),
                'views' => $this->findViews($dir . '/src/View'),
            ];
        }
    }
    
    /**
     * Scan webservices plugins directory
     */
    public function scanPlugins(): void
    {
        if (!is_dir(WEBSERVICES_PLUGINS_PATH)) {
            throw new RuntimeException('Webservices plugins path not found: ' . WEBSERVICES_PLUGINS_PATH);
        }
        
        $dirs = glob(WEBSERVICES_PLUGINS_PATH . '/*', GLOB_ONLYDIR);
        
        foreach ($dirs as $dir) {
            $pluginName = basename($dir);
            $extensionFile = $dir . '/src/Extension/' . ucfirst($pluginName) . '.php';
            
            if (!file_exists($extensionFile)) {
                continue;
            }
            
            $this->plugins[$pluginName] = [
                'name' => $pluginName,
                'path' => $dir,
                'extension_file' => $extensionFile,
                'routes' => $this->parsePluginRoutes($extensionFile),
            ];
        }
    }
    
    /**
     * Find all controllers in a directory
     */
    private function findControllers(string $path): array
    {
        if (!is_dir($path)) {
            return [];
        }
        
        $controllers = [];
        $files = glob($path . '/*Controller.php');
        
        foreach ($files as $file) {
            $controllerName = basename($file, '.php');
            $controllers[$controllerName] = [
                'file' => $file,
                'filters' => $this->parseControllerFilters($file),
            ];
        }
        
        return $controllers;
    }
    
    /**
     * Find all views in a directory
     */
    private function findViews(string $path): array
    {
        if (!is_dir($path)) {
            return [];
        }
        
        $views = [];
        $dirs = glob($path . '/*', GLOB_ONLYDIR);
        
        foreach ($dirs as $dir) {
            $viewName = basename($dir);
            $jsonapiView = $dir . '/JsonapiView.php';
            
            if (file_exists($jsonapiView)) {
                $views[$viewName] = [
                    'file' => $jsonapiView,
                    'fields' => $this->parseViewFields($jsonapiView),
                ];
            }
        }
        
        return $views;
    }
    
    /**
     * Parse plugin routes from Extension class
     */
    private function parsePluginRoutes(string $file): array
    {
        $content = file_get_contents($file);
        $routes = [];
        
        // Find onBeforeApiRoute method
        if (preg_match('/function\s+onBeforeApiRoute\s*\([^)]*\)\s*(?::\s*void)?\s*\{([^}]+)\}/s', $content, $match)) {
            $methodBody = $match[1];
            
            // Find createCRUDRoutes calls
            if (preg_match_all('/\$router->createCRUDRoutes\s*\(\s*[\'"]([^\'"]+)[\'"](?:\s*,\s*[\'"]([^\'"]+)[\'"])?/s', $methodBody, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $m) {
                    $routes[] = [
                        'type' => 'crud',
                        'path' => $m[1],
                        'controller' => $m[2] ?? null,
                    ];
                }
            }
            
            // Find other helper methods
            if (preg_match_all('/\$this->(create\w+Routes)\s*\(/s', $methodBody, $matches)) {
                foreach ($matches[1] as $method) {
                    $routes[] = [
                        'type' => 'custom',
                        'method' => $method,
                    ];
                }
            }
        }
        
        // Parse custom route methods (createFieldsRoutes, createContentHistoryRoutes, etc.)
        // Need to capture multi-line method bodies with nested braces
        if (preg_match_all('/function\s+(create\w+Routes)\s*\([^)]*\)\s*(?::\s*void)?\s*\{([\s\S]*?)\n    \}/s', $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $methodName = $m[1];
                $methodBody = $m[2];
                
                // Find createCRUDRoutes in custom methods
                if (preg_match_all('/\$router->createCRUDRoutes\s*\(\s*[\'"]([^\'"]+)[\'"](?:\s*,\s*[\'"]([^\'"]+)[\'"])?/s', $methodBody, $subMatches, PREG_SET_ORDER)) {
                    foreach ($subMatches as $sm) {
                        $routes[] = [
                            'type' => strtolower(str_replace(['create', 'Routes'], '', $methodName)),
                            'path' => $sm[1],
                            'controller' => $sm[2] ?? null,
                        ];
                    }
                }
            }
        }
        
        // Also parse $router->addRoutes() calls with custom Route objects
        if (preg_match_all('/new\s+Route\s*\(\s*\[[\'"]([A-Z]+)[\'"]\]\s*,\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]([^\'"]*)\.([^\'"]+)[\'"]/s', $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $method = $m[1];
                $path = $m[2];
                $controller = $m[3];
                $action = $m[4];
                
                $routes[] = [
                    'type' => 'custom_route',
                    'method' => $method,
                    'path' => $path,
                    'controller' => $controller,
                    'action' => $action,
                ];
            }
        }
        
        return $routes;
    }
    
    /**
     * Parse controller filters from displayList method
     */
    private function parseControllerFilters(string $file): array
    {
        $content = file_get_contents($file);
        $filters = [];
        
        // Find displayList method - improved regex to capture complete method body
        if (preg_match('/function\s+displayList\s*\([^)]*\)\s*(?::\s*[\w\\|]+)?\s*\{([\s\S]*?)\n\s{4}\}/s', $content, $match)) {
            $methodBody = $match[1];
            
            // Find $this->modelState->set calls for filters
            // Look for both filter.xxx and direct filter assignments
            if (preg_match_all('/\$this->modelState->set\s*\(\s*[\'"]filter\.([^\'"]+)[\'"]/s', $methodBody, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $m) {
                    $filterName = $m[1];
                    // Map internal filter names to API parameter names
                    $apiParamName = $this->mapFilterToApiParam($filterName);
                    $filters[] = [
                        'name' => $apiParamName,
                        'type' => $this->guessFilterType($filterName),
                        'description' => $this->generateFilterDescription($filterName),
                    ];
                }
            }
            
            // Find $this->modelState->set calls for list parameters
            if (preg_match_all('/\$this->modelState->set\s*\(\s*[\'"]list\.([^\'"]+)[\'"]/s', $methodBody, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $m) {
                    $paramName = $m[1];
                    $filters[] = [
                        'name' => "list[$paramName]",
                        'type' => $this->guessFilterType($paramName),
                        'description' => $this->generateFilterDescription($paramName),
                    ];
                }
            }
        }
        
        // Always add common pagination parameters
        $filters[] = [
            'name' => 'page[limit]',
            'type' => 'integer',
            'description' => 'Number of items to return',
            'default' => 20,
        ];
        
        $filters[] = [
            'name' => 'page[offset]',
            'type' => 'integer',
            'description' => 'Pagination offset',
            'default' => 0,
        ];
        
        // Remove duplicates based on filter name
        $uniqueFilters = [];
        $seenNames = [];
        foreach ($filters as $filter) {
            if (!in_array($filter['name'], $seenNames)) {
                $uniqueFilters[] = $filter;
                $seenNames[] = $filter['name'];
            }
        }
        
        return $uniqueFilters;
    }
    
    /**
     * Parse view fields from JsonapiView
     */
    private function parseViewFields(string $file): array
    {
        $content = file_get_contents($file);
        $fields = ['list' => [], 'item' => []];
        
        // Find $fieldsToRenderList
        if (preg_match('/protected\s+(?:static\s+)?(?:array\s+)?\$fieldsToRenderList\s*=\s*\[(.*?)\]/s', $content, $match)) {
            $fieldsList = $match[1];
            preg_match_all('/[\'"]([^\'"]+)[\'"]/', $fieldsList, $matches);
            $fields['list'] = $matches[1];
        }
        
        // Find $fieldsToRenderItem
        if (preg_match('/protected\s+(?:static\s+)?(?:array\s+)?\$fieldsToRenderItem\s*=\s*\[(.*?)\]/s', $content, $match)) {
            $fieldsItem = $match[1];
            preg_match_all('/[\'"]([^\'"]+)[\'"]/', $fieldsItem, $matches);
            $fields['item'] = $matches[1];
        }
        
        return $fields;
    }
    
    /**
     * Map internal filter name to API parameter name
     */
    private function mapFilterToApiParam(string $filterName): string
    {
        // Map common internal filter names to their API equivalents
        $mapping = [
            'author_id' => 'filter[author]',
            'category_id' => 'filter[category]',
            'published' => 'filter[state]',
            'checked_out' => 'filter[checked_out]',
            'tag' => 'filter[tag]',
        ];
        
        if (isset($mapping[$filterName])) {
            return $mapping[$filterName];
        }
        
        // Default: use the filter name as-is
        return "filter[$filterName]";
    }
    
    /**
     * Guess filter parameter type based on name
     */
    private function guessFilterType(string $name): string
    {
        if (in_array($name, ['search', 'title', 'name', 'language', 'ordering', 'direction'])) {
            return 'string';
        }
        
        if (in_array($name, ['state', 'published', 'category', 'catid', 'category_id', 'author_id', 'tag', 'id', 'limit', 'offset', 'checked_out', 'featured', 'author'])) {
            return 'integer';
        }
        
        if (strpos($name, 'date') !== false || strpos($name, 'modified') !== false || strpos($name, '_start') !== false || strpos($name, '_end') !== false) {
            return 'string'; // Date strings in ISO format
        }
        
        return 'string'; // Default
    }
    
    /**
     * Generate filter description
     */
    private function generateFilterDescription(string $name): string
    {
        $descriptions = [
            'search' => 'Search term',
            'state' => 'Publication state (1 = published, 0 = unpublished)',
            'published' => 'Filter by publication status',
            'category' => 'Filter by category ID',
            'catid' => 'Category ID',
            'category_id' => 'Filter by category ID',
            'author' => 'Filter by author ID',
            'author_id' => 'Filter by author ID',
            'tag' => 'Filter by tag ID',
            'language' => 'Language code',
            'ordering' => 'Field to sort by',
            'direction' => 'Sort direction (ASC or DESC)',
            'limit' => 'Number of items to return',
            'offset' => 'Pagination offset',
            'featured' => 'Filter by featured status (1 = featured, 0 = not featured)',
            'checked_out' => 'Filter by checked out status',
            'modified_start' => 'Filter articles modified after this date (ISO 8601 format)',
            'modified_end' => 'Filter articles modified before this date (ISO 8601 format)',
        ];
        
        return $descriptions[$name] ?? "Filter by $name";
    }
    
    /**
     * Build hierarchical tag from path
     * Examples:
     * /contacts -> Contact
     * /contacts/categories -> Contact|Categories
     * /contacts/form/{id} -> Contact|Form
     * /config/application -> Config|Application
     * /config/{component_name} -> Config|Component
     * /menus/site/items -> Menus|Site|Items
     * /templates/styles/site -> Templates|Styles|Site
     * /fields/contacts/contact -> Contact|Fields|Contact
     * /fields/groups/contacts/contact -> Contact|Fields|Groups|Contact
     * /contacts/{id}/contenthistory -> Contact|Content History
     */
    private function buildHierarchicalTag(string $componentName, string $path): string
    {
        $parts = [];
        $parts[] = ucfirst($componentName);
        
        // Remove v1/ prefix and normalize path
        $path = preg_replace('#^/?v1/#', '', $path);
        
        // Remove the component name from beginning if present
        $componentVariants = [
            $componentName,
            $componentName . 's',
            rtrim($componentName, 's'),
        ];
        
        foreach ($componentVariants as $variant) {
            $path = preg_replace('#^/?' . preg_quote($variant, '#') . '(?=/|$)#i', '', $path);
        }
        
        // Handle /fields/ paths specially (multilevel hierarchy)
        if (preg_match('#^/?fields/(.+)$#', $path, $match)) {
            $parts[] = 'Fields';
            $fieldsPath = $match[1];
            
            // Check for /fields/groups/ pattern
            if (preg_match('#^groups/(.+)$#', $fieldsPath, $groupsMatch)) {
                $parts[] = 'Groups';
                $groupsPath = $groupsMatch[1];
                
                // Check if there's a context after component: /fields/groups/component/context
                if (preg_match('#^[^/]+/([^/{]+)#', $groupsPath, $subMatch)) {
                    $context = ucfirst($subMatch[1]);
                    $parts[] = $context;
                }
                // else: just /fields/groups/component - no additional level needed
            } else {
                // Regular field path: /fields/component/context
                if (preg_match('#^[^/]+/([^/{]+)#', $fieldsPath, $subMatch)) {
                    $context = ucfirst($subMatch[1]);
                    $parts[] = $context;
                }
            }
            
            return implode('|', $parts);
        }
        
        // Handle /contenthistory paths
        if (strpos($path, '/contenthistory') !== false) {
            $parts[] = 'Content History';
            return implode('|', $parts);
        }
        
        // Parse remaining path segments to build hierarchy
        $path = ltrim($path, '/');
        if ($path) {
            $segments = explode('/', $path);
            
            foreach ($segments as $segment) {
                // Skip empty segments
                if (!$segment) {
                    continue;
                }
                
                // Skip ID placeholders like {id}, {component_id}, etc.
                if (strpos($segment, '{') !== false) {
                    continue;
                }
                
                // Handle special route parameters like :component_name
                if (strpos($segment, ':') === 0) {
                    $paramName = substr($segment, 1);
                    if ($paramName === 'component_name') {
                        $parts[] = 'Component';
                    }
                    // Skip other colon parameters
                    continue;
                }
                
                // Add segment as part of hierarchy
                $parts[] = ucfirst($segment);
            }
        }
        
        return implode('|', $parts);
    }
    
    /**
     * Build tags array
     */
    public function buildTags(): void
    {
        // Collect all unique tags from paths
        $tagSet = [];
        
        foreach ($this->components as $shortName => $component) {
            // Check if should include this component
            if (!$this->includeAll && !isset($this->plugins[$shortName])) {
                continue;
            }
            
            $plugin = $this->plugins[$shortName] ?? null;
            $routes = $plugin['routes'] ?? [];
            
            foreach ($routes as $route) {
                $path = $route['path'] ?? '';
                $tag = $this->buildHierarchicalTag($shortName, $path);
                
                if (!isset($tagSet[$tag])) {
                    $tagSet[$tag] = $this->generateTagDescription($shortName, $tag);
                }
            }
        }
        
        // Convert to array format
        foreach ($tagSet as $tagName => $description) {
            $this->tags[] = [
                'name' => $tagName,
                'description' => $description,
            ];
        }
    }
    
    /**
     * Generate appropriate description for a hierarchical tag
     */
    private function generateTagDescription(string $componentName, string $tag): string
    {
        $parts = explode('|', $tag);
        $mainComponent = $parts[0];
        
        if (count($parts) === 1) {
            return "Operations related to " . strtolower($componentName);
        }
        
        // Build descriptive text based on hierarchy
        $lastPart = end($parts);
        
        if ($lastPart === 'Content History') {
            return "Content history for " . strtolower($componentName);
        }
        
        if ($lastPart === 'Fields' || (count($parts) >= 2 && $parts[1] === 'Fields')) {
            if (count($parts) === 2) {
                return "Custom fields for " . strtolower($componentName);
            }
            if (count($parts) === 3) {
                return ucfirst($lastPart) . " fields for " . strtolower($componentName);
            }
            if (count($parts) === 4 && $parts[2] === 'Groups') {
                return ucfirst($lastPart) . " field groups for " . strtolower($componentName);
            }
        }
        
        return ucfirst($lastPart) . " for " . strtolower($componentName);
    }
    
    /**
     * Build paths array
     */
    public function buildPaths(): void
    {
        foreach ($this->components as $shortName => $component) {
            // Check if should include this component
            if (!$this->includeAll && !isset($this->plugins[$shortName])) {
                continue;
            }
            
            $plugin = $this->plugins[$shortName] ?? null;
            $routes = $plugin['routes'] ?? [];
            
            // Generate paths from plugin routes
            foreach ($routes as $route) {
                if ($route['type'] === 'crud') {
                    $this->generateCrudPaths($shortName, $route, $component);
                } else {
                    $this->generateCustomPaths($shortName, $route, $component);
                }
            }
        }
    }
    
    /**
     * Generate CRUD paths
     */
    private function generateCrudPaths(string $componentName, array $route, array $component): void
    {
        // Remove 'v1/' prefix if present (it's already in server URL)
        $path = ltrim($route['path'], '/');
        $path = preg_replace('#^v1/#', '', $path);
        
        // Use generic hierarchical tag builder
        $tag = $this->buildHierarchicalTag($componentName, $path);
        
        // Extract resource name from path (e.g., /content/articles -> articles)
        $resourceName = basename($path);
        
        // Find controller - need to normalize the controller name
        $controllerBase = $route['controller'] ?? $resourceName;
        // Capitalize and add Controller suffix if not present
        $controllerName = ucfirst($controllerBase);
        if (!str_ends_with($controllerName, 'Controller')) {
            $controllerName .= 'Controller';
        }
        $controller = $component['controllers'][$controllerName] ?? null;
        
        // Find view
        $viewName = ucfirst($resourceName);
        $view = $component['views'][$viewName] ?? null;
        
        // GET list
        $this->paths[$path]['get'] = [
            'summary' => "Get list of $resourceName",
            'tags' => [$tag],
            'security' => [['BearerAuth' => []]],
            'parameters' => $this->formatParameters($controller['filters'] ?? []),
            'responses' => $this->generateListResponse($view),
        ];
        
        // POST create
        $this->paths[$path]['post'] = [
            'summary' => "Create new item in $resourceName",
            'tags' => [$tag],
            'security' => [['BearerAuth' => []]],
            'requestBody' => $this->generateRequestBody($view),
            'responses' => $this->generateCreateResponse(),
        ];
        
        // GET/PATCH/DELETE single item
        $itemPath = "$path/{id}";
        
        $this->paths[$itemPath]['get'] = [
            'summary' => "Get single item from $resourceName",
            'tags' => [$tag],
            'security' => [['BearerAuth' => []]],
            'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
            'responses' => $this->generateItemResponse($view),
        ];
        
        $this->paths[$itemPath]['patch'] = [
            'summary' => "Update item in $resourceName",
            'tags' => [$tag],
            'security' => [['BearerAuth' => []]],
            'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
            'requestBody' => $this->generateRequestBody($view),
            'responses' => $this->generateUpdateResponse(),
        ];
        
        $this->paths[$itemPath]['delete'] = [
            'summary' => "Delete item from $resourceName",
            'tags' => [$tag],
            'security' => [['BearerAuth' => []]],
            'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
            'responses' => $this->generateDeleteResponse(),
        ];
    }
    
    /**
     * Generate custom paths (fields, contenthistory, custom routes, etc.)
     */
    private function generateCustomPaths(string $componentName, array $route, array $component): void
    {
        // Skip routes without necessary information
        if ($route['type'] === 'custom' && !isset($route['method'])) {
            // This is a placeholder for unimplemented custom helper methods
            return;
        }
        
        if ($route['type'] === 'fields' || ($route['type'] === 'crud' && isset($route['path']))) {
            // Fields routes and CRUD routes use standard CRUD operations
            $this->generateCrudPaths($componentName, $route, $component);
            return;
        }
        
        if ($route['type'] === 'contenthistory') {
            // Content history routes are custom
            $this->generateContentHistoryPaths($componentName, $route, $component);
            return;
        }
        
        if ($route['type'] === 'custom_route') {
            // Custom Route objects from $router->addRoutes()
            $this->generateCustomRoutePath($componentName, $route, $component);
            return;
        }
        
        // Unknown custom type - skip it
    }
    
    /**
     * Generate content history paths
     */
    private function generateContentHistoryPaths(string $componentName, array $route, array $component): void
    {
        // Remove 'v1/' prefix if present
        $path = ltrim($route['path'], '/');
        $path = preg_replace('#^v1/#', '', $path);
        
        // Use generic hierarchical tag builder
        $tag = $this->buildHierarchicalTag($componentName, $path);
        
        // Content history has specific operations
        $this->paths[$path]['get'] = [
            'summary' => 'Get content history',
            'tags' => [$tag],
            'security' => [['BearerAuth' => []]],
            'parameters' => [],
            'responses' => $this->generateListResponse(null),
        ];
    }
    
    /**
     * Generate path from custom Route object
     */
    private function generateCustomRoutePath(string $componentName, array $route, array $component): void
    {
        // Remove 'v1/' prefix if present
        $path = ltrim($route['path'], '/');
        $path = preg_replace('#^v1/#', '', $path);
        
        // Use generic hierarchical tag builder
        $tag = $this->buildHierarchicalTag($componentName, $path);
        
        $method = strtolower($route['method']);
        $action = $route['action'];
        
        // Replace :id and other params with {id}
        $openApiPath = preg_replace('/:(\w+)/', '{$1}', $path);
        
        // Find controller
        $controllerName = ucfirst($route['controller']) . 'Controller';
        $controller = $component['controllers'][$controllerName] ?? null;
        
        // Determine summary based on action
        $summary = ucfirst(str_replace('_', ' ', $action));
        
        // Build parameters array
        $parameters = [];
        if (preg_match_all('/:(\w+)/', $path, $matches)) {
            foreach ($matches[1] as $paramName) {
                $parameters[] = [
                    'name' => $paramName,
                    'in' => 'path',
                    'required' => true,
                    'schema' => ['type' => $paramName === 'id' ? 'integer' : 'string'],
                ];
            }
        }
        
        // Add query parameters for GET displayList
        if ($method === 'get' && $action === 'displayList' && $controller) {
            $parameters = array_merge($parameters, $this->formatParameters($controller['filters'] ?? []));
        }
        
        // Build operation spec
        $operation = [
            'summary' => $summary,
            'tags' => [$tag],
            'security' => [['BearerAuth' => []]],
            'parameters' => $parameters,
        ];
        
        // Add request body for POST/PATCH
        if (in_array($method, ['post', 'patch'])) {
            $operation['requestBody'] = $this->generateRequestBody(null);
        }
        
        // Add responses
        if ($method === 'get' && $action === 'displayList') {
            $operation['responses'] = $this->generateListResponse(null);
        } elseif ($method === 'get' && $action === 'displayItem') {
            $operation['responses'] = $this->generateItemResponse(null);
        } elseif ($method === 'post') {
            $operation['responses'] = $this->generateCreateResponse();
        } elseif ($method === 'patch') {
            $operation['responses'] = $this->generateUpdateResponse();
        } elseif ($method === 'delete') {
            $operation['responses'] = $this->generateDeleteResponse();
        } else {
            $operation['responses'] = [
                '200' => ['description' => 'Successful response'],
                '401' => ['description' => 'Unauthorized'],
                '500' => ['description' => 'Server error'],
            ];
        }
        
        $this->paths[$openApiPath][$method] = $operation;
    }
    
    /**
     * Format parameters for OpenAPI
     */
    private function formatParameters(array $filters): array
    {
        $params = [];
        
        foreach ($filters as $filter) {
            $param = [
                'name' => $filter['name'],
                'in' => 'query',
                'description' => $filter['description'],
                'schema' => ['type' => $filter['type']],
            ];
            
            if (isset($filter['default'])) {
                $param['schema']['default'] = $filter['default'];
            }
            
            if ($filter['name'] === 'list[direction]') {
                $param['schema']['enum'] = ['ASC', 'DESC'];
            }
            
            $params[] = $param;
        }
        
        return $params;
    }
    
    /**
     * Generate list response
     */
    private function generateListResponse(?array $view): array
    {
        return [
            '200' => [
                'description' => 'Successful response',
                'content' => [
                    'application/vnd.api+json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'data' => [
                                    'type' => 'array',
                                    'items' => ['type' => 'object'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '401' => ['description' => 'Unauthorized'],
            '500' => ['description' => 'Server error'],
        ];
    }
    
    /**
     * Generate item response
     */
    private function generateItemResponse(?array $view): array
    {
        return [
            '200' => [
                'description' => 'Successful response',
                'content' => [
                    'application/vnd.api+json' => [
                        'schema' => ['type' => 'object'],
                    ],
                ],
            ],
            '401' => ['description' => 'Unauthorized'],
            '404' => ['description' => 'Not found'],
            '500' => ['description' => 'Server error'],
        ];
    }
    
    /**
     * Generate request body
     */
    private function generateRequestBody(?array $view): array
    {
        return [
            'required' => true,
            'content' => [
                'application/vnd.api+json' => [
                    'schema' => ['type' => 'object'],
                ],
            ],
        ];
    }
    
    /**
     * Generate create response
     */
    private function generateCreateResponse(): array
    {
        return [
            '201' => ['description' => 'Created successfully'],
            '400' => ['description' => 'Bad request'],
            '401' => ['description' => 'Unauthorized'],
            '422' => ['description' => 'Validation error'],
            '500' => ['description' => 'Server error'],
        ];
    }
    
    /**
     * Generate update response
     */
    private function generateUpdateResponse(): array
    {
        return [
            '200' => ['description' => 'Updated successfully'],
            '400' => ['description' => 'Bad request'],
            '401' => ['description' => 'Unauthorized'],
            '404' => ['description' => 'Not found'],
            '422' => ['description' => 'Validation error'],
            '500' => ['description' => 'Server error'],
        ];
    }
    
    /**
     * Generate delete response
     */
    private function generateDeleteResponse(): array
    {
        return [
            '204' => ['description' => 'Deleted successfully'],
            '401' => ['description' => 'Unauthorized'],
            '404' => ['description' => 'Not found'],
            '500' => ['description' => 'Server error'],
        ];
    }
    
    /**
     * Build schemas
     */
    public function buildSchemas(): void
    {
        // TODO: Generate schemas from view fields
        // Placeholder for now
        $this->schemas['Error'] = [
            'type' => 'object',
            'properties' => [
                'errors' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'status' => ['type' => 'string'],
                            'title' => ['type' => 'string'],
                            'detail' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ];
    }
    
    /**
     * Generate complete YAML output
     */
    public function generateYaml(): string
    {
        // Determine title and description based on includeAll flag
        $scope = $this->includeAll ? 'all available components' : 'active webservices plugins';
        $title = $this->includeAll 
            ? 'Joomla Core APIs - All Components' 
            : 'Joomla Core APIs - Active Plugins';
        $summary = "Dynamically generated API documentation for $scope";
        $description = "REST API endpoints automatically extracted from this Joomla installation. Shows $scope and their available operations.";
        
        // Try to get Joomla version
        $version = 'unknown';
        if (defined('JVERSION')) {
            $version = 'Joomla! '.JVERSION;
        } elseif (file_exists(JOOMLA_ROOT . '/libraries/src/Version.php')) {
            $versionFile = file_get_contents(JOOMLA_ROOT . '/libraries/src/Version.php');
            if (preg_match('/const\s+RELEASE\s*=\s*[\'"]([^\'\"]+)[\'"]/', $versionFile, $match)) {
                $version = 'Joomla! '.$match[1];
            }
        }
        
        $yaml = <<<YAML
openapi: 3.1.0
info:
  title: $title
  summary: $summary
  description: $description
  version: $version
  license:
    name: GNU General Public License v2 or later
    identifier: GPL-2.0-or-later
    #url: https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html

servers:
  - url: '/api/index.php/v1'
    description: Current Joomla site

YAML;
        
        // Add tags
        $yaml .= "\ntags:\n";
        foreach ($this->tags as $tag) {
            $yaml .= "  - name: {$tag['name']}\n";
            $yaml .= "    description: {$tag['description']}\n";
        }
        
        // Add paths
        $yaml .= "\npaths:\n";
        foreach ($this->paths as $path => $methods) {
            // Ensure path starts with /
            $cleanPath = '/' . ltrim($path, '/');
            $yaml .= "  $cleanPath:\n";
            foreach ($methods as $method => $spec) {
                $yaml .= $this->formatMethodSpec($method, $spec, 4);
            }
        }
        
        // Add components
        $yaml .= "\ncomponents:\n";
        $yaml .= "  securitySchemes:\n";
        $yaml .= "    BearerAuth:\n";
        $yaml .= "      type: http\n";
        $yaml .= "      scheme: bearer\n";
        $yaml .= "      bearerFormat: JWT\n";
        $yaml .= "      description: JWT Bearer token for API authentication\n";
        
        $yaml .= "\n  schemas:\n";
        foreach ($this->schemas as $name => $schema) {
            $yaml .= "    $name:\n";
            $yaml .= $this->formatSchema($schema, 3);
        }
        
        return $yaml;
    }
    
    /**
     * Format method specification
     */
    private function formatMethodSpec(string $method, array $spec, int $indent): string
    {
        $spaces = str_repeat(' ', $indent);
        $yaml = $spaces . "$method:\n";
        $indent += 2;
        
        if (isset($spec['summary'])) {
            $yaml .= str_repeat(' ', $indent) . "summary: {$spec['summary']}\n";
        }
        
        if (isset($spec['tags'])) {
            $yaml .= str_repeat(' ', $indent) . "tags:\n";
            foreach ($spec['tags'] as $tag) {
                $yaml .= str_repeat(' ', $indent + 2) . "- $tag\n";
            }
        }
        
        if (isset($spec['security'])) {
            $yaml .= str_repeat(' ', $indent) . "security:\n";
            $yaml .= str_repeat(' ', $indent + 2) . "- BearerAuth: []\n";
        }
        
        if (isset($spec['parameters'])) {
            if (empty($spec['parameters'])) {
                // Empty array: write as []
                $yaml .= str_repeat(' ', $indent) . "parameters: []\n";
            } else {
                // Non-empty array: write as list
                $yaml .= str_repeat(' ', $indent) . "parameters:\n";
                foreach ($spec['parameters'] as $param) {
                    $yaml .= $this->formatParameter($param, $indent + 2);
                }
            }
        }
        
        if (isset($spec['requestBody'])) {
            $yaml .= str_repeat(' ', $indent) . "requestBody:\n";
            $yaml .= $this->formatRequestBody($spec['requestBody'], $indent + 2);
        }
        
        if (isset($spec['responses'])) {
            $yaml .= str_repeat(' ', $indent) . "responses:\n";
            foreach ($spec['responses'] as $code => $response) {
                $yaml .= str_repeat(' ', $indent + 2) . "'$code':\n";
                $yaml .= str_repeat(' ', $indent + 4) . "description: {$response['description']}\n";
                
                if (isset($response['content'])) {
                    $yaml .= str_repeat(' ', $indent + 4) . "content:\n";
                    $yaml .= str_repeat(' ', $indent + 6) . "application/vnd.api+json:\n";
                    $yaml .= str_repeat(' ', $indent + 8) . "schema:\n";
                    $yaml .= $this->formatSchema($response['content']['application/vnd.api+json']['schema'], ($indent + 10) / 2);
                }
            }
        }
        
        return $yaml;
    }
    
    /**
     * Format parameter
     */
    private function formatParameter(array $param, int $indent): string
    {
        $yaml = str_repeat(' ', $indent) . "- name: {$param['name']}\n";
        $yaml .= str_repeat(' ', $indent + 2) . "in: {$param['in']}\n";
        
        if (isset($param['required']) && $param['required']) {
            $yaml .= str_repeat(' ', $indent + 2) . "required: true\n";
        }
        
        if (isset($param['description'])) {
            $yaml .= str_repeat(' ', $indent + 2) . "description: {$param['description']}\n";
        }
        
        $yaml .= str_repeat(' ', $indent + 2) . "schema:\n";
        $yaml .= str_repeat(' ', $indent + 4) . "type: {$param['schema']['type']}\n";
        
        if (isset($param['schema']['default'])) {
            $yaml .= str_repeat(' ', $indent + 4) . "default: {$param['schema']['default']}\n";
        }
        
        if (isset($param['schema']['enum'])) {
            $yaml .= str_repeat(' ', $indent + 4) . "enum:\n";
            foreach ($param['schema']['enum'] as $value) {
                $yaml .= str_repeat(' ', $indent + 6) . "- $value\n";
            }
        }
        
        return $yaml;
    }
    
    /**
     * Format request body
     */
    private function formatRequestBody(array $body, int $indent): string
    {
        $yaml = str_repeat(' ', $indent) . "required: true\n";
        $yaml .= str_repeat(' ', $indent) . "content:\n";
        $yaml .= str_repeat(' ', $indent + 2) . "application/vnd.api+json:\n";
        $yaml .= str_repeat(' ', $indent + 4) . "schema:\n";
        $yaml .= $this->formatSchema($body['content']['application/vnd.api+json']['schema'], ($indent + 6) / 2);
        
        return $yaml;
    }
    
    /**
     * Format schema
     */
    private function formatSchema(array $schema, int $indent): string
    {
        $yaml = '';
        $spaces = str_repeat(' ', $indent * 2);
        
        if (isset($schema['type'])) {
            $yaml .= $spaces . "type: {$schema['type']}\n";
        }
        
        if (isset($schema['properties'])) {
            $yaml .= $spaces . "properties:\n";
            foreach ($schema['properties'] as $name => $prop) {
                $yaml .= $spaces . "  $name:\n";
                $yaml .= $this->formatSchema($prop, $indent + 2);
            }
        }
        
        if (isset($schema['items'])) {
            $yaml .= $spaces . "items:\n";
            $yaml .= $this->formatSchema($schema['items'], $indent + 1);
        }
        
        return $yaml;
    }
    
    /**
     * Run the generator
     */
    public function run(bool $isWeb = false): void
    {
        // Scan components and plugins
        $this->scanComponents();
        $this->scanPlugins();
        
        // Build OpenAPI structure
        $this->buildTags();
        $this->buildPaths();
        $this->buildSchemas();
        
        // Generate YAML
        $yaml = $this->generateYaml();
        
        if ($isWeb) {
            // Web request: output YAML directly
            echo $yaml;
        } else {
            // CLI: Write to file with UTF-8 encoding (no BOM)
            $outputFile = __DIR__ . '/joomla-core-apis-generated.yaml';
            file_put_contents($outputFile, $yaml, LOCK_EX);
            
            echo "OpenAPI specification generated successfully!\n";
            echo "Output file: $outputFile\n";
            echo "Total components: " . count($this->components) . "\n";
            echo "Active plugins: " . count($this->plugins) . "\n";
        }
    }
}

// Run generator
try {
    $generator = new JoomlaCoreApisGenerator($showAll);
    $generator->run($isWeb);
} catch (Exception $e) {
    if ($isWeb) {
        header('HTTP/1.1 500 Internal Server Error');
        echo "# Error generating OpenAPI specification\n";
        echo "# " . $e->getMessage() . "\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
    exit(1);
}
