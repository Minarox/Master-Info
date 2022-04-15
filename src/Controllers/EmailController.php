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
class EmailController extends Controller
{
    /**
     * Return array of emails
     * Usage: GET /emails | Scope: admin, super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Unauthorized if user don't have the permission
     */
    public function getEmails(Request $request, Response $response): Response
    {
        // Check scope before accessing function
        $this->checkScope(["admin"]);

        // Display emails list
        $response->getBody()->write(
            json_encode(
                $this->database()->find(
                    "emails",
                    [
                        "email_id",
                        "title",
                        "description"
                    ],
                    ['*'],
                    false,
                    "email_id"
                )
            )
        );
        return $response;
    }
}