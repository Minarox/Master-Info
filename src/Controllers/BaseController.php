<?php
declare(strict_types=1);

namespace Controllers;

use Controller;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * Controller for base path
 */
class BaseController extends Controller
{
    /**
     * Return API description.
     * Usage: GET / | Scope: none
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     */
    public function basePath(Request $request, Response $response): Response
    {
        // Display description
        $response->getBody()->write(
            json_encode([
                "version"     => "v1.1.4",
                "title"       => "Cerealis API",
                "description" => "Enterprise Resource Management API",
                "host"        => "https://mspr.minarox.fr",
                "base_path"   => "/api"
            ]));
        return $response->withStatus(200);
    }
}