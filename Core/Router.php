<?php

namespace Core;


use Core\Middleware\Middleware;
use Exception;
use JetBrains\PhpStorm\NoReturn;


/**
 * Class Router
 *
 * This class provides a mechanism to resolve and handle routes based on a given URI and method.
 */
class Router
{
	/**
	 * @var array $routes The array of routes.
	 */
	public array $routes = [];
	
	/**
	 * Adds a route to the routes array.
	 *
	 * @param string $method     The HTTP method.
	 * @param string $uri        The URI of the route.
	 * @param string $controller The controller to handle the route.
	 *
	 * @return void
	 */
	private function add(string $method, string $uri, string $controller): void
	{
		$this->routes[$method][$uri] = $controller;
	}
	
	/**
	 * Adds a GET route.
	 *
	 * @param string $uri        The URI of the route.
	 * @param string $controller The controller to handle the route.
	 *
	 * @return void
	 */
	public function get(string $uri, string $controller): void
	{
		$this->add('GET', $uri, $controller);
	}
	
	/**
	 * Adds a POST route.
	 *
	 * @param string $uri        The URI of the route.
	 * @param string $controller The controller to handle the route.
	 *
	 * @return void
	 */
	public function post(string $uri, string $controller): void
	{
		$this->add('POST', $uri, $controller);
	}
	
	/**
	 * Adds a PUT route.
	 *
	 * @param string $uri        The URI of the route.
	 * @param string $controller The controller to handle the route.
	 *
	 * @return void
	 */
	public function put(string $uri, string $controller): void
	{
		$this->add('PUT', $uri, $controller);
	}
	
	/**
	 * Adds a PATCH route.
	 *
	 * @param string $uri        The URI of the route.
	 * @param string $controller The controller to handle the route.
	 *
	 * @return void
	 */
	public function patch(string $uri, string $controller): void
	{
		$this->add('PATCH', $uri, $controller);
	}
	
	/**
	 * Adds a DELETE route.
	 *
	 * @param string $uri        The URI of the route.
	 * @param string $controller The controller to handle the route.
	 *
	 * @return void
	 */
	public function delete(string $uri, string $controller): void
	{
		$this->add('DELETE', $uri, $controller);
	}
	
	/**
	 * Routes the request to the appropriate controller.
	 *
	 * @param string $uri    The URI of the request.
	 * @param string $method The HTTP method of the request.
	 *
	 * @return void
	 * @throws Exception If the route is not found.
	 */
	#[NoReturn] public function route(string $uri, string $method): void
	{
		// Find the route
		foreach ($this->routes as $route) {
			if ($route['uri'] === $uri && $route['method'] === strtoupper($method)) {
				// Check if the route has middleware
				Middleware::resolve($route['middleware']);
				
				// Call the controller
				require_once BASE_PATH . "Http/controller/{$route['controller']}.php";
				exit;
			}
		}
		
		// If no route is found, abort with 404
		$this->abort();
	}
	
	/**
	 * Gets the previous URL.
	 *
	 * @return string The previous URL.
	 */
	public function previousUrl(): string
	{
		return $_SERVER['HTTP_REFERER'] ?? '/';
	}
	
	/**
	 * Aborts the request with a given HTTP status code.
	 *
	 * @param int $code The HTTP status code.
	 *
	 * @return void
	 */
	#[NoReturn] protected function abort(int $code = 404): void
	{
		http_response_code($code);
		
		require_once BASE_PATH . "resources/views/$code.view.php";
		exit;
	}
	
	/**
	 * Adds a middleware to the last route.
	 *
	 * @param string $key The key of the middleware.
	 *
	 * @return Router
	 */
	public function only(string $key): Router
	{
		$this->routes[array_key_last($this->routes)]['middleware'] = $key;
		
		return $this;
	}
}