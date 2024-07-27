<?php

namespace Tests\Routing;

use LunarisForge\Http\Enums\HttpStatus;
use LunarisForge\Http\Request;
use LunarisForge\Http\Response;
use LunarisForge\Routing\Router;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    /**
     * @return void
     * @throws Exception
     */
    public function testGetRoute()
    {
        $router = new Router();
        $router->get('/test', function ($request) {
            return new Response('Test GET', HttpStatus::OK);
        });

        $request = $this->createMock(Request::class);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getPath')->willReturn('/test');

        $response = $router->dispatch($request);

        $this->assertEquals(HttpStatus::OK->value, $response->getStatusCode());
        $this->assertEquals('Test GET', $response->getContents());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testPostRoute()
    {
        $router = new Router();
        $router->post('/test', function ($request) {
            return new Response('Test POST', HttpStatus::OK);
        });

        $request = $this->createMock(Request::class);
        $request->method('getMethod')->willReturn('POST');
        $request->method('getPath')->willReturn('/test');

        $response = $router->dispatch($request);

        $this->assertEquals(HttpStatus::OK->value, $response->getStatusCode());
        $this->assertEquals('Test POST', $response->getContents());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testRouteNotFound()
    {
        $router = new Router();

        $request = $this->createMock(Request::class);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getPath')->willReturn('/nonexistent');

        $response = $router->dispatch($request);

        $this->assertEquals(HttpStatus::NOT_FOUND->value, $response->getStatusCode());
        $this->assertEquals('Not Found', $response->getContents());
    }
}
