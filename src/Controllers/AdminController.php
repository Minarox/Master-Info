<?php
declare(strict_types=1);

namespace Controllers;

use Controller;
use BadRequest;
use NotFound;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * Controller for Actions table
 */
class AdminController extends Controller
{
    /**
     * Set max user account number
     *
     * Usage: POST /admin/max-users | Scope: admin
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     * @throws BadRequest|NotFound
     */
    public function setMaxUsers(Request $request, Response $response): Response
    {
        if (!$GLOBALS["user"]["is_admin"]) return $this->errorCode()->unauthorized();
        $body = $this->parseBody($request);

        $this->checkExist("max_users", $body);
        if ((int) $body["max_users"] <= 0) return $this->errorCode()->badRequest();

        $this->updateConfig("session", "maxUsers", (int) $body["max_users"]);

        return $this->successCode()->success();
    }

    /**
     * Set max users per group number
     *
     * Usage: POST /admin/users-per-group | Scope: admin
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     * @throws BadRequest|NotFound
     */
    public function setUsersPerGroup(Request $request, Response $response): Response
    {
        if (!$GLOBALS["user"]["is_admin"]) return $this->errorCode()->unauthorized();
        $body = $this->parseBody($request);

        $this->checkExist("users_per_group", $body);
        if ((int) $body["users_per_group"] <= 0) return $this->errorCode()->badRequest();

        $this->updateConfig("groups", "usersPerGroup", (int) $body["users_per_group"]);

        return $this->successCode()->success();
    }

    /**
     * Set last group mode
     *
     * Usage: POST /admin/last-group | Scope: admin
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     * @throws BadRequest|NotFound
     */
    public function setLastGroupConfig(Request $request, Response $response): Response
    {
        if (!$GLOBALS["user"]["is_admin"]) return $this->errorCode()->unauthorized();
        $body = $this->parseBody($request);

        $this->checkExist("last_group_mode", $body);

        if ($body["last_group_mode"] == "LAST_MIN" || $body["last_group_mode"] == "LAST_MAX") {
            $this->updateConfig("groups", "lastGroupMode", $body["last_group_mode"]);
        } else {
            return $this->errorCode()->badRequest();
        }

        return $this->successCode()->success();
    }

    /**
     * Get users list
     *
     * Usage: GET /admin/users | Scope: admin
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     * @throws BadRequest|NotFound
     */
    public function getUsers(Request $request, Response $response): Response
    {
        if (!$GLOBALS["user"]["is_admin"]) return $this->errorCode()->unauthorized();

        $response->getBody()->write(
            json_encode(
                $this->database()->find(
                    "Users",
                    ["id", "username", "group_id", "created_at"],
                    ["is_admin" => '0'],
                    order: "username"
                )
            )
        );
        return $response->withStatus(200);
    }

    /**
     * Delete user from the database
     *
     * Usage: DELETE /admin/user/{user_id} | Scope: admin
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     * @throws BadRequest|NotFound
     */
    public function deleteUser(Request $request, Response $response, array $args): Response
    {
        if (!$GLOBALS["user"]["is_admin"]) return $this->errorCode()->unauthorized();

        if ((int) $args["user_id"] <= 0) return $this->errorCode()->badRequest();

        $this->database()->find(
            "Users",
            ["id"],
            ["id" => (int) $args["user_id"]],
            true
        );

        $this->database()->deleteId("Users", (int) $args["user_id"]);

        return $this->successCode()->success();
    }
}