<?php
declare(strict_types = 1);

namespace Controllers;

use BadRequest;
use Controller;
use Enums\Action;
use Enums\Type;
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
     * Default type value
     *
     * @var Type $type
     */
    private Type $type = Type::User;

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

        // Display users list
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
                    order: "first_name"
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
        $this->checkExist("user_id", $args, "users", true);

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

    /**
     * Create new user
     * Usage: POST /users | Scope: app, super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Unauthorized if user don't have the permission
     */
    public function addUser(Request $request, Response $response): Response
    {
        // Check scope before accessing function
        $this->checkScope(["app"]);

        // Check if values exist in request
        $this->checkExist("email", $GLOBALS["body"], strict: true);
        $this->checkExist("first_name", $GLOBALS["body"], strict: true);
        $this->checkExist("last_name", $GLOBALS["body"], strict: true);

        // Create new user
        $user_id = ($this->database()->create(
            "users",
            [
                "email" => $GLOBALS["body"]["email"],
                "first_name" => $GLOBALS["body"]["first_name"],
                "last_name" => $GLOBALS["body"]["last_name"],
                "device" => $GLOBALS["body"]["scope"] ?? '',
            ],
            "user_id"
        ))["user_id"];

        // Add log
        $this->addLog(Action::Add, $user_id, $this->type);

        // Display success code
        return $this->successCode()->created();
    }

    /**
     * Edit information of a user
     * Usage: PUT /users/{user_id} | Scope: super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Unauthorized if user don't have the permission
     */
    public function editUser(Request $request, Response $response, array $args): Response
    {
        // Check scope before accessing function
        $this->checkScope();

        // Check if user exist
        $this->checkExist("user_id", $args, "users", true);

        // Edit user information
        $this->database()->update(
            "users",
            [
                "email" => $GLOBALS["body"]["email"] ?? '',
                "first_name" => $GLOBALS["body"]["first_name"] ?? '',
                "last_name" => $GLOBALS["body"]["last_name"] ?? '',
                "device" => $GLOBALS["body"]["device"] ?? ''
            ],
            ["user_id" => $args["user_id"]]
        );

        // Add log
        $this->addLog(Action::Edit, $args["user_id"], $this->type);

        // Display success code
        return $this->successCode()->success();
    }

    /**
     * Delete existing user
     * Usage: DELETE /users/{user_id} | Scope: super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Unauthorized if user don't have the permission
     */
    public function deleteUser(Request $request, Response $response, array $args): Response
    {
        // Check scope before accessing function
        $this->checkScope();

        // Check if user exist
        $this->checkExist("user_id", $args, "users", true);

        // Remove user
        $this->database()->delete(
            "users",
            ["user_id" => $args["user_id"]]
        );

        // Display success code
        return $this->successCode()->success();
    }
}