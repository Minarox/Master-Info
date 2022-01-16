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
     * Test set max users function
     *
     * @throws BadRequest|NotFound
     */
    public function testSetMaxUsers()
    {
        $GLOBALS["user"] = $this->test_user;
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
        $GLOBALS["user"] = $this->test_user;
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
     * Test set max users function without administrator permission
     *
     * @throws BadRequest|NotFound
     */
    public function testSetMaxUsersWithoutAdminPerm()
    {
        $GLOBALS["user"] = $this->test_user;
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
        $GLOBALS["user"] = $this->test_user;
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
        $GLOBALS["user"] = $this->test_user;
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
     * Test set users per group function without administrator permission
     *
     * @throws BadRequest|NotFound
     */
    public function testSetUsersPerGroupWithoutAdminPerm()
    {
        $GLOBALS["user"] = $this->test_user;
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
        $GLOBALS["user"] = $this->test_user;
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
        $GLOBALS["user"] = $this->test_user;
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
     * Test set last group config function without administrator permission
     *
     * @throws BadRequest|NotFound
     */
    public function testSetLastGroupConfigWithoutAdminPerm()
    {
        $GLOBALS["user"] = $this->test_user;
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
     * Test get users function
     *
     * @throws BadRequest|NotFound
     */
    public function testGetUsers()
    {
        $GLOBALS["user"] = $this->test_user;
        $request = $this->createRequest("GET", "/admin/users");
        $result = $this->adminController->getUsers($request, $this->response);

        self::assertSame(
            json_encode($this->pdo->query("SELECT id, username, group_id, created_at FROM Users WHERE is_admin = 0;")->fetchAll()),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test get users function without administrator permission
     *
     * @throws BadRequest|NotFound
     */
    public function testGetUsersWithoutAdminPerm()
    {
        $GLOBALS["user"] = $this->test_user;
        $GLOBALS["user"]["is_admin"] = 0;
        $request = $this->createRequest("GET", "/admin/users");
        $result = $this->adminController->getUsers($request, $this->response);

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
     * Test delete user function
     *
     * @throws BadRequest|NotFound
     */
    public function testDeleteUser()
    {
        $GLOBALS["user"] = $this->test_user;
        $test_user_id = $this->test_user["id"];
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
     * Test delete user function with bad value
     *
     * @throws BadRequest|NotFound
     */
    public function testDeleteUserWithBadValue()
    {
        $GLOBALS["user"] = $this->test_user;
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
        $GLOBALS["user"] = $this->test_user;
        $GLOBALS["user"]["is_admin"] = 0;
        $test_user_id = $this->test_user["id"];
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
        self::assertSame($test_user_id, $this->pdo->query("SELECT id FROM Users WHERE id = '$test_user_id' LIMIT 1;")->fetchColumn());
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