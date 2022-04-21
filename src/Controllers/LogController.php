<?php
declare(strict_types = 1);

namespace Controllers;

use BadRequest;
use Controller;
use NotFound;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Unauthorized;

/**
 * Controller for emails
 */
class LogController extends Controller
{
    /**
     * Return array of logs
     * Usage: GET /logs | Scope: super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Unauthorized if user don't have the permission
     */
    public function getLogs(Request $request, Response $response): Response
    {
        // Check scope before accessing function
        $this->checkScope([]);

        // Display emails list
        $response->getBody()->write(
            json_encode(
                $this->database()->find(
                    "logs",
                    [
                        "source",
                        "source_id",
                        "source_type",
                        "action",
                        "target",
                        "target_id",
                        "target_type",
                        "created_at"
                    ],
                    ['*'],
                    false,
                    "created_at DESC"
                )
            )
        );
        return $response;
    }
}