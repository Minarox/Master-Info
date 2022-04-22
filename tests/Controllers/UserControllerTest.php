<?php
declare(strict_types = 1);

namespace Controllers;

require_once __DIR__ . "/../TestCase.php";

use BadRequest;
use Enums\Action;
use Enums\Type;
use NotFound;
use TestCase;
use Unauthorized;

/**
 * Test class for UserController
 */
class UserControllerTest extends TestCase
{
    /**
     * @var UserController $userController
     */
    private UserController $userController;

    /**
     * @var string $user_id
     */
    private string $user_id;

    /**
     * @var string $type
     */
    private string $type;

    /**
     * Construct UserController for tests
     *
     * @param string|null $name
     * @param array       $data
     * @param string      $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->userController = new UserController();
        $this->type           = Type::User->name;
        $GLOBALS["pdo"]       = $this->userController->database()->getPdo();
    }

    /**
     * Test getUsers function
     * Usage: GET /users | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetUsers()
    {
        // Call function
        $request = $this->createRequest("GET", "/users");
        $result = $this->userController->getUsers($request, $this->response);

        // Check if request = database and http code is correct
        self::assertSame(
            json_encode(
                $GLOBALS["pdo"]
                    ->query("SELECT user_id, email, first_name, last_name FROM users ORDER BY first_name LIMIT 300;")
                    ->fetchAll()
            ),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test getUsers function without permission
     * Usage: GET /users | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetUsersWithoutScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "app";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $request = $this->createRequest("GET", "/users");
        $this->userController->getUsers($request, $this->response);
    }

    /**
     * Test getUser function
     * Usage: GET /users/{user_id} | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetUser()
    {
        // Call function
        $request = $this->createRequest("GET", "/users/" . $this->user_id);
        $result = $this->userController->getUser($request, $this->response, ["user_id" => $this->user_id]);

        // Check if request = database and http code is correct
        self::assertSame(
            json_encode(
                $GLOBALS["pdo"]
                    ->query("SELECT email, first_name, last_name, device, created_at, updated_at FROM users WHERE user_id = '$this->user_id' LIMIT 1;")
                    ->fetch()
            ),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test getUser function without permission
     * Usage: GET /users/{user_id} | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetUserWithoutScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "app";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $request = $this->createRequest("GET", "/users/" . $this->user_id);
        $this->userController->getUser($request, $this->response, ["user_id" => $this->user_id]);
    }

    /**
     * Test getUser function without params
     * Usage: GET /users/{user_id} | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetUserWithoutParams()
    {
        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("GET", "/users/");
        $this->userController->getUser($request, $this->response, []);
    }

    /**
     * Test getUser function with bad ID
     * Usage: GET /users/{user_id} | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetUserWithBadID()
    {
        // Check if exception is thrown
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage("Nothing was found in the database");

        // Call function
        $request = $this->createRequest("GET", "/users/00000000-0000-0000-0000-000000000000");
        $this->userController->getUser($request, $this->response, ["user_id" => "00000000-0000-0000-0000-000000000000"]);
    }

    /**
     * Test addUser function
     * Usage: POST /users | Scope: app, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testAddUser()
    {
        // Fields
        $GLOBALS["body"] = [
            "email"            => "test_add2@example.com",
            "first_name"       => "Test_add_user2",
            "last_name"        => "User_add2"
        ];

        // Call function
        $request = $this->createRequest("POST", "/users");
        $result = $this->userController->addUser($request, $this->response);

        // Fetch new user
        $new_user = $GLOBALS["pdo"]
            ->query("SELECT user_id, email, first_name, last_name FROM users WHERE email = '{$GLOBALS["body"]["email"]}' LIMIT 1;")
            ->fetch();

        // Check if log added = database
        $type = Type::Admin;
        $action = Action::Add;
        $name = $new_user["first_name"] . ' ' . $new_user["last_name"];
        $log_id = $GLOBALS["pdo"]
            ->query("SELECT log_id FROM logs WHERE source_id = '{$GLOBALS["session"]["user_id"]}' AND source_type = '$type->name' AND action = '$action->name' AND target = '$name' AND target_id = '{$new_user["user_id"]}' AND target_type = '$this->type' LIMIT 1;")
            ->fetchColumn();
        self::assertNotFalse((bool) $log_id);

        // Remove new log
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM logs WHERE log_id = '$log_id';")
            ->execute();

        // Remove new user
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM users WHERE email = '{$GLOBALS["body"]["email"]}';")
            ->execute();

        // Check if request = database and http code is correct
        self::assertSame(array_slice($new_user, 1), $GLOBALS["body"]);
        $this->assertHTTPCode($result, 201, "Created");
    }

    /**
     * Test addUser function without permission
     * Usage: POST /users | Scope: app, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testAddUserWithoutScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "admin";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $request = $this->createRequest("POST", "/users");
        $this->userController->addUser($request, $this->response);
    }

    /**
     * Test addUser function without params
     * Usage: POST /users | Scope: app, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testAddUserWithoutParams()
    {
        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("POST", "/users");
        $this->userController->addUser($request, $this->response);
    }

    /**
     * Test addUser function with missing params
     * Usage: POST /users | Scope: app, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testAddUserWithMissingParams()
    {
        // Fields
        $GLOBALS["body"] = [
            "email" => "test_add2@example.com"
        ];

        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("POST", "/users");
        $this->userController->addUser($request, $this->response);
    }

    /**
     * Test editUser function
     * Usage: PUT /users/{user_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testEditUser()
    {
        $GLOBALS["body"] = [
            "email"      => "test123@example.com",
            "first_name" => "Test_edit",
            "last_name"  => "User_edit",
            "device"     => "test"
        ];

        // Call function
        $request = $this->createRequest("PUT", "/users/" . $this->user_id);
        $result = $this->userController->editUser($request, $this->response, ["user_id" => $this->user_id]);

        // Fetch user
        $edit_user = $GLOBALS["pdo"]
            ->query("SELECT user_id, email, first_name, last_name, device FROM users WHERE email = '{$GLOBALS["body"]["email"]}' LIMIT 1;")
            ->fetch();

        // Check if log added = database
        $type = Type::Admin;
        $action = Action::Edit;
        $name = $edit_user["first_name"] . ' ' . $edit_user["last_name"];
        $log_id = $GLOBALS["pdo"]
            ->query("SELECT log_id FROM logs WHERE source_id = '{$GLOBALS["session"]["user_id"]}' AND source_type = '$type->name' AND action = '$action->name' AND target = '$name' AND target_id = '{$edit_user["user_id"]}' AND target_type = '$this->type' LIMIT 1;")
            ->fetchColumn();
        self::assertNotFalse((bool) $log_id);

        // Remove new log
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM logs WHERE log_id = '$log_id';")
            ->execute();

        // Check if http code is correct
        self::assertSame(array_slice($edit_user, 1), $GLOBALS["body"]);
        $this->assertHTTPCode($result);
    }

    /**
     * Test editUser function without permission
     * Usage: PUT /users/{user_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testEditUserWithoutScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "admin";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $request = $this->createRequest("PUT", "/users/" . $this->user_id);
        $this->userController->editUser($request, $this->response, ["user_id" => $this->user_id]);
    }

    /**
     * Test editUser function without params
     * Usage: PUT /users/{user_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testEditUserWithoutParams()
    {
        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("PUT", "/users/");
        $this->userController->editUser($request, $this->response, []);
    }

    /**
     * Test editUser function without body
     * Usage: PUT /users/{user_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testEditUserWithoutBody()
    {
        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("PUT", "/users/" . $this->user_id);
        $this->userController->editUser($request, $this->response, ["user_id" => $this->user_id]);
    }

    /**
     * Test editUser function with bad ID
     * Usage: PUT /users/{user_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testEditUserWithBadID()
    {
        // Check if exception is thrown
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage("Nothing was found in the database");

        // Call function
        $request = $this->createRequest("PUT", "/users/00000000-0000-0000-0000-000000000000");
        $this->userController->editUser($request, $this->response, ["user_id" => "00000000-0000-0000-0000-000000000000"]);
    }

    /**
     * Test deleteUser function
     * Usage: DELETE /users/{user_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testDeleteUser()
    {
        // Call function
        $request = $this->createRequest("DELETE", "/users/" . $this->user_id);
        $result = $this->userController->deleteUser($request, $this->response, ["user_id" => $this->user_id]);

        // Check if log added = database
        $type = Type::Admin;
        $action = Action::Remove;
        $log_id = $GLOBALS["pdo"]
            ->query("SELECT log_id FROM logs WHERE source_id = '{$GLOBALS["session"]["user_id"]}' AND source_type = '$type->name' AND action = '$action->name' AND target_id = '$this->user_id' AND target_type = '$this->type' LIMIT 1;")
            ->fetchColumn();
        self::assertNotFalse((bool) $log_id);

        // Remove new log
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM logs WHERE log_id = '$log_id';")
            ->execute();

        // Check if http code is correct
        self::assertFalse(
            $GLOBALS["pdo"]
                ->query("SELECT user_id FROM users WHERE user_id = '$this->user_id' LIMIT 1;")
                ->fetchColumn()
        );
        $this->assertHTTPCode($result);
    }

    /**
     * Test deleteUser function with bad ID
     * Usage: DELETE /user_id/{email_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testDeleteUserWithBadID()
    {
        // Check if exception is thrown
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage("Nothing was found in the database");

        // Call function
        $request = $this->createRequest("DELETE", "/users/00000000-0000-0000-0000-000000000000");
        $this->userController->deleteUser($request, $this->response, ["user_id" => "00000000-0000-0000-0000-000000000000"]);
    }

    /**
     * Test deleteUser function without permission
     * Usage: DELETE /users/{user_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testDeleteUserWithoutScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "admin";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $request = $this->createRequest("DELETE", "/users/" . $this->user_id);
        $this->userController->deleteUser($request, $this->response, ["user_id" => $this->user_id]);
    }

    /**
     * Test deleteUser function without params
     * Usage: DELETE /users/{user_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testDeleteUserWithoutParams()
    {
        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("DELETE", "/users/");
        $this->userController->deleteUser($request, $this->response, []);
    }

    /**
     * SetUp parameters before execute tests
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user_id = $GLOBALS["pdo"]
            ->query("INSERT INTO users (email, first_name, last_name, device) VALUES ('test_user@example.com', 'Test', 'User', 'Android 10') RETURNING user_id;")
            ->fetchColumn();
    }

    /**
     * Clean parameters after execute tests
     */
    protected function tearDown(): void
    {
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM users WHERE user_id = '$this->user_id';")
            ->execute();

        parent::tearDown();
    }
}