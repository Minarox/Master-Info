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
 * Test class for AdminController
 */
class AdminControllerTest extends TestCase
{
    /**
     * @var AdminController $adminController
     */
    private AdminController $adminController;

    /**
     * @var string $type
     */
    private string $type;

    /**
     * Construct AdminController for tests
     *
     * @param string|null $name
     * @param array       $data
     * @param string      $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->adminController = new AdminController();
        $this->type           = Type::Admin->name;
        $GLOBALS["pdo"]        = $this->adminController->database()->getPdo();
    }

    /**
     * Test getAdmins function
     * Usage: GET /admins | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetAdmins()
    {
        // Call function
        $request = $this->createRequest("GET", "/admins");
        $result  = $this->adminController->getAdmins($request, $this->response);

        // Check if request = database and http code is correct
        self::assertSame(
            json_encode(
                $GLOBALS["pdo"]
                    ->query("SELECT admin_id, first_name, last_name, scope, active FROM admins ORDER BY first_name LIMIT 300;")
                    ->fetchAll()
            ),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test getAdmins function without permission
     * Usage: GET /admins | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetAdminsWithoutScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "admin";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $request = $this->createRequest("GET", "/admins");
        $this->adminController->getAdmins($request, $this->response);
    }

    /**
     * Test getAdmin function
     * Usage: GET /admins/{admin_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetAdmin()
    {
        // Call function
        $request = $this->createRequest("GET", "/admins/" . $GLOBALS["session"]["user_id"]);
        $result  = $this->adminController->getAdmin($request, $this->response, ["admin_id" => $GLOBALS["session"]["user_id"]]);

        // Fetch admin information
        $data = $GLOBALS["pdo"]
            ->query("SELECT email, first_name, last_name, scope, active, created_at, updated_at FROM admins WHERE admin_id = '{$GLOBALS["session"]["user_id"]}' LIMIT 1;")
            ->fetch();
        $expires = $GLOBALS["pdo"]
            ->query("SELECT expires - INTERVAL 1 HOUR FROM tokens WHERE user_id = '{$GLOBALS["session"]["user_id"]}' ORDER BY expires DESC LIMIT 1;")
            ->fetchColumn();
        $data["last_connexion"] = ($expires) ?: null;

        // Check if request = database and http code is correct
        self::assertSame(
            json_encode($data),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test getAdmin function without permission
     * Usage: GET /admins/{admin_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetAdminWithoutScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "admin";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $request = $this->createRequest("GET", "/admins/" . $GLOBALS["session"]["user_id"]);
        $this->adminController->getAdmin($request, $this->response, ["admin_id" => $GLOBALS["session"]["user_id"]]);
    }

    /**
     * Test getAdmin function without params
     * Usage: GET /admins/{admin_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetAdminWithoutParams()
    {
        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("GET", "/admins/");
        $this->adminController->editAdmin($request, $this->response, []);
    }

    /**
     * Test getAdmin function with bad ID
     * Usage: GET /admins/{admin_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetAdminWithBadID()
    {
        // Check if exception is thrown
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage("Nothing was found in the database");

        // Call function
        $request = $this->createRequest("GET", "/admins/00000000-0000-0000-0000-000000000000");
        $this->adminController->getAdmin($request, $this->response, ["admin_id" => "00000000-0000-0000-0000-000000000000"]);
    }

    /**
     * Test addAdmin function
     * Usage: POST /admins | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testAddAdmin()
    {
        // Fields
        $GLOBALS["body"] = [
            "email"            => "test_add@example.com",
            "password"         => "test1234",
            "confirm_password" => "test1234",
            "first_name"       => "Test_add_user",
            "last_name"        => "User_add",
            "scope"            => "admin",
            "active"           => 1
        ];

        // Call function
        $request = $this->createRequest("POST", "/admins", $GLOBALS["body"]);
        $result = $this->adminController->addAdmin($request, $this->response);

        // Fetch new admin info
        $new_admin = $GLOBALS["pdo"]
            ->query("SELECT admin_id, password, email, first_name, last_name, scope, active FROM admins WHERE email = '{$GLOBALS["body"]["email"]}' LIMIT 1;")
            ->fetch();

        // Check if log added = database
        $action = Action::Add;
        $name = $new_admin["first_name"] . ' ' . $new_admin["last_name"];
        $log_id = $GLOBALS["pdo"]
            ->query("SELECT log_id FROM logs WHERE source_id = '{$GLOBALS["session"]["user_id"]}' AND source_type = '$this->type' AND action = '$action->name' AND target = '$name' AND target_id = '{$new_admin["admin_id"]}' AND target_type = '$this->type' LIMIT 1;")
            ->fetchColumn();
        self::assertNotFalse((bool) $log_id);

        // Remove new log
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM logs WHERE log_id = '$log_id';")
            ->execute();

        // Remove new admin
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM admins WHERE email = '{$GLOBALS["body"]["email"]}';")
            ->execute();

        // Check if request = database and http code is correct
        self::assertTrue(password_verify($GLOBALS["body"]["password"], $new_admin["password"]));
        unset($GLOBALS["body"]["password"], $GLOBALS["body"]["confirm_password"]);
        self::assertSame($GLOBALS["body"], array_slice($new_admin, 2));
        $this->assertHTTPCode($result, 201, "Created");
    }

    /**
     * Test addAdmin function without permission
     * Usage: POST /admins | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testAddAdminWithoutScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "admin";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $request = $this->createRequest("POST", "/admins");
        $this->adminController->addAdmin($request, $this->response);
    }

    /**
     * Test addAdmin function without params
     * Usage: POST /admins | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testAddAdminWithoutParams()
    {
        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("POST", "/admins", $GLOBALS["body"] = []);
        $this->adminController->addAdmin($request, $this->response);
    }

    /**
     * Test addAdmin function with bad passwords
     * Usage: POST /admins | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testAddAdminWithBadPasswords()
    {
        // Fields
        $GLOBALS["body"] = [
            "email"            => "test_add@example.com",
            "password"         => "test1234",
            "confirm_password" => "test!1234",
            "first_name"       => "Test_add_user",
            "last_name"        => "User_add",
            "scope"            => "admin",
            "active"           => '1'
        ];

        // Call function
        $request = $this->createRequest("POST", "/admins", $GLOBALS["body"]);
        $result = $this->adminController->addAdmin($request, $this->response);

        // Check if http code is correct
        $this->assertHTTPCode($result, 409, "Passwords doesn't match");
    }

    /**
     * Test addAdmin function with missing params
     * Usage: POST /admins | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testAddAdminWithMissingParams()
    {
        // Fields
        $GLOBALS["body"] = [
            "email"            => "test_add@example.com",
            "password"         => "test1234",
            "confirm_password" => "test1234"
        ];

        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("POST", "/admins", $GLOBALS["body"]);
        $this->adminController->addAdmin($request, $this->response);
    }

    /**
     * Test editAdmin function
     * Usage: PUT /admins/{admin_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testEditAdmin()
    {
        $GLOBALS["body"] = [
            "email"      => "test123@example.com",
            "first_name" => "Test_edit",
            "last_name"  => "User_edit"
        ];

        // Call function
        $request = $this->createRequest("PUT", "/admins/" . $GLOBALS["session"]["user_id"], $GLOBALS["body"]);
        $result = $this->adminController->editAdmin($request, $this->response, ["admin_id" => $GLOBALS["session"]["user_id"]]);

        // Check if http code is correct
        $this->assertHTTPCode($result);
    }

    /**
     * Test editAdmin function without permission
     * Usage: PUT /admins/{admin_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testEditAdminWithoutScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "admin";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $request = $this->createRequest("PUT", "/admins/" . $GLOBALS["session"]["user_id"], $GLOBALS["body"] = []);
        $this->adminController->editAdmin($request, $this->response, ["admin_id" => $GLOBALS["session"]["user_id"]]);
    }

    /**
     * Test editAdmin function without params
     * Usage: PUT /admins/{admin_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testEditAdminWithoutParams()
    {
        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("PUT", "/admins/");
        $this->adminController->editAdmin($request, $this->response, []);
    }

    /**
     * Test editAdmin function without body
     * Usage: PUT /admins/{admin_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testEditAdminWithoutBody()
    {
        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("PUT", "/admins/" . $GLOBALS["session"]["user_id"], $GLOBALS["body"] = []);
        $this->adminController->editAdmin($request, $this->response, ["admin_id" => $GLOBALS["session"]["user_id"]]);
    }

    /**
     * Test editAdmin function with bad ID
     * Usage: PUT /admins/{admin_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testEditAdminWithBadID()
    {
        // Check if exception is thrown
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage("Nothing was found in the database");

        // Call function
        $request = $this->createRequest("PUT", "/admins/00000000-0000-0000-0000-000000000000", $GLOBALS["body"] = []);
        $this->adminController->editAdmin($request, $this->response, ["admin_id" => "00000000-0000-0000-0000-000000000000"]);
    }

    /**
     * Test editAdminPassword function
     * Usage: PUT /admins/{admin_id}/password | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testEditAdminPassword()
    {
        $GLOBALS["body"] = [
            "new_password"         => "test1234",
            "confirm_new_password" => "test1234"
        ];

        // Call function
        $request = $this->createRequest("PUT", "/admins/" . $GLOBALS["session"]["user_id"] . "/password", $GLOBALS["body"]);
        $result = $this->adminController->editAdminPassword($request, $this->response, ["admin_id" => $GLOBALS["session"]["user_id"]]);

        // Fetch password hash
        $new_password = $GLOBALS["pdo"]
            ->query("SELECT password FROM admins WHERE admin_id = '{$GLOBALS["session"]["user_id"]}' LIMIT 1;")
            ->fetchColumn();

        // Check if request = database and http code is correct
        self::assertTrue(password_verify($GLOBALS["body"]["new_password"], $new_password));
        $this->assertHTTPCode($result);
    }

    /**
     * Test editAdminPassword function without permission
     * Usage: PUT /admins/{admin_id}/password | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testEditAdminPasswordWithoutScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "admin";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $request = $this->createRequest("PUT", "/admins/" . $GLOBALS["session"]["user_id"]. "/password");
        $this->adminController->editAdminPassword($request, $this->response, ["admin_id" => $GLOBALS["session"]["user_id"]]);
    }

    /**
     * Test editAdminPassword function without params
     * Usage: PUT /admins/{admin_id}/password | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testEditAdminPasswordWithoutParams()
    {
        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("PUT", "/admins/");
        $this->adminController->editAdminPassword($request, $this->response, []);
    }

    /**
     * Test editAdminPassword function without body
     * Usage: PUT /admins/{admin_id}/password | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testEditAdminPasswordWithoutBody()
    {
        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("PUT", "/admins/" . $GLOBALS["session"]["user_id"], $GLOBALS["body"] = []);
        $this->adminController->editAdminPassword($request, $this->response, ["admin_id" => $GLOBALS["session"]["user_id"]]);
    }

    /**
     * Test editAdminPassword function with bad ID
     * Usage: PUT /admins/{admin_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testEditAdminPasswordWithBadID()
    {
        // Check if exception is thrown
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage("Nothing was found in the database");

        // Call function
        $request = $this->createRequest("PUT", "/admins/00000000-0000-0000-0000-000000000000/password", $GLOBALS["body"] = []);
        $this->adminController->editAdminPassword($request, $this->response, ["admin_id" => "00000000-0000-0000-0000-000000000000"]);
    }

    /**
     * Test editAdminPassword function with bad passwords
     * Usage: PUT /admins/{admin_id}/password | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testEditAdminPasswordWithBadPasswords()
    {
        // Fields
        $GLOBALS["body"] = [
            "new_password"         => "test1234",
            "confirm_new_password" => "test!123"
        ];

        // Call function
        $request = $this->createRequest("PUT", "/admins/" . $GLOBALS["session"]["user_id"]. "/password", $GLOBALS["body"]);
        $result = $this->adminController->editAdminPassword($request, $this->response, ["admin_id" => $GLOBALS["session"]["user_id"]]);

        // Check if http code is correct
        $this->assertHTTPCode($result, 409, "New passwords doesn't match");
    }

    /**
     * Test deleteAdmin function
     * Usage: DELETE /admins/{admin_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testDeleteAdmin()
    {
        // Call function
        $request = $this->createRequest("DELETE", "/admins/" . $GLOBALS["session"]["user_id"]);
        $result = $this->adminController->deleteAdmin($request, $this->response, ["admin_id" => $GLOBALS["session"]["user_id"]]);

        // Check if http code is correct
        $this->assertHTTPCode($result);
    }

    /**
     * Test deleteAdmin function with bad ID
     * Usage: DELETE /admins/{admin_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testDeleteAdminWithBadID()
    {
        // Check if exception is thrown
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage("Nothing was found in the database");

        // Call function
        $request = $this->createRequest("DELETE", "/admins/00000000-0000-0000-0000-000000000000");
        $this->adminController->deleteAdmin($request, $this->response, ["admin_id" => "00000000-0000-0000-0000-000000000000"]);
    }

    /**
     * Test deleteAdmin function without permission
     * Usage: DELETE /admins/{admin_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testDeleteAdminWithoutScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "admin";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $request = $this->createRequest("DELETE", "/admins/" . $GLOBALS["session"]["user_id"]);
        $this->adminController->deleteAdmin($request, $this->response, ["admin_id" => $GLOBALS["session"]["user_id"]]);
    }

    /**
     * Test deleteAdmin function without params
     * Usage: DELETE /admins/{admin_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testDeleteAdminWithoutParams()
    {
        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("DELETE", "/admins/");
        $this->adminController->deleteAdmin($request, $this->response, []);
    }
}