<?php
declare(strict_types = 1);

namespace Controllers;

use BadRequest;
use Controller;
use Forbidden;
use NotFound;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * Controller for admins
 */
class AdminController extends Controller
{
    /**
     * Return array of admins
     * Usage: GET /admins | Scope: super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Forbidden if user don't have the permission
     */
    public function getAdmins(Request $request, Response $response): Response
    {
        // Check scope before accessing function
        $this->checkScope();

        // Display admins list
        $response->getBody()->write(
            json_encode(
                $this->database()->find(
                    "admins",
                    [
                        "admin_id",
                        "first_name",
                        "last_name",
                        "scope",
                        "active"
                    ],
                    ['*'],
                    false,
                    "first_name"
                )
            )
        );
        return $response;
    }

    /**
     * Return information of an admin
     * Usage: GET /admins/{admin_id} | Scope: super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Forbidden if user don't have the permission
     */
    public function getAdmin(Request $request, Response $response, array $args): Response
    {
        // Check scope before accessing function
        $this->checkScope();

        // Fetch admin information
        $data = $this->database()->find(
            "admins",
            [
                "email",
                "first_name",
                "last_name",
                "scope",
                "active",
                "created_at",
                "updated_at"
            ],
            ["admin_id" => $args["admin_id"]],
            true
        );

        // Add last connexion information
        $data["last_connexion"] = $this->database()->find(
            "tokens",
            ["expires - INTERVAL 1 HOUR as expires"],
            ["user_id" => $args["admin_id"]],
            true,
            "expires desc",
            false
        ) ?: null;

        // Display admin information
        $response->getBody()->write(
            json_encode($data)
        );
        return $response;
    }
}