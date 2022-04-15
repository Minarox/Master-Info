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
 * Controller for users
 */
class UserController extends Controller
{
    /**
     * Return array of users
     * Usage: GET /users | Scope: admin, super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Unauthorized if user don't have the permission
     */
    public function getUsers(Request $request, Response $response): Response
    {
        // Check scope before accessing function
        $this->checkScope(["admin"]);

        // Display admins list
        $response->getBody()->write(
            json_encode(
                $this->database()->find(
                    "users",
                    [
                        "user_id",
                        "email",
                        "first_name",
                        "last_name"
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
     * Return information of a user
     * Usage: GET /users/{user_id} | Scope: admin, super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Unauthorized if user don't have the permission
     */
    public function getUser(Request $request, Response $response, array $args): Response
    {
        // Check scope before accessing function
        $this->checkScope(["admin"]);

        // Check if user exist
        $this->checkExist("user_id", $args, "users", true, "user_id");

        // Fetch and display user information
        $response->getBody()->write(
            json_encode(
                $this->database()->find(
                    "users",
                    [
                        "email",
                        "first_name",
                        "last_name",
                        "device",
                        "created_at",
                        "updated_at"
                    ],
                    ["user_id" => $args["user_id"]],
                    true
                )
            )
        );
        return $response;
    }
}