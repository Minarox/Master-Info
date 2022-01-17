<?php
declare(strict_types=1);

namespace TestCase\Controllers;

require_once __DIR__ . "/../../TestCase.php";

use Controllers\AdminController;
use BadRequest;
use NotFound;
use TestCase;

/**
 * Test class for SessionController
 */
class AdminControllerTest extends TestCase
{
    /**
     * @var AdminController
     */
    private AdminController $adminController;

    /**
     * Test get config function
     */
    public function testGetConfig()
    {
        $request = $this->createRequest("GET", "/admin/config");
        $result = $this->adminController->getConfig($request, $this->response);

        self::assertSame(
            json_encode([
                "maxUsers" => $GLOBALS["config"]["session"]["maxUsers"],
                "usersPerGroup" => $GLOBALS["config"]["groups"]["usersPerGroup"],
                "lastGroupMode" => $GLOBALS["config"]["groups"]["lastGroupMode"]
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test get config function without administrator permission
     */
    public function testGetConfigWithoutAdminPerm()
    {
        $GLOBALS["user"]["is_admin"] = 0;
        $request = $this->createRequest("GET", "/admin/config");
        $result = $this->adminController->getConfig($request, $this->response);

        self::assertSame(
            json_encode([
                "code_value" => 401,
                "code_description" => "Unauthorized"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(401, $result->getStatusCode());
    }

    /**
     * Test set max users function
     *
     * @throws BadRequest|NotFound
     */
    public function testSetMaxUsers()
    {
        $temp = (int) $GLOBALS["config"]["session"]["maxUsers"];
        $request = $this->createRequest("POST", "/admin/max-users", ["max_users" => "1"]);
        $result = $this->adminController->setMaxUsers($request, $this->response);

        self::assertSame(
            json_encode([
                "code_value" => 200,
                "code_description" => "Success"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
        self::assertSame(1, (int) $GLOBALS["config"]["session"]["maxUsers"]);

        $this->updateConfig("session", "maxUsers", $temp);
    }

    /**
     * Test set max users function with bad value
     *
     * @throws BadRequest|NotFound
     */
    public function testSetMaxUsersWithBadValue()
    {
        $maxUsers = (int) $GLOBALS["config"]["session"]["maxUsers"];
        $request = $this->createRequest("POST", "/admin/max-users", ["max_users" => "-15"]);
        $result = $this->adminController->setMaxUsers($request, $this->response);

        self::assertSame(
            json_encode([
                "code_value" => 400,
                "code_description" => "Bad Request"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(400, $result->getStatusCode());
        self::assertSame($maxUsers, (int) $GLOBALS["config"]["session"]["maxUsers"]);
    }

    /**
     * Test set max users function with bad key
     *
     * @throws BadRequest|NotFound
     */
    public function testSetMaxUsersWithBadKey()
    {
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Value doesn't exist in array");

        $request = $this->createRequest("POST", "/admin/max-users", ["test" => "15"]);
        $this->adminController->setMaxUsers($request, $this->response);
    }

    /**
     * Test set max users function without administrator permission
     *
     * @throws BadRequest|NotFound
     */
    public function testSetMaxUsersWithoutAdminPerm()
    {
        $GLOBALS["user"]["is_admin"] = 0;
        $maxUsers = (int) $GLOBALS["config"]["session"]["maxUsers"];
        $request = $this->createRequest("POST", "/admin/max-users", ["max_users" => "15"]);
        $result = $this->adminController->setMaxUsers($request, $this->response);

        self::assertSame(
            json_encode([
                "code_value" => 401,
                "code_description" => "Unauthorized"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(401, $result->getStatusCode());
        self::assertSame($maxUsers, (int) $GLOBALS["config"]["session"]["maxUsers"]);
    }

    /**
     * Test set users per group function
     *
     * @throws BadRequest|NotFound
     */
    public function testSetUsersPerGroup()
    {
        $temp = (int) $GLOBALS["config"]["groups"]["usersPerGroup"];
        $request = $this->createRequest("POST", "/admin/users-per-group", ["users_per_group" => "50"]);
        $result = $this->adminController->setUsersPerGroup($request, $this->response);

        self::assertSame(
            json_encode([
                "code_value" => 200,
                "code_description" => "Success"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
        self::assertSame(50, (int) $GLOBALS["config"]["groups"]["usersPerGroup"]);

        $this->updateConfig("groups", "usersPerGroup", $temp);
    }

    /**
     * Test set users per group function with bad value
     *
     * @throws BadRequest|NotFound
     */
    public function testSetUsersPerGroupWithBadValue()
    {
        $usersPerGroup = (int) $GLOBALS["config"]["groups"]["usersPerGroup"];
        $request = $this->createRequest("POST", "/admin/users-per-group", ["users_per_group" => "-15"]);
        $result = $this->adminController->setUsersPerGroup($request, $this->response);

        self::assertSame(
            json_encode([
                "code_value" => 400,
                "code_description" => "Bad Request"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(400, $result->getStatusCode());
        self::assertSame($usersPerGroup, (int) $GLOBALS["config"]["groups"]["usersPerGroup"]);
    }

    /**
     * Test set users per group function with bad key
     *
     * @throws BadRequest|NotFound
     */
    public function testSetUsersPerGroupWithBadKey()
    {
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Value doesn't exist in array");

        $request = $this->createRequest("POST", "/admin/users-per-group", ["test" => "15"]);
        $this->adminController->setUsersPerGroup($request, $this->response);
    }

    /**
     * Test set users per group function without administrator permission
     *
     * @throws BadRequest|NotFound
     */
    public function testSetUsersPerGroupWithoutAdminPerm()
    {
        $GLOBALS["user"]["is_admin"] = 0;
        $usersPerGroup = (int) $GLOBALS["config"]["groups"]["usersPerGroup"];
        $request = $this->createRequest("POST", "/admin/users-per-group", ["users_per_group" => "50"]);
        $result = $this->adminController->setUsersPerGroup($request, $this->response);

        self::assertSame(
            json_encode([
                "code_value" => 401,
                "code_description" => "Unauthorized"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(401, $result->getStatusCode());
        self::assertSame($usersPerGroup, (int) $GLOBALS["config"]["groups"]["usersPerGroup"]);
    }

    /**
     * Test set last group config function
     *
     * @throws BadRequest|NotFound
     */
    public function testSetLastGroupConfig()
    {
        $temp = $GLOBALS["config"]["groups"]["lastGroupMode"];
        $request = $this->createRequest("POST", "/admin/last-group", ["last_group_mode" => "LAST_MAX"]);
        $result = $this->adminController->setLastGroupConfig($request, $this->response);

        self::assertSame(
            json_encode([
                "code_value" => 200,
                "code_description" => "Success"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
        self::assertSame("LAST_MAX", $GLOBALS["config"]["groups"]["lastGroupMode"]);

        $this->updateConfig("groups", "lastGroupMode", $temp);
    }

    /**
     * Test set last group config function with bad value
     *
     * @throws BadRequest|NotFound
     */
    public function testSetLastGroupConfigWithBadValue()
    {
        $lastGroupMode = $GLOBALS["config"]["groups"]["lastGroupMode"];
        $request = $this->createRequest("POST", "/admin/last-group", ["last_group_mode" => "test"]);
        $result = $this->adminController->setLastGroupConfig($request, $this->response);

        self::assertSame(
            json_encode([
                "code_value" => 400,
                "code_description" => "Bad Request"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(400, $result->getStatusCode());
        self::assertSame($lastGroupMode, $GLOBALS["config"]["groups"]["lastGroupMode"]);
    }

    /**
     * Test set last group config function with bad key
     *
     * @throws BadRequest|NotFound
     */
    public function testSetLastGroupConfigWithBadKey()
    {
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Value doesn't exist in array");

        $request = $this->createRequest("POST", "/admin/last-group", ["test" => "LAST_MAX"]);
        $this->adminController->setLastGroupConfig($request, $this->response);
    }

    /**
     * Test set last group config function without administrator permission
     *
     * @throws BadRequest|NotFound
     */
    public function testSetLastGroupConfigWithoutAdminPerm()
    {
        $GLOBALS["user"]["is_admin"] = 0;
        $lastGroupMode = $GLOBALS["config"]["groups"]["lastGroupMode"];
        $request = $this->createRequest("POST", "/admin/last-group", ["last_group_mode" => "LAST_MAX"]);
        $result = $this->adminController->setUsersPerGroup($request, $this->response);

        self::assertSame(
            json_encode([
                "code_value" => 401,
                "code_description" => "Unauthorized"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(401, $result->getStatusCode());
        self::assertSame($lastGroupMode, $GLOBALS["config"]["groups"]["lastGroupMode"]);
    }

    /**
     * Test delete user function
     *
     * @throws BadRequest|NotFound
     */
    public function testDeleteUser()
    {
        $test_user_id = $GLOBALS["user"]["id"];
        $request = $this->createRequest("DELETE", "/admin/user/" . $GLOBALS["user"]["id"]);
        $result = $this->adminController->deleteUser($request, $this->response, ["user_id" => $GLOBALS["user"]["id"]]);

        self::assertSame(
            json_encode([
                "code_value" => 200,
                "code_description" => "Success"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
        self::assertFalse($this->pdo->query("SELECT id FROM Users WHERE id = '$test_user_id' LIMIT 1;")->fetch());
    }

    /**
     * Test delete user function without values
     *
     * @throws BadRequest|NotFound
     */
    public function testDeleteUserWithoutValues()
    {
        $request = $this->createRequest("DELETE", "/admin/user/0");
        $result = $this->adminController->deleteUser($request, $this->response, []);

        self::assertSame(
            json_encode([
                "code_value" => 400,
                "code_description" => "Bad Request"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(400, $result->getStatusCode());
    }

    /**
     * Test delete user function with bad value
     *
     * @throws BadRequest|NotFound
     */
    public function testDeleteUserWithBadValue()
    {
        $request = $this->createRequest("DELETE", "/admin/user/test");
        $result = $this->adminController->deleteUser($request, $this->response, ["user_id" => "test"]);

        self::assertSame(
            json_encode([
                "code_value" => 400,
                "code_description" => "Bad Request"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(400, $result->getStatusCode());
    }

    /**
     * Test delete user function without administrator permission
     *
     * @throws BadRequest|NotFound
     */
    public function testDeleteUserWithoutAdminPerm()
    {
        $GLOBALS["user"]["is_admin"] = 0;
        $request = $this->createRequest("DELETE", "/admin/user/" . $GLOBALS["user"]["id"]);
        $result = $this->adminController->deleteUser($request, $this->response, ["user_id" => $GLOBALS["user"]["id"]]);

        self::assertSame(
            json_encode([
                "code_value" => 401,
                "code_description" => "Unauthorized"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(401, $result->getStatusCode());
        self::assertSame($GLOBALS["user"]["id"], $this->pdo->query("SELECT id FROM Users WHERE id = '{$GLOBALS["user"]["id"]}' LIMIT 1;")->fetchColumn());
    }

    /**
     * Test get groups function
     *
     * @throws BadRequest|NotFound
     */
    public function testGetGroups()
    {
        $request = $this->createRequest("GET", "/admin/groups");
        $result = $this->adminController->getGroups($request, $this->response);

        $data = $this->pdo->query("SELECT * FROM Groups;")->fetchAll();
        for ($i = 0; $i < count($data); $i++) {
            $group_id = $data[$i]["id"];
            $data[$i]["users"] = $this->pdo->query("SELECT id, username, created_at FROM Users WHERE group_id = '$group_id'")->fetchAll();
        }

        self::assertSame(
            json_encode($data),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test get groups function without administrator permission
     *
     * @throws BadRequest|NotFound
     */
    public function testGetGroupsWithoutAdminPerm()
    {
        $GLOBALS["user"]["is_admin"] = 0;
        $request = $this->createRequest("GET", "/admin/groups");
        $result = $this->adminController->getGroups($request, $this->response);

        self::assertSame(
            json_encode([
                "code_value" => 401,
                "code_description" => "Unauthorized"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(401, $result->getStatusCode());
    }

    /**
     * Test delete group function
     *
     * @throws BadRequest|NotFound
     */
    public function testDeleteGroup()
    {
        $test_group_id = $GLOBALS["user"]["group_id"];
        $request = $this->createRequest("DELETE", "/admin/group/" . $GLOBALS["user"]["group_id"]);
        $result = $this->adminController->deleteGroup($request, $this->response, ["group_id" => $GLOBALS["user"]["group_id"]]);

        self::assertSame(
            json_encode([
                "code_value" => 200,
                "code_description" => "Success"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
        self::assertFalse($this->pdo->query("SELECT id FROM Groups WHERE id = '$test_group_id' LIMIT 1;")->fetch());
    }

    /**
     * Test delete group function without administrator permission
     *
     * @throws BadRequest|NotFound
     */
    public function testDeleteGroupWithoutAdminPerm()
    {
        $GLOBALS["user"]["is_admin"] = 0;
        $request = $this->createRequest("DELETE", "/admin/group/" . $GLOBALS["user"]["group_id"]);
        $result = $this->adminController->deleteGroup($request, $this->response, ["group_id" => $GLOBALS["user"]["group_id"]]);

        self::assertSame(
            json_encode([
                "code_value" => 401,
                "code_description" => "Unauthorized"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(401, $result->getStatusCode());
        self::assertSame($GLOBALS["user"]["group_id"], $this->pdo->query("SELECT id FROM Groups WHERE id = '{$GLOBALS["user"]["group_id"]}' LIMIT 1;")->fetchColumn());
    }

    /**
     * Test delete group function with bad value
     *
     * @throws BadRequest|NotFound
     */
    public function testDeleteGroupWithBadValue()
    {
        $request = $this->createRequest("DELETE", "/admin/group/test");
        $result = $this->adminController->deleteGroup($request, $this->response, ["group_id" => "test"]);

        self::assertSame(
            json_encode([
                "code_value" => 400,
                "code_description" => "Bad Request"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(400, $result->getStatusCode());
    }

    /**
     * Test delete group function without values
     *
     * @throws BadRequest|NotFound
     */
    public function testDeleteGroupWithoutValues()
    {
        $request = $this->createRequest("DELETE", "/admin/group/0");
        $result = $this->adminController->deleteGroup($request, $this->response, []);

        self::assertSame(
            json_encode([
                "code_value" => 400,
                "code_description" => "Bad Request"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(400, $result->getStatusCode());
    }

    /**
     * Test delete group function
     *
     * @throws BadRequest|NotFound
     */
    public function testDeleteGroups()
    {
        $test_group_id = $GLOBALS["user"]["group_id"];
        $request = $this->createRequest("DELETE", "/admin/group/" . $GLOBALS["user"]["group_id"]);
        $result = $this->adminController->deleteGroup($request, $this->response, ["group_id" => $GLOBALS["user"]["group_id"]]);

        self::assertSame(
            json_encode([
                "code_value" => 200,
                "code_description" => "Success"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
        self::assertFalse($this->pdo->query("SELECT id FROM Groups WHERE id = '$test_group_id' LIMIT 1;")->fetch());
    }

    /**
     * Test delete group function without administrator permission
     *
     * @throws BadRequest|NotFound
     */
    public function testDeleteGroupsWithoutAdminPerm()
    {
        $GLOBALS["user"]["is_admin"] = 0;
        $test_group_id = $GLOBALS["user"]["group_id"];
        $request = $this->createRequest("DELETE", "/admin/group/" . $GLOBALS["user"]["group_id"]);
        $result = $this->adminController->deleteGroup($request, $this->response, ["group_id" => $GLOBALS["user"]["group_id"]]);

        self::assertSame(
            json_encode([
                "code_value" => 401,
                "code_description" => "Unauthorized"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(401, $result->getStatusCode());
        self::assertSame($test_group_id, $this->pdo->query("SELECT id FROM Groups WHERE id = '$test_group_id' LIMIT 1;")->fetchColumn());
    }

    /**
     * Test delete group function with bad value
     *
     * @throws BadRequest|NotFound
     */
    public function testDeleteGroupsWithBadValue()
    {
        $request = $this->createRequest("DELETE", "/admin/group/test");
        $result = $this->adminController->deleteGroup($request, $this->response, ["group_id" => "test"]);

        self::assertSame(
            json_encode([
                "code_value" => 400,
                "code_description" => "Bad Request"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(400, $result->getStatusCode());
    }

    /**
     * Test delete group function with bad value
     *
     * @throws BadRequest|NotFound
     */
    public function testDeleteGroupsWithoutValues()
    {
        $request = $this->createRequest("DELETE", "/admin/group/0");
        $result = $this->adminController->deleteGroup($request, $this->response, []);

        self::assertSame(
            json_encode([
                "code_value" => 400,
                "code_description" => "Bad Request"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(400, $result->getStatusCode());
    }

    /**
     * SetUp parameters before execute tests
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->adminController = new AdminController();
    }

    /**
     * Clean parameters after execute tests
     */
    protected function tearDown(): void
    {
        unset($this->adminController);
        parent::tearDown();
    }
}