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

        // Fetch query params
        $params = $request->getQueryParams();

        // Fields
        $fields = array_filter([
            "email LIKE" => array_key_exists("email", $params) ? '%' . $params["email"] . '%' : '',
            "first_name LIKE" => array_key_exists("first_name", $params) ? '%' . $params["first_name"] . '%' : '',
            "last_name LIKE" => array_key_exists("last_name", $params) ? '%' . $params["last_name"] . '%' : '',
            "device LIKE" => array_key_exists("device", $params) ? '%' . $params["device"] . '%' : '',
            "nb_share LIKE" => array_key_exists("nb_share", $params) ? '%' . $params["nb_share"] . '%' : '',
        ]);
        if (empty($fields)) {
            $fields = ['*'];
        }

        // Display users list
        $response->getBody()->write(
            json_encode(
                $this->database()->find(
                    "users",
                    [
                        "user_id",
                        "email",
                        "first_name",
                        "last_name",
                        "device",
                        "nb_share",
                        "created_at"
                    ],
                    $fields,
                    order: "created_at DESC"
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
                        "nb_share",
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

        // Check if user already exist
        if ($this->checkExist("email", $GLOBALS["body"], "users")) {
            // Fetch user_id
            $user = $this->database()->find(
                "users",
                [
                    "user_id",
                    "nb_share"
                ],
                [
                    "email" => $GLOBALS["body"]["email"]
                ],
                true
            );

            // Edit user information
            $this->database()->update(
                "users",
                [
                    "email" => $GLOBALS["body"]["email"],
                    "first_name" => $GLOBALS["body"]["first_name"],
                    "last_name" => $GLOBALS["body"]["last_name"],
                    "device" => $GLOBALS["body"]["device"] ?? '',
                    "nb_share" => (int) $user["nb_share"] + 1
                ],
                ["user_id" => $user["user_id"]]
            );

            // Add log
            $this->addLog(Action::Edit, $user["user_id"], $this->type, source_type: $GLOBALS["session"]["scope"] == "app" ? Type::App : Type::Admin);

            // Display success code
            return $this->successCode()->success();
        }

        // Create new user
        $user_id = ($this->database()->create(
            "users",
            [
                "email" => $GLOBALS["body"]["email"],
                "first_name" => $GLOBALS["body"]["first_name"],
                "last_name" => $GLOBALS["body"]["last_name"],
                "device" => $GLOBALS["body"]["device"] ?? ''
            ],
            "user_id"
        ))["user_id"];

        // Add log
        $this->addLog(Action::Add, $user_id, $this->type, source_type: $GLOBALS["session"]["scope"] == "app" ? Type::App : Type::Admin);

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

        // Add log
        $this->addLog(Action::Remove, $args["user_id"], $this->type);

        // Remove user
        $this->database()->delete(
            "users",
            ["user_id" => $args["user_id"]]
        );

        // Display success code
        return $this->successCode()->success();
    }

    /**
     * Delete existing users
     * Usage: PUT /users/delete | Scope: super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Unauthorized if user don't have the permission
     */
    public function deleteUsers(Request $request, Response $response): Response
    {
        // Check scope before accessing function
        $this->checkScope();

        // Check if users exist in request
        $this->checkExist("users", $GLOBALS["body"], strict: true);

        // Delete each users
        $errors = 0;
        foreach ($GLOBALS["body"]["users"] as $user) {
            $user = $this->database()->find(
                "users",
                [
                    "user_id",
                ],
                ["user_id" => $user],
                true,
                exception: false
            );

            if ($user) {
                // Add log
                $this->addLog(Action::Remove, $user["user_id"], $this->type);

                // Remove user
                $this->database()->delete(
                    "users",
                    ["user_id" => $user["user_id"]]
                );
            } else {
                $errors ++;
            }
        }

        // Response according to errors
        if (count($GLOBALS["body"]["users"]) == $errors) {
            return $this->errorCode()->badRequest();
        }
        return $this->successCode()->success();
    }
}