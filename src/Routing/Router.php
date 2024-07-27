<?php

namespace LunarisForge\Routing;

use LunarisForge\Http\Enums\HttpStatus;
use LunarisForge\Http\Request;
use LunarisForge\Http\Response;
use LunarisForge\Routing\Enums\RequestMethod;
use LunarisForge\Pipelines\Pipeline;

/**
 * Class Router
 * @package LunarisForge\Routing
 */
class Router
{
    /**
     * Hold all active routes
     *
     * @var array<string, array<string, mixed>>
     */
    protected array $routes = [];

    /**
     * Pipelines stack
     *
     * @var array<callable>
     */
    protected array $stages = [];

    /**
     * Add a single route
     *
     * @param  string  $method
     * @param  string  $uri
     * @param  mixed  $action
     * @return void
     */
    protected function add(string $method, string $uri, mixed $action): void
    {
        if (!isset($this->routes[$method])) {
            $this->routes[$method] = [];
        }

        $this->routes[$method][$uri] = $action;
    }

    /**
     * Define a GET route
     *
     * @param  string  $uri
     * @param  mixed  $action
     * @return void
     */
    public function get(string $uri, mixed $action): void
    {
        $this->add(RequestMethod::GET->value, $uri, $action);
    }

    /**
     * Define a POST route
     *
     * @param  string  $uri
     * @param  mixed  $action
     * @return void
     */
    public function post(string $uri, mixed $action): void
    {
        $this->add(RequestMethod::POST->value, $uri, $action);
    }

    /**
     * Define a PATCH route
     *
     * @param  string  $uri
     * @param  mixed  $action
     * @return void
     */
    public function patch(string $uri, mixed $action): void
    {
        $this->add(RequestMethod::PATCH->value, $uri, $action);
    }

    /**
     * Define a DELETE route
     *
     * @param  string  $uri
     * @param  mixed  $action
     * @return void
     */
    public function delete(string $uri, mixed $action): void
    {
        $this->add(RequestMethod::DELETE->value, $uri, $action);
    }

    /**
     * Register stages to be applied to all requests
     *
     * @param  array<callable>  $stages
     * @return void
     */
    public function stages(array $stages): void
    {
        $this->stages = $stages;
    }

    /**
     * Dispatch the current request to the appropriate route
     *
     * @param  Request  $request
     * @return Response
     */
    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $uri = $request->getPath();

        $handler = function (Request $request) use ($method, $uri) {
            if (isset($this->routes[$method])) {
                // Match routes with dynamic parameters
                foreach ($this->routes[$method] as $routeUri => $action) {
                    $pattern = preg_replace('/\{[^\}]+\}/', '([^/]+)', $routeUri);

                    /** @phpstan-ignore-next-line */
                    if (preg_match("#^$pattern$#", $uri, $matches)) {
                        array_shift($matches);
                        /** @phpstan-ignore-next-line */
                        return call_user_func_array($action, array_merge([$request], $matches));
                    }
                }
            }

            return new Response('Not Found', HttpStatus::NOT_FOUND);
        };

        // Create the pipeline instance
        $pipeline = new Pipeline($this->stages);

        // Pass the request through the pipeline
        return $pipeline->handle($request, $handler);
    }
}
