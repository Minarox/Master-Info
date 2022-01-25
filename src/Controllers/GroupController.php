<?php
declare(strict_types=1);

namespace Controllers;

use BadRequest;
use Controller;
use Exception;
use NotFound;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * Controller for Actions table
 */
class GroupController extends Controller
{
    /**
     * Add new group in the database
     *
     * Usage: POST /group
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     * @throws BadRequest|NotFound
     */
    public function addGroup(Request $request, Response $response): Response
    {
        if ($GLOBALS["user"]["group_id"]) return $this->errorCode()->conflict("Leave the group before creating another one");
        $body = $this->parseBody($request);

        $this->checkExist("name", $body);

        $ratio = $GLOBALS["config"]["session"]["maxUsers"] / $GLOBALS["config"]["groups"]["usersPerGroup"];

        $nbGroups = ($this->database()->find(
            "Groups",
            ["count(id) as groups"],
            ['*'],
            true
        ))["groups"];

        if (is_int($ratio) && $nbGroups < $ratio
            || $GLOBALS["config"]["groups"]["lastGroupMode"] == "LAST_MAX" && $nbGroups < (int) $ratio + 1
            || $GLOBALS["config"]["groups"]["lastGroupMode"] == "LAST_MIN" && $nbGroups < (int) $ratio) {
            $newGroup = $this->database()->create(
                "Groups",
                [
                    "name" => $body["name"],
                    "admin" => $GLOBALS["user"]["id"],
                    "link" => $this->randString()
                ],
                '*'
            );

            $this->database()->update(
                "Users",
                ["group_id" => $newGroup["id"]],
                ["id" => $GLOBALS["user"]["id"]]
            );

            $response->getBody()->write(
                json_encode($newGroup)
            );
            return $response->withStatus(201);
        } else {
            return $this->errorCode()->conflict("Maximum number of groups reached");
        }
    }

    /**
     * Add new group in the database
     *
     * Usage: PUT /group
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     * @throws BadRequest|NotFound
     */
    public function editGroup(Request $request, Response $response): Response
    {
        if (!$GLOBALS["user"]["group_id"]) return $this->errorCode()->notFound();
        $body = $this->parseBody($request);

        $this->checkExist("name", $body);
        $this->checkExist("admin", $body);
        if ((int) $body["admin"] <= 0) return $this->errorCode()->badRequest();

        $this->database()->update(
            "Groups",
            ["name" => $body["name"], "admin" => $body["admin"]],
            ["id" => $GLOBALS["user"]["group_id"], "admin" => $GLOBALS["user"]["id"]]
        );

        return $this->successCode()->success();
    }

    /**
     * Join group with a link
     *
     * Usage: GET /group/join/{group_link}
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     * @throws BadRequest|NotFound
     */
    public function joinGroup(Request $request, Response $response, array $args): Response
    {
        if ($GLOBALS["user"]["group_id"]) return $this->errorCode()->conflict("Leave the group before joining another one");
        if (!array_key_exists("group_link", $args)) return $this->errorCode()->badRequest();

        $group_id = ($this->database()->find(
            "Groups",
            ["id"],
            ["link" => $args["group_link"]],
            true
        ))["id"];

        $nbUsers = ($this->database()->find(
            "Users",
            ["count(id) as users"],
            ["group_id" => $group_id],
            true
        ))["users"];

        if (!$this->newUserInGroupVerification($nbUsers))
            return $this->errorCode()->conflict("The group is full");

        $this->database()->update(
            "Users",
            ["group_id" => $group_id],
            ["id" => $GLOBALS["user"]["id"]]
        );

        return $this->successCode()->success();
    }

    /**
     * Join random group
     *
     * Usage: GET /group/random
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     * @throws BadRequest|NotFound|Exception
     */
    public function joinRandomGroup(Request $request, Response $response): Response
    {
        if ($GLOBALS["user"]["group_id"]) return $this->errorCode()->conflict("Leave the group before joining another one");

        $groups = $this->database()->find(
            "Groups",
            ["id"],
            ['*']
        );

        for ($i = 0; $i < count($groups); $i++) {
           $users = ($this->database()->find(
               "Users",
               ["count(id) as users"],
               ["group_id" => $groups[$i]["id"]],
               true
           ))["users"];

           if ($this->newUserInGroupVerification($users)) {
               $groupsNotFull[] = [
                   "id" => $groups[$i]["id"]
               ];
           } else {
               return $this->errorCode()->conflict();
           }
        }

        $groupSelected = random_int(0, count($groupsNotFull)-1);
        $this->database()->update(
            "Users",
            ["group_id" => $groupsNotFull[$groupSelected]["id"]],
            ["id" => $GLOBALS["user"]["id"]]
        );

        return $this->successCode()->success();
    }

    /**
     * Get current group information
     *
     * Usage: GET /group
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     * @throws BadRequest|NotFound
     */
    public function getCurrentGroup(Request $request, Response $response): Response
    {
        if (!$GLOBALS["user"]["group_id"]) return $this->errorCode()->notFound();

        $data["group"] = $this->database()->find(
            "Groups",
            ['*'],
            ["id" => $GLOBALS["user"]["group_id"]],
            true
        );

        $data["users"] = $this->database()->find(
            "Users",
            ["id", "username", "expire"],
            ["group_id" => $GLOBALS["user"]["group_id"]]
        );

        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    /**
     * Leave the current group
     *
     * Usage: GET /group/leave
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     * @throws BadRequest|NotFound
     * @throws Exception
     */
    public function leaveCurrentGroup(Request $request, Response $response): Response
    {
        if (!$GLOBALS["user"]["group_id"]) return $this->errorCode()->conflict();

        $groupAdmin = ($this->database()->find(
            "Groups",
            ["admin"],
            ["id" => $GLOBALS["user"]["group_id"]],
            true
        ))["admin"];

        if ($groupAdmin == $GLOBALS["user"]["id"]) {
            $group_id = $GLOBALS["user"]["group_id"];
            $newGroupAdmin = $this->database()->getPdo()->query("SELECT id FROM Users WHERE group_id = '$group_id' AND id <> '$groupAdmin';")->fetchAll();
            if ($newGroupAdmin == false || count($newGroupAdmin) < 2) {
                $this->database()->update(
                    "Users",
                    ["group_id" => "null"],
                    ["group_id" => $group_id]
                );
                $this->database()->deleteId("Groups", $GLOBALS["user"]["group_id"]);
            } else {
                $random = random_int(0, count($newGroupAdmin)-1);
                $this->database()->update(
                    "Groups",
                    ["admin" => $newGroupAdmin[$random]["id"]],
                    ["id" => $GLOBALS["user"]["group_id"]]
                );
            }
        }
        $this->database()->update(
            "Users",
            ["group_id" => "null"],
            ["id" => $GLOBALS["user"]["id"]]
        );

        return $this->successCode()->success();
    }

    /**
     * Get users list
     *
     * Usage: GET /group/users
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     * @throws BadRequest|NotFound
     */
    public function getUsers(Request $request, Response $response): Response
    {
        $response->getBody()->write(
            json_encode(
                $this->database()->find(
                    "Users",
                    ["id", "username", "group_id", "expire", "created_at"],
                    ["is_admin" => '0'],
                    order: "username"
                )
            )
        );
        return $response->withStatus(200);
    }

    /**
     * Check if it is possible to join a group
     *
     * @param int $nbUsersInGroup
     * @return bool
     * @throws BadRequest
     * @throws NotFound
     */
    public function newUserInGroupVerification(int $nbUsersInGroup): bool
    {
        $nbUsersNotInGroup = ($this->database()->find(
            "Users",
            ["count(id) as users"],
            ["group_id" => "null"],
            true,
            exception: false
        ))["users"];

        if (is_float($GLOBALS["config"]["session"]["maxUsers"] / $GLOBALS["config"]["groups"]["usersPerGroup"])) {
            if ($nbUsersNotInGroup == 1) {
                if (($GLOBALS["config"]["groups"]["lastGroupMode"] == "LAST_MAX" && $nbUsersInGroup < $GLOBALS["config"]["groups"]["usersPerGroup"])
                    || ($GLOBALS["config"]["groups"]["lastGroupMode"] == "LAST_MIN" && $nbUsersInGroup < $GLOBALS["config"]["groups"]["usersPerGroup"] - 1)) {
                    return true;
                }
            } else {
                if (($GLOBALS["config"]["groups"]["lastGroupMode"] == "LAST_MAX" && $nbUsersInGroup < $GLOBALS["config"]["groups"]["usersPerGroup"] - 1)
                    || ($GLOBALS["config"]["groups"]["lastGroupMode"] == "LAST_MIN" && $nbUsersInGroup < $GLOBALS["config"]["groups"]["usersPerGroup"])) {
                    return true;
                }
            }
        } else {
            if ($nbUsersInGroup < $GLOBALS["config"]["groups"]["usersPerGroup"]) {
                return true;
            }
        }
        return false;
    }
}