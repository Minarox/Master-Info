<?php
declare(strict_types = 1);

namespace Controllers;

require_once __DIR__ . "/../TestCase.php";

use BadRequest;
use NotFound;
use TestCase;
use Unauthorized;

/**
 * Test class for AdminController
 */
class UserControllerTest extends TestCase
{
    /**
     * @var UserController
     */
    private UserController $userController;

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
        $this->userController = new UserController();
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
}