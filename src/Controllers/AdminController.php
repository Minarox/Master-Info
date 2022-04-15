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
     * @throws Unauthorized if user don't have the permission
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
     * @throws Unauthorized if user don't have the permission
     */
    public function getAdmin(Request $request, Response $response, array $args): Response
    {
        // Check scope before accessing function
        $this->checkScope();

        // Check if admin exist
        $this->checkExist("admin_id", $args, "admins", true, "admin_id");

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
        $expires = $this->database()->find(
            "tokens",
            ["expires - INTERVAL 1 HOUR as time"],
            ["user_id" => $args["admin_id"]],
            true,
            "expires desc",
            false
        );

        $data["last_connexion"] = ($expires) ? $expires["time"] : null;

        // Display admin information
        $response->getBody()->write(
            json_encode($data)
        );
        return $response;
    }

    /**
     * Edit information of an admin
     * Usage: PUT /admins/{admin_id} | Scope: super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Unauthorized if user don't have the permission
     */
    public function editAdmin(Request $request, Response $response, array $args): Response
    {
        // Check scope before accessing function
        $this->checkScope();

        // Check if admin exist
        $this->checkExist("admin_id", $args, "admins", true, "admin_id");

        // Edit admin information
        $this->database()->update(
            "admins",
            [
                "email" => $GLOBALS["body"]["email"] ?? '',
                "first_name" => $GLOBALS["body"]["first_name"] ?? '',
                "last_name" => $GLOBALS["body"]["last_name"] ?? '',
                "scope" => $GLOBALS["body"]["scope"] ?? '',
                "active" => $GLOBALS["body"]["active"] ?? ''
            ],
            ["admin_id" => $args["admin_id"]]
        );

        // Display success code
        return $this->successCode()->success();
    }

    /**
     * Edit admin password
     * Usage: PUT /admins/{admin_id}/password | Scope: super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Unauthorized if user don't have the permission
     */
    public function editAdminPassword(Request $request, Response $response, array $args): Response
    {
        // Check scope before accessing function
        $this->checkScope();

        // Check if admin exist
        $this->checkExist("admin_id", $args, "admins", true, "admin_id");

        // Check if values exist in request
        $this->checkExist("new_password", $GLOBALS["body"], null, true);
        $this->checkExist("confirm_new_password", $GLOBALS["body"], null, true);

        // Check if new passwords are the same
        if ($GLOBALS["body"]["new_password"] !== $GLOBALS["body"]["confirm_new_password"]) {
            return $this->errorCode()->conflict("New passwords doesn't match");
        }

        // Update admin password
        $this->database()->update(
            "admins",
            ["password" => password_hash($GLOBALS["body"]["new_password"], PASSWORD_BCRYPT)],
            ["admin_id" => $args["admin_id"]]
        );

        // Invalidate admin sessions
        $this->database()->delete(
            "tokens",
            ["user_id" => $args["admin_id"]]
        );
        $this->database()->delete(
            "refresh_tokens",
            ["user_id" => $args["admin_id"]]
        );

        // Display success code
        return $this->successCode()->success();
    }

    /**
     * Create new admin
     * Usage: POST /admins | Scope: super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Unauthorized if user don't have the permission
     */
    public function addAdmin(Request $request, Response $response): Response
    {
        // Check scope before accessing function
        $this->checkScope();

        // Check if values exist in request
        $this->checkExist("email", $GLOBALS["body"], null, true);
        $this->checkExist("password", $GLOBALS["body"], null, true);
        $this->checkExist("confirm_password", $GLOBALS["body"], null, true);
        $this->checkExist("first_name", $GLOBALS["body"], null, true);
        $this->checkExist("last_name", $GLOBALS["body"], null, true);

        // Check if new passwords are the same
        if ($GLOBALS["body"]["password"] !== $GLOBALS["body"]["confirm_password"]) {
            return $this->errorCode()->conflict("Passwords doesn't match");
        }

        // Create new admin
        $this->database()->create(
            "admins",
            [
                "email" => $GLOBALS["body"]["email"],
                "password" => password_hash($GLOBALS["body"]["password"], PASSWORD_BCRYPT),
                "first_name" => $GLOBALS["body"]["first_name"],
                "last_name" => $GLOBALS["body"]["last_name"],
                "scope" => $GLOBALS["body"]["scope"] ?? "admin",
                "active" => $GLOBALS["body"]["active"] ?? '1'
            ]
        );

        // Display success code
        return $this->successCode()->created();
    }

    /**
     * Delete existing admin
     * Usage: DELETE /admins/{admin_id} | Scope: super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Unauthorized if user don't have the permission
     */
    public function deleteAdmin(Request $request, Response $response, array $args): Response
    {
        // Check scope before accessing function
        $this->checkScope();

        // Check if admin exist
        $this->checkExist("admin_id", $args, "admins", true, "admin_id");

        // Remove admin
        $this->database()->delete(
            "admins",
            ["admin_id" => $args["admin_id"]]
        );

        // Display success code
        return $this->successCode()->success();
    }
}