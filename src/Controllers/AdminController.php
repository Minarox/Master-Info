<?php
declare(strict_types=1);

namespace Controllers;

use BadRequest;
use Controller;
use NotFound;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * Controller for Actions table
 */
class AdminController extends Controller
{
    /**
     * Get actual config
     *
     * Usage: GET /admin/config | Scope: admin
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     */
    public function getConfig(Request $request, Response $response): Response
    {
        if (!$GLOBALS["user"]["is_admin"]) return $this->errorCode()->forbidden();

        $response->getBody()->write(
            json_encode([
                "maxUsers" => $GLOBALS["config"]["session"]["maxUsers"],
                "usersPerGroup" => $GLOBALS["config"]["groups"]["usersPerGroup"],
                "lastGroupMode" => $GLOBALS["config"]["groups"]["lastGroupMode"]
            ])
        );
        return $response->withStatus(200);
    }

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
        if (!$GLOBALS["user"]["is_admin"]) return $this->errorCode()->forbidden();
        $body = $this->parseBody($request);

        $this->checkExist("max_users", $body);
        if ((int) $body["max_users"] <= 0) return $this->errorCode()->badRequest();

        if ((int) $body["max_users"] != (int) $GLOBALS["config"]["session"]["maxUsers"])
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
        if (!$GLOBALS["user"]["is_admin"]) return $this->errorCode()->forbidden();
        $body = $this->parseBody($request);

        $this->checkExist("users_per_group", $body);
        if ((int) $body["users_per_group"] <= 0) return $this->errorCode()->badRequest();

        if ((int) $body["users_per_group"] != (int) $GLOBALS["config"]["groups"]["usersPerGroup"])
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
        if (!$GLOBALS["user"]["is_admin"]) return $this->errorCode()->forbidden();
        $body = $this->parseBody($request);

        $this->checkExist("last_group_mode", $body);

        if (!($body["last_group_mode"] == "LAST_MIN" || $body["last_group_mode"] == "LAST_MAX"))
            return $this->errorCode()->badRequest();

        if ($body["last_group_mode"] != $GLOBALS["config"]["groups"]["lastGroupMode"])
            $this->updateConfig("groups", "lastGroupMode", $body["last_group_mode"]);

        return $this->successCode()->success();
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
        if (!$GLOBALS["user"]["is_admin"]) return $this->errorCode()->forbidden();
        if (!array_key_exists("user_id", $args)) return $this->errorCode()->badRequest();

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

    /**
     * Get groups from the database
     *
     * Usage: GET /admin/groups | Scope: admin
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     * @throws BadRequest|NotFound
     */
    public function getGroups(Request $request, Response $response): Response
    {
        if (!$GLOBALS["user"]["is_admin"]) return $this->errorCode()->forbidden();

        $data = $this->database()->find(
            "Groups",
            ['*'],
            ['*']
        );

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]["users"] = $this->database()->find(
                "Users",
                ["id", "username", "created_at"],
                ["group_id" => $data[$i]["id"]],
                exception: false
            );
        }

        $response->getBody()->write(
            json_encode($data)
        );
        return $response->withStatus(200);
    }

    /**
     * Delete group from the application
     *
     * Usage: DELETE /admin/group/{group_id} | Scope: admin
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     * @throws BadRequest|NotFound
     */
    public function deleteGroup(Request $request, Response $response, array $args): Response
    {
        if (!$GLOBALS["user"]["is_admin"]) return $this->errorCode()->forbidden();
        if (!array_key_exists("group_id", $args)) return $this->errorCode()->badRequest();

        if ((int) $args["group_id"] <= 0) return $this->errorCode()->badRequest();

        $this->database()->find(
            "Groups",
            ["id"],
            ["id" => (int) $args["group_id"]],
            true
        );

        $this->database()->deleteId("Groups", (int) $args["group_id"]);

        return $this->successCode()->success();
    }
}