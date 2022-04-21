<?php
declare(strict_types = 1);

namespace Controllers;

require_once __DIR__ . "/../TestCase.php";

use BadRequest;
use NotFound;
use TestCase;
use Unauthorized;

/**
 * Test class for BaseController
 */
class BaseControllerTest extends TestCase
{
    /**
     * @var BaseController $baseController
     */
    private BaseController $baseController;

    /**
     * Construct BaseController for tests
     *
     * @param string|null $name
     * @param array       $data
     * @param string      $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->baseController = new BaseController();
        $GLOBALS["pdo"]       = $this->baseController->database()->getPdo();
    }

    /**
     * Test basePath function
     * Usage: GET / | Scope: none
     */
    public function testBasePath()
    {
        // Call function
        $request = $this->createRequest("GET", "/actions");
        $result  = $this->baseController->basePath($request, $this->response);

        // Check if request = database and http code is correct
        self::assertSame(
            json_encode([
                "version"     => "v0.4",
                "title"       => "Cerealis API",
                "description" => "Enterprise Resource Management API",
                "host"        => "https://mspr.minarox.fr",
                "base_path"   => "/api"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test checkScope function
     *
     * @throws Unauthorized
     */
    public function testCheckScope()
    {
        // Call function
        $result = $this->baseController->checkScope([], false);
        self::assertTrue($result);
    }

    /**
     * Test checkScope function with fake scope
     *
     * @throws Unauthorized
     */
    public function testCheckScopeWithFakeScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "admin";

        // Call function
        $result = $this->baseController->checkScope([], false);
        self::assertFalse($result);
    }

    /**
     * Test checkScope function with fake scope
     *
     * @throws Unauthorized
     */
    public function testCheckScopeWithFakeScopeAndException()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "admin";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $this->baseController->checkScope([], true);
    }

    /**
     * Test checkExist function
     *
     * @throws BadRequest|NotFound
     */
    public function testCheckExist()
    {
        // Fields
        $array = [
            "test" => 0
        ];

        // Call function
        $result = $this->baseController->checkExist("test", $array);
        self::assertTrue($result);
    }

    /**
     * Test checkExist function in table
     *
     * @throws BadRequest|NotFound
     */
    public function testCheckExistInTable()
    {
        // Fields
        $array = [
            "admin_id" => $GLOBALS["session"]["user_id"]
        ];

        // Call function
        $result = $this->baseController->checkExist("admin_id", $array, "admins");
        self::assertTrue($result);
    }

    /**
     * Test checkExist function with fake admin id
     *
     * @throws BadRequest|NotFound
     */
    public function testCheckExistInTableWithFakeID()
    {
        // Fields
        $array = [
            "admin_id" => "00000000-0000-0000-0000-000000000000"
        ];

        // Call function
        $result = $this->baseController->checkExist("admin_id", $array, "admins");
        self::assertFalse($result);
    }

    /**
     * Test checkExist function with fake admin id and exception
     *
     * @throws BadRequest|NotFound
     */
    public function testCheckExistInTableWithFakeIDAndException()
    {
        // Fields
        $array = [
            "admin_id" => "00000000-0000-0000-0000-000000000000"
        ];

        // Check if exception is thrown
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage("Nothing was found in the database");

        // Call function
        $this->baseController->checkExist("admin_id", $array, "admins", true);
    }

    /**
     * Test checkExist function with fake value
     *
     * @throws BadRequest|NotFound
     */
    public function testCheckExistWithFakeValue()
    {
        // Call function
        $result = $this->baseController->checkExist("test", []);
        self::assertFalse($result);
    }

    /**
     * Test checkExist function with fake value
     *
     * @throws BadRequest|NotFound
     */
    public function testCheckExistWithFakeValueAndException()
    {
        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $this->baseController->checkExist("test", [], strict: true);
    }
}