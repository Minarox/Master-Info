<?php
declare(strict_types=1);

namespace TestCase\Controllers;

require_once __DIR__ . "/../../TestCase.php";

use Controllers\GroupController;
use BadRequest;
use NotFound;
use TestCase;

/**
 * Test class for SessionController
 */
class GroupControllerTest extends TestCase
{
    /**
     * @var GroupController
     */
    private GroupController $groupController;

    /**
     * Test add group function
     *
     * @throws BadRequest|NotFound
     */
    public function testAddGroup()
    {
        $this->removeLinkToGroup();
        $temp = $GLOBALS["config"]["session"]["maxUsers"];
        $GLOBALS["config"]["session"]["maxUsers"] = 999;
        $request = $this->createRequest("POST", "/group", ["name" => "test_group_phpunit2"]);
        $result = $this->groupController->addGroup($request, $this->response);

        $test_group = $this->pdo->query("SELECT * FROM Groups WHERE name = 'test_group_phpunit2' LIMIT 1;")->fetch();

        self::assertSame(
            json_encode($test_group),
            $result->getBody()->__toString()
        );
        self::assertSame(201, $result->getStatusCode());
        self::assertNotNull($this->pdo->query("SELECT group_id FROM Users WHERE id = '{$GLOBALS["user"]["id"]}' LIMIT 1;")->fetchColumn());

        $this->pdo->prepare("DELETE FROM Groups WHERE id = '{$test_group["id"]}'")->execute();

        $GLOBALS["config"]["session"]["maxUsers"] = $temp;
    }

    /**
     * Test add group function with user already in group
     *
     * @throws BadRequest|NotFound
     */
    public function testAddGroupAlreadyInGroup()
    {
        $request = $this->createRequest("POST", "/group", ["name" => "test_group_phpunit2"]);
        $result = $this->groupController->addGroup($request, $this->response);

        self::assertSame(
            json_encode([
                "code_value" => 409,
                "code_description" => "Leave the group before creating another one"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(409, $result->getStatusCode());
        self::assertNotNull($this->pdo->query("SELECT group_id FROM Users WHERE id = '{$GLOBALS["user"]["id"]}' LIMIT 1;")->fetchColumn());
    }

    /**
     * Test add group function with small ratio
     *
     * @throws BadRequest|NotFound
     */
    public function testAddGroupWithSmallRatio()
    {
        $this->removeLinkToGroup();
        $temp = $GLOBALS["config"]["session"]["maxUsers"];
        $GLOBALS["config"]["session"]["maxUsers"] = 0;
        $request = $this->createRequest("POST", "/group", ["name" => "test_group_phpunit2"]);
        $result = $this->groupController->addGroup($request, $this->response);

        self::assertSame(
            json_encode([
                "code_value" => 409,
                "code_description" => "Maximum number of groups reached"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(409, $result->getStatusCode());
        self::assertFalse($this->pdo->query("SELECT id FROM Groups WHERE name = 'test_group_phpunit2' LIMIT 1;")->fetchColumn());
        self::assertNull($this->pdo->query("SELECT group_id FROM Users WHERE id = '{$GLOBALS["user"]["id"]}' LIMIT 1;")->fetchColumn());

        $GLOBALS["config"]["session"]["maxUsers"] = $temp;
    }

    /**
     * Test add group function with bad key
     *
     * @throws BadRequest|NotFound
     */
    public function testAddGroupWithBadKey()
    {
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Value doesn't exist in array");

        $this->removeLinkToGroup();
        $request = $this->createRequest("POST", "/group", ["test" => "test_group_phpunit2"]);
        $this->groupController->addGroup($request, $this->response);
    }

    /**
     * Test join group function
     *
     * @throws BadRequest|NotFound
     */
    public function testJoinGroup()
    {
        $this->removeLinkToGroup();
        $request = $this->createRequest("GET", "/join/phpunittestlink");
        $result = $this->groupController->joinGroup($request, $this->response, ["group_link" => "phpunittestlink"]);

        self::assertSame(
            json_encode([
                "code_value" => 200,
                "code_description" => "Success"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
        self::assertNotNull($this->pdo->query("SELECT group_id FROM Users WHERE id = '{$GLOBALS["user"]["id"]}' LIMIT 1;")->fetchColumn());
    }

    /**
     * Test join group function with group already full
     *
     * @throws BadRequest|NotFound
     */
    public function testJoinGroupFull()
    {
        $this->removeLinkToGroup();
        $test_user_id2 = $this->pdo->query("INSERT INTO Users (username, is_admin, group_id) VALUES ('test_user_phpunit2', 0, '{$this->test_group['id']}') RETURNING id;")->fetchColumn();

        $temp = $GLOBALS["config"]["groups"]["usersPerGroup"];
        $GLOBALS["config"]["groups"]["usersPerGroup"] = 1;
        $request = $this->createRequest("GET", "/join/phpunittestlink");
        $result = $this->groupController->joinGroup($request, $this->response, ["group_link" => "phpunittestlink"]);

        self::assertSame(
            json_encode([
                "code_value" => 409,
                "code_description" => "The group is full"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(409, $result->getStatusCode());
        self::assertNull($this->pdo->query("SELECT group_id FROM Users WHERE id = '{$GLOBALS["user"]["id"]}' LIMIT 1;")->fetchColumn());

        $GLOBALS["config"]["groups"]["usersPerGroup"] = $temp;
        $this->pdo->prepare("DELETE FROM Users WHERE id = '$test_user_id2';")->execute();
    }

    /**
     * Test join group function with user already in group
     *
     * @throws BadRequest|NotFound
     */
    public function testJoinGroupAlreadyInGroup()
    {
        $request = $this->createRequest("GET", "/join/phpunittestlink");
        $result = $this->groupController->joinGroup($request, $this->response, ["group_link" => "phpunittestlink"]);

        self::assertSame(
            json_encode([
                "code_value" => 409,
                "code_description" => "Leave the group before joining another one"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(409, $result->getStatusCode());
        self::assertNotNull($this->pdo->query("SELECT group_id FROM Users WHERE id = '{$GLOBALS["user"]["id"]}' LIMIT 1;")->fetchColumn());
    }

    /**
     * Test join group function with bad key
     *
     * @throws BadRequest|NotFound
     */
    public function testJoinGroupWithBadKey()
    {
        $this->removeLinkToGroup();
        $request = $this->createRequest("GET", "/join/phpunittestlink");
        $result = $this->groupController->joinGroup($request, $this->response, ["test" => "phpunittestlink"]);

        self::assertSame(
            json_encode([
                "code_value" => 400,
                "code_description" => "Bad Request"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(400, $result->getStatusCode());
        self::assertNull($this->pdo->query("SELECT group_id FROM Users WHERE id = '{$GLOBALS["user"]["id"]}' LIMIT 1;")->fetchColumn());
    }

    /**
     * Test join random group function with only full groups
     *
     * @throws BadRequest|NotFound
     */
    public function testJoinRandomGroupFull()
    {
        $this->removeLinkToGroup();
        $test_user_id2 = $this->pdo->query("INSERT INTO Users (username, is_admin, group_id) VALUES ('test_user_phpunit2', 0, '{$this->test_group['id']}') RETURNING id;")->fetchColumn();

        $temp = $GLOBALS["config"]["groups"]["usersPerGroup"];
        $GLOBALS["config"]["groups"]["usersPerGroup"] = 1;

        $request = $this->createRequest("GET", "/random");
        $result = $this->groupController->joinRandomGroup($request, $this->response);

        self::assertSame(
            json_encode([
                "code_value" => 409,
                "code_description" => "Conflict"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(409, $result->getStatusCode());
        self::assertNull($this->pdo->query("SELECT group_id FROM Users WHERE id = '{$GLOBALS["user"]["id"]}' LIMIT 1;")->fetchColumn());

        $GLOBALS["config"]["groups"]["usersPerGroup"] = $temp;
        $this->pdo->prepare("DELETE FROM Users WHERE id = '$test_user_id2';")->execute();
    }

    /**
     * Test join random group function
     *
     * @throws BadRequest|NotFound
     */
    public function testJoinRandomGroup()
    {
        $this->removeLinkToGroup();
        $test_user_id2 = $this->pdo->query("INSERT INTO Users (username, is_admin, group_id) VALUES ('test_user_phpunit2', 0, '{$this->test_group['id']}') RETURNING id;")->fetchColumn();

        $temp = $GLOBALS["config"]["groups"]["usersPerGroup"];
        $GLOBALS["config"]["groups"]["usersPerGroup"] = 5;

        $request = $this->createRequest("GET", "/random");
        $result = $this->groupController->joinRandomGroup($request, $this->response);

        self::assertSame(
            json_encode([
                "code_value" => 200,
                "code_description" => "Success"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
        self::assertNotNull($this->pdo->query("SELECT group_id FROM Users WHERE id = '{$GLOBALS["user"]["id"]}' LIMIT 1;")->fetchColumn());

        $GLOBALS["config"]["groups"]["usersPerGroup"] = $temp;
        $this->pdo->prepare("DELETE FROM Users WHERE id = '$test_user_id2';")->execute();
    }

    /**
     * Test join random group function with user already in group
     *
     * @throws BadRequest|NotFound
     */
    public function testJoinRandomGroupAlreadyInGroup()
    {
        $request = $this->createRequest("GET", "/random");
        $result = $this->groupController->joinRandomGroup($request, $this->response);

        self::assertSame(
            json_encode([
                "code_value" => 409,
                "code_description" => "Leave the group before joining another one"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(409, $result->getStatusCode());
        self::assertNotNull($this->pdo->query("SELECT group_id FROM Users WHERE id = '{$GLOBALS["user"]["id"]}' LIMIT 1;")->fetchColumn());
    }

    /**
     * Test get current group function
     *
     * @throws BadRequest|NotFound
     */
    public function testGetCurrentGroup()
    {
        $request = $this->createRequest("GET", "/group");
        $result = $this->groupController->getCurrentGroup($request, $this->response);

        self::assertSame(
            json_encode(
                $this->pdo->query("SELECT * FROM Groups WHERE id = '{$GLOBALS["user"]["group_id"]}' LIMIT 1;")->fetch()
            ),
            json_encode(json_decode($result->getBody()->__toString(), true)["group"])
        );
        self::assertSame(
            json_encode(
                $this->pdo->query("SELECT id, username, expire FROM Users WHERE group_id = '{$GLOBALS["user"]["group_id"]}'")->fetchAll()
            ),
            json_encode(json_decode($result->getBody()->__toString(), true)["users"])
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test get current group function without group
     *
     * @throws BadRequest|NotFound
     */
    public function testGetCurrentGroupWithoutGroup()
    {
        $this->removeLinkToGroup();
        $request = $this->createRequest("GET", "/group");
        $result = $this->groupController->getCurrentGroup($request, $this->response);

        self::assertSame(
            json_encode([
                "code_value" => 404,
                "code_description" => "Not Found"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(404, $result->getStatusCode());
        self::assertNull($this->pdo->query("SELECT group_id FROM Users WHERE id = '{$GLOBALS["user"]["id"]}' LIMIT 1;")->fetchColumn());
    }

    /**
     * Test leave current group function without other members
     *
     * @throws BadRequest|NotFound
     */
    public function testLeaveCurrentGroupWithoutOtherMembers()
    {
        $request = $this->createRequest("GET", "/group/leave");
        $result = $this->groupController->leaveCurrentGroup($request, $this->response);

        self::assertSame(
            json_encode([
                "code_value" => 200,
                "code_description" => "Success"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
        self::assertNull($this->pdo->query("SELECT group_id FROM Users WHERE id = '{$GLOBALS["user"]["id"]}' LIMIT 1;")->fetchColumn());
    }

    /**
     * Test leave current group function with only one other members
     *
     * @throws BadRequest|NotFound
     */
    public function testLeaveCurrentGroupWithOneOtherMembers()
    {
        $test_user_id2 = $this->pdo->query("INSERT INTO Users (username, is_admin, group_id) VALUES ('test_user_phpunit2', 0, '{$GLOBALS["user"]["group_id"]}') RETURNING id;")->fetchColumn();
        $request = $this->createRequest("GET", "/group/leave");
        $result = $this->groupController->leaveCurrentGroup($request, $this->response);

        self::assertSame(
            json_encode([
                "code_value" => 200,
                "code_description" => "Success"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
        self::assertNull($this->pdo->query("SELECT group_id FROM Users WHERE id = '{$GLOBALS["user"]["id"]}' LIMIT 1;")->fetchColumn());
        self::assertFalse($this->pdo->query("SELECT admin FROM Groups WHERE id = '{$GLOBALS["user"]["group_id"]}' LIMIT 1;")->fetchColumn());

        $this->pdo->prepare("DELETE FROM Users WHERE id = '$test_user_id2'")->execute();
    }

    /**
     * Test leave current group function with other members
     *
     * @throws BadRequest|NotFound
     */
    public function testLeaveCurrentGroupWithOtherMembers()
    {
        $test_user_id2 = $this->pdo->query("INSERT INTO Users (username, is_admin, group_id) VALUES ('test_user_phpunit2', 0, '{$GLOBALS["user"]["group_id"]}') RETURNING id;")->fetchColumn();
        $test_user_id3 = $this->pdo->query("INSERT INTO Users (username, is_admin, group_id) VALUES ('test_user_phpunit3', 0, '{$GLOBALS["user"]["group_id"]}') RETURNING id;")->fetchColumn();
        $request = $this->createRequest("GET", "/group/leave");
        $result = $this->groupController->leaveCurrentGroup($request, $this->response);

        self::assertSame(
            json_encode([
                "code_value" => 200,
                "code_description" => "Success"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
        self::assertNull($this->pdo->query("SELECT group_id FROM Users WHERE id = '{$GLOBALS["user"]["id"]}' LIMIT 1;")->fetchColumn());
        self::assertNotNull($this->pdo->query("SELECT id FROM Groups WHERE id = '{$GLOBALS["user"]["group_id"]}' LIMIT 1;")->fetchColumn());

        $this->pdo->prepare("DELETE FROM Users WHERE id = '$test_user_id2'")->execute();
        $this->pdo->prepare("DELETE FROM Users WHERE id = '$test_user_id3'")->execute();
    }

    /**
     * Test leave current group function without group
     *
     * @throws BadRequest|NotFound
     */
    public function testLeaveCurrentGroupWithoutGroup()
    {
        $this->removeLinkToGroup();
        $request = $this->createRequest("GET", "/group/leave");
        $result = $this->groupController->leaveCurrentGroup($request, $this->response);

        self::assertSame(
            json_encode([
                "code_value" => 409,
                "code_description" => "Conflict"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(409, $result->getStatusCode());
        self::assertNull($this->pdo->query("SELECT group_id FROM Users WHERE id = '{$GLOBALS["user"]["id"]}' LIMIT 1;")->fetchColumn());
    }

    /**
     * Test get users function
     *
     * @throws BadRequest|NotFound
     */
    public function testGetUsers()
    {
        $request = $this->createRequest("GET", "/admin/users");
        $result = $this->groupController->getUsers($request, $this->response);

        self::assertSame(
            json_encode($this->pdo->query("SELECT id, username, group_id, expire, created_at FROM Users WHERE is_admin = 0 ORDER BY username;")->fetchAll()),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test add user in group verification function with float ratio and last user and last group mode is LAST_MAX
     *
     * @throws BadRequest|NotFound
     */
    public function testAddUserInGroupVerificationWithFloatAndLastAndModeIsLastMax()
    {
        $this->pdo->prepare("UPDATE Users SET group_id = '{$this->test_group["id"]}' WHERE group_id IS NULL")->execute();
        $this->removeLinkToGroup();

        $temp["maxUsers"] = $GLOBALS["config"]["session"]["maxUsers"];
        $temp["usersPerGroup"] = $GLOBALS["config"]["groups"]["usersPerGroup"];
        $temp["lastGroupMode"] = $GLOBALS["config"]["groups"]["lastGroupMode"];
        $GLOBALS["config"]["session"]["maxUsers"] = 19;
        $GLOBALS["config"]["groups"]["usersPerGroup"] = 5;
        $GLOBALS["config"]["groups"]["lastGroupMode"] = "LAST_MAX";

        self::assertTrue($this->groupController->addUserInGroupVerification(4));
        self::assertFalse($this->groupController->addUserInGroupVerification(5));
        self::assertNull($this->pdo->query("SELECT group_id FROM Users WHERE id = '{$GLOBALS["user"]["id"]}' LIMIT 1;")->fetchColumn());

        $GLOBALS["config"]["session"]["maxUsers"] = $temp["maxUsers"];
        $GLOBALS["config"]["groups"]["usersPerGroup"] = $temp["usersPerGroup"];
        $GLOBALS["config"]["groups"]["lastGroupMode"] = $temp["lastGroupMode"];

        $this->pdo->prepare("UPDATE Users SET group_id = NULL WHERE group_id = '{$this->test_group["id"]}'")->execute();
    }

    /**
     * Test add user in group verification function with float ratio and last user and last group mode is LAST_MIN
     *
     * @throws BadRequest|NotFound
     */
    public function testAddUserInGroupVerificationWithFloatAndLastAndModeIsLastMin()
    {
        $this->pdo->prepare("UPDATE Users SET group_id = '{$this->test_group["id"]}' WHERE group_id IS NULL")->execute();
        $this->removeLinkToGroup();

        $temp["maxUsers"] = $GLOBALS["config"]["session"]["maxUsers"];
        $temp["usersPerGroup"] = $GLOBALS["config"]["groups"]["usersPerGroup"];
        $temp["lastGroupMode"] = $GLOBALS["config"]["groups"]["lastGroupMode"];
        $GLOBALS["config"]["session"]["maxUsers"] = 19;
        $GLOBALS["config"]["groups"]["usersPerGroup"] = 5;
        $GLOBALS["config"]["groups"]["lastGroupMode"] = "LAST_MIN";

        self::assertTrue($this->groupController->addUserInGroupVerification(3));
        self::assertFalse($this->groupController->addUserInGroupVerification(4));
        self::assertNull($this->pdo->query("SELECT group_id FROM Users WHERE id = '{$GLOBALS["user"]["id"]}' LIMIT 1;")->fetchColumn());

        $GLOBALS["config"]["session"]["maxUsers"] = $temp["maxUsers"];
        $GLOBALS["config"]["groups"]["usersPerGroup"] = $temp["usersPerGroup"];
        $GLOBALS["config"]["groups"]["lastGroupMode"] = $temp["lastGroupMode"];

        $this->pdo->prepare("UPDATE Users SET group_id = NULL WHERE group_id = '{$this->test_group["id"]}'")->execute();
    }

    /**
     * Test add user in group verification function with float ratio and not last user and last group mode is LAST_MAX
     *
     * @throws BadRequest|NotFound
     */
    public function testAddUserInGroupVerificationWithFloatAndNotLastAndModeIsLastMax()
    {
        $this->pdo->prepare("UPDATE Users SET group_id = '{$this->test_group["id"]}' WHERE group_id IS NULL")->execute();
        $test_user_id2 = $this->pdo->query("INSERT INTO Users (username, is_admin, group_id) VALUES ('test_user_phpunit2', 0, NULL) RETURNING id;")->fetchColumn();
        $this->removeLinkToGroup();

        $temp["maxUsers"] = $GLOBALS["config"]["session"]["maxUsers"];
        $temp["usersPerGroup"] = $GLOBALS["config"]["groups"]["usersPerGroup"];
        $temp["lastGroupMode"] = $GLOBALS["config"]["groups"]["lastGroupMode"];
        $GLOBALS["config"]["session"]["maxUsers"] = 19;
        $GLOBALS["config"]["groups"]["usersPerGroup"] = 5;
        $GLOBALS["config"]["groups"]["lastGroupMode"] = "LAST_MAX";

        self::assertTrue($this->groupController->addUserInGroupVerification(3));
        self::assertFalse($this->groupController->addUserInGroupVerification(4));
        self::assertNull($this->pdo->query("SELECT group_id FROM Users WHERE id = '{$GLOBALS["user"]["id"]}' LIMIT 1;")->fetchColumn());

        $GLOBALS["config"]["session"]["maxUsers"] = $temp["maxUsers"];
        $GLOBALS["config"]["groups"]["usersPerGroup"] = $temp["usersPerGroup"];
        $GLOBALS["config"]["groups"]["lastGroupMode"] = $temp["lastGroupMode"];

        $this->pdo->prepare("DELETE FROM Users WHERE id = '$test_user_id2'")->execute();
        $this->pdo->prepare("UPDATE Users SET group_id = NULL WHERE group_id = '{$this->test_group["id"]}'")->execute();
    }

    /**
     * Test add user in group verification function with float ratio and not last user and last group mode is LAST_MIN
     *
     * @throws BadRequest|NotFound
     */
    public function testAddUserInGroupVerificationWithFloatAndNotLastAndModeIsLastMin()
    {
        $this->pdo->prepare("UPDATE Users SET group_id = '{$this->test_group["id"]}' WHERE group_id IS NULL")->execute();
        $test_user_id2 = $this->pdo->query("INSERT INTO Users (username, is_admin, group_id) VALUES ('test_user_phpunit2', 0, NULL) RETURNING id;")->fetchColumn();
        $this->removeLinkToGroup();

        $temp["maxUsers"] = $GLOBALS["config"]["session"]["maxUsers"];
        $temp["usersPerGroup"] = $GLOBALS["config"]["groups"]["usersPerGroup"];
        $temp["lastGroupMode"] = $GLOBALS["config"]["groups"]["lastGroupMode"];
        $GLOBALS["config"]["session"]["maxUsers"] = 19;
        $GLOBALS["config"]["groups"]["usersPerGroup"] = 5;
        $GLOBALS["config"]["groups"]["lastGroupMode"] = "LAST_MIN";

        self::assertTrue($this->groupController->addUserInGroupVerification(4));
        self::assertFalse($this->groupController->addUserInGroupVerification(5));
        self::assertNull($this->pdo->query("SELECT group_id FROM Users WHERE id = '{$GLOBALS["user"]["id"]}' LIMIT 1;")->fetchColumn());

        $GLOBALS["config"]["session"]["maxUsers"] = $temp["maxUsers"];
        $GLOBALS["config"]["groups"]["usersPerGroup"] = $temp["usersPerGroup"];
        $GLOBALS["config"]["groups"]["lastGroupMode"] = $temp["lastGroupMode"];

        $this->pdo->prepare("DELETE FROM Users WHERE id = '$test_user_id2'")->execute();
        $this->pdo->prepare("UPDATE Users SET group_id = NULL WHERE group_id = '{$this->test_group["id"]}'")->execute();
    }

    /**
     * Test add user in group verification function with int ratio
     *
     * @throws BadRequest|NotFound
     */
    public function testAddUserInGroupVerificationWithInt()
    {
        $this->pdo->prepare("UPDATE Users SET group_id = '{$this->test_group["id"]}' WHERE group_id IS NULL")->execute();
        $this->removeLinkToGroup();

        $temp["maxUsers"] = $GLOBALS["config"]["session"]["maxUsers"];
        $temp["usersPerGroup"] = $GLOBALS["config"]["groups"]["usersPerGroup"];
        $GLOBALS["config"]["session"]["maxUsers"] = 20;
        $GLOBALS["config"]["groups"]["usersPerGroup"] = 5;

        self::assertTrue($this->groupController->addUserInGroupVerification(4));
        self::assertFalse($this->groupController->addUserInGroupVerification(5));
        self::assertNull($this->pdo->query("SELECT group_id FROM Users WHERE id = '{$GLOBALS["user"]["id"]}' LIMIT 1;")->fetchColumn());

        $GLOBALS["config"]["session"]["maxUsers"] = $temp["maxUsers"];
        $GLOBALS["config"]["groups"]["usersPerGroup"] = $temp["usersPerGroup"];

        $this->pdo->prepare("UPDATE Users SET group_id = NULL WHERE group_id = '{$this->test_group["id"]}'")->execute();
    }

    /**
     * SetUp parameters before execute tests
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->groupController = new GroupController();
    }

    /**
     * Clean parameters after execute tests
     */
    protected function tearDown(): void
    {
        unset($this->groupController);
        parent::tearDown();
    }

    /**
     * Remove link to group
     */
    private function removeLinkToGroup(): void
    {
        $GLOBALS["user"]["group_id"] = null;
        $this->pdo->prepare("UPDATE Users SET group_id = NULL WHERE id = '{$GLOBALS["user"]["id"]}'")->execute();
    }
}