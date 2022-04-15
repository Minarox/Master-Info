<?php
declare(strict_types = 1);

namespace Controllers;

require_once __DIR__ . "/../TestCase.php";

use BadRequest;
use Unauthorized;
use NotFound;
use TestCase;

/**
 * Test class for AdminController
 */
class AdminControllerTest extends TestCase
{
    /**
     * @var AdminController
     */
    private AdminController $adminController;

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
        $result = $this->adminController->getAdmins($request, $this->response);

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
     * Usage: PUT /admins/{admin_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetAdmin()
    {
        // Call function
        $request = $this->createRequest("GET", "/admins/" . $GLOBALS["session"]["user_id"]);
        $result = $this->adminController->getAdmin($request, $this->response, ["admin_id" => $GLOBALS["session"]["user_id"]]);

        // Fetch admin information
        $data = $GLOBALS["pdo"]
            ->query("SELECT email, first_name, last_name, scope, active, created_at, updated_at FROM admins WHERE admin_id = '{$GLOBALS["session"]["user_id"]}' LIMIT 1;")
            ->fetch();
        $expires = $GLOBALS["pdo"]
            ->query("SELECT expires - INTERVAL 1 HOUR FROM tokens WHERE user_id = '{$GLOBALS["session"]["user_id"]}' ORDER BY expires DESC LIMIT 1;")
            ->fetchColumn();
        $data["last_connexion"] = ($expires) ? $expires : null;

        // Check if request = database and http code is correct
        self::assertSame(
            json_encode($data),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test getAdmins function without permission
     * Usage: PUT /admins/{admin_id} | Scope: super_admin
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
     * Test editAdmin function
     * Usage: PUT /admins/{admin_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testEditAdmin()
    {
        $GLOBALS["body"] = [
            "email" => "test123@example.com",
            "first_name" => "Test_edit",
            "last_name" => "User_edit"
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
        $request = $this->createRequest("PUT", "/admins/" . $GLOBALS["session"]["user_id"]);
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
            "new_password" => "test1234",
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
        $request = $this->createRequest("PUT", "/admins/" . $GLOBALS["session"]["user_id"]. "/password", $GLOBALS["body"] = []);
        $this->adminController->editAdminPassword($request, $this->response, ["admin_id" => $GLOBALS["session"]["user_id"]]);
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
            "new_password" => "test1234",
            "confirm_new_password" => "test!123"
        ];

        // Call function
        $request = $this->createRequest("PUT", "/admins/" . $GLOBALS["session"]["user_id"]. "/password", $GLOBALS["body"]);
        $result = $this->adminController->editAdminPassword($request, $this->response, ["admin_id" => $GLOBALS["session"]["user_id"]]);

        // Check if http code is correct
        $this->assertHTTPCode($result, 409, "New passwords doesn't match");
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
            "email" => "test_add@example.com",
            "password" => "test1234",
            "confirm_password" => "test1234",
            "first_name" => "Test_add_user",
            "last_name" => "User_add",
            "scope" => "admin",
            "active" => '1'
        ];

        // Call function
        $request = $this->createRequest("POST", "/admins", $GLOBALS["body"]);
        $result = $this->adminController->addAdmin($request, $this->response);

        // Fetch password hash
        $new_password = $GLOBALS["pdo"]
            ->query("SELECT password FROM admins WHERE email = '{$GLOBALS["body"]["email"]}' LIMIT 1;")
            ->fetchColumn();

        // Remove new admin
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM admins WHERE email = '{$GLOBALS["body"]["email"]}';")
            ->execute();

        // Check if request = database and http code is correct
        self::assertTrue(password_verify($GLOBALS["body"]["password"], $new_password));
        $this->assertHTTPCode($result, 201, "Created");
    }

    /**
     * Test addAdmin function without permission
     * Usage: POST /admins | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testAddAdminPasswordWithoutScope()
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
    public function testAddAdminPasswordWithoutParams()
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
    public function testAddAdminPasswordWithBadPasswords()
    {
        // Fields
        $GLOBALS["body"] = [
            "email" => "test_add@example.com",
            "password" => "test1234",
            "confirm_password" => "test!1234",
            "first_name" => "Test_add_user",
            "last_name" => "User_add",
            "scope" => "admin",
            "active" => '1'
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
    public function testAddAdminPasswordWithMissingParams()
    {
        // Fields
        $GLOBALS["body"] = [
            "email" => "test_add@example.com",
            "password" => "test1234",
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
     * Test deleteAdmin function without permission
     * Usage: DELETE /admins/{admin_id} | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testDeleteAdmin()
    {
        // Call function
        $request = $this->createRequest("POST", "/admins/" . $GLOBALS["session"]["user_id"]);
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
        $request = $this->createRequest("POST", "/admins/00000000-0000-0000-0000-000000000000");
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
        $request = $this->createRequest("POST", "/admins/" . $GLOBALS["session"]["user_id"]);
        $this->adminController->deleteAdmin($request, $this->response, ["admin_id" => $GLOBALS["session"]["user_id"]]);
    }
}