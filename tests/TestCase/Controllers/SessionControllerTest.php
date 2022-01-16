<?php
declare(strict_types=1);

namespace TestCase\Controllers;

require_once __DIR__ . "/../../TestCase.php";

use Controllers\SessionController;
use BadRequest;
use NotFound;
use TestCase;

/**
 * Test class for SessionController
 */
class SessionControllerTest extends TestCase
{
    /**
     * @var SessionController
     */
    private SessionController $sessionController;

    /**
     * Test login function
     *
     * @throws BadRequest|NotFound
     */
    public function testLogin()
    {
        $test_user_id = $this->test_user["id"];
        $request = $this->createRequest("POST", "/login", ["username" => "test_user_phpunit"]);
        $result = $this->sessionController->login($request, $this->response);

        self::assertSame(
            json_encode($this->pdo->query("SELECT * FROM Users WHERE id = '$test_user_id' LIMIT 1;")->fetch()),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test login function without account
     *
     * @throws BadRequest|NotFound
     */
    public function testLoginWithoutAccount()
    {
        $request = $this->createRequest("POST", "/login", ["username" => "test_user_phpunit2"]);
        $result = $this->sessionController->login($request, $this->response);
        $test_user_id = (json_decode($result->getBody()->__toString(), true))["id"];

        self::assertSame(
            json_encode($this->pdo->query("SELECT * FROM Users WHERE id = '$test_user_id' LIMIT 1;")->fetch()),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());

        $this->pdo->prepare("DELETE FROM Users WHERE id = '$test_user_id';")->execute();
    }

    /**
     * Test login function without account with blocked by maxUsers
     *
     * @throws BadRequest|NotFound
     */
    public function testLoginWithoutAccountBlocked()
    {
        $temp = $GLOBALS["config"]["session"]["maxUsers"];
        $GLOBALS["config"]["session"]["maxUsers"] = 0;

        $request = $this->createRequest("POST", "/login", ["username" => "test_user_phpunit2"]);
        $result = $this->sessionController->login($request, $this->response);

        self::assertSame(
            json_encode([
                "code_value" => 409,
                "code_description" => "Maximum number of users reached"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(409, $result->getStatusCode());

        $GLOBALS["config"]["session"]["maxUsers"] = $temp;
    }

    /**
     * Test session function
     *
     * @throws BadRequest|NotFound
     */
    public function testSession()
    {
        $GLOBALS["user"] = $this->test_user;
        $request = $this->createRequest("GET", "/session");
        $result = $this->sessionController->currentSession($request, $this->response);
        $test_user_id = $this->test_user["id"];

        self::assertSame(
            json_encode($this->pdo->query("SELECT * FROM Users WHERE id = '$test_user_id' LIMIT 1;")->fetch()),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test logout function
     *
     * @throws BadRequest|NotFound
     */
    public function testLogout()
    {
        $GLOBALS["user"] = $this->test_user;
        $request = $this->createRequest("GET", "/logout");
        $result = $this->sessionController->logout($request, $this->response);

        self::assertSame(
            json_encode([
                "code_value" => 200,
                "code_description" => "Success"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * SetUp parameters before execute tests
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->sessionController = new SessionController();
    }

    /**
     * Clean parameters after execute tests
     */
    protected function tearDown(): void
    {
        unset($this->sessionController);
        parent::tearDown();
    }
}