<?php
declare(strict_types=1);

namespace Controllers;

use Controller;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * Controller for Actions table
 */
class HelloController extends Controller
{
    /**
     * Return "Hello World".
     *
     * Usage: GET /
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     */
    public function helloWorld(Request $request, Response $response): Response
    {
        // Display "Hello World"
        $response->getBody()->write(json_encode("Hello world!"));
        return $response->withStatus(200);
    }
}