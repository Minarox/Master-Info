<?php
declare(strict_types=1);

namespace Controllers;

use Controller;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * Controller for Actions table
 */
class BaseController extends Controller
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
    public function basePath(Request $request, Response $response): Response
    {
        // Display description
        $response->getBody()->write(
            json_encode([
                "Version"     => "v1.0",
                "Title"       => "MSPR",
                "Description" => "API de gestion du back-office du projet MSPR 2022.",
                "Host"        => "https://mspr.minarox.fr/",
                "BasePath"    => "api/"
            ]));
        return $response->withStatus(200);
    }
}