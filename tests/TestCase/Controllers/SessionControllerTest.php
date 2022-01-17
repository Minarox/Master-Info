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
        $request = $this->createRequest("POST", "/login", ["username" => "test_user_phpunit"]);
        $result = $this->sessionController->login($request, $this->response);

        self::assertSame(
            json_encode($this->pdo->query("SELECT * FROM Users WHERE id = '{$GLOBALS["user"]["id"]}' LIMIT 1;")->fetch()),
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
     * Test login function with bad key
     *
     * @throws BadRequest|NotFound
     */
    public function testLoginWithBadKey()
    {
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Value doesn't exist in array");

        $temp = $GLOBALS["config"]["session"]["maxUsers"];
        $GLOBALS["config"]["session"]["maxUsers"] = 0;

        $request = $this->createRequest("POST", "/login", ["test" => "test_user_phpunit2"]);
        $this->sessionController->login($request, $this->response);

        $GLOBALS["config"]["session"]["maxUsers"] = $temp;
    }

    /**
     * Test session function
     *
     * @throws BadRequest|NotFound
     */
    public function testSession()
    {
        $request = $this->createRequest("GET", "/session");
        $result = $this->sessionController->currentSession($request, $this->response);

        self::assertSame(
            json_encode($this->pdo->query("SELECT * FROM Users WHERE id = '{$GLOBALS["user"]["id"]}' LIMIT 1;")->fetch()),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test session function without token
     *
     * @throws BadRequest|NotFound
     */
    public function testSessionWithoutToken()
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage("Nothing was found in the database");

        $GLOBALS["user"]["token"] = $GLOBALS["user"]["expire"] = null;

        $request = $this->createRequest("GET", "/session");
        $this->sessionController->currentSession($request, $this->response);
    }

    /**
     * Test logout function
     *
     * @throws BadRequest|NotFound
     */
    public function testLogout()
    {
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
     * Test pars body function with JSON body
     *
     * @throws BadRequest
     */
    public function testParseBodyWithJSON()
    {
        $request = $this->createRequest("GET", "", ["key1" => "value1", "key2" => "value2"]);
        $result = $this->sessionController->parseBody($request);

        self::assertSame(
            array(
                "key1" => "value1",
                "key2" => "value2"
            ),
            $result
        );
    }

    /**
     * Test pars body function with FORM body
     *
     * @throws BadRequest
     */
    public function testParseBodyWithFORM()
    {
        $request = $this->createRequest("GET", "", "key1=value1&key2=value2", "application/x-www-form-urlencoded");
        $result = $this->sessionController->parseBody($request);

        self::assertSame(
            array(
                "key1" => "value1",
                "key2" => "value2"
            ),
            $result
        );
    }

    /**
     * Test pars body function with XML body
     *
     * @throws BadRequest
     */
    public function testParseBodyWithXML()
    {
        $body = "<?xml version='1.0' encoding='UTF-8' ?>
                    <root>
                      <key1>value1</key1>
                      <key2>value2</key2>
                    </root>";
        $request = $this->createRequest("GET", "", $body, "application/xml");
        $result = $this->sessionController->parseBody($request);

        self::assertSame(
            array(
                "key1" => "value1",
                "key2" => "value2"
            ),
            $result
        );
    }

    /**
     * Test pars body function with XML text body
     *
     * @throws BadRequest
     */
    public function testParseBodyWithTextXML()
    {
        $body = "<?xml version='1.0' encoding='UTF-8' ?>
                    <root>
                      <key1>value1</key1>
                      <key2>value2</key2>
                    </root>";
        $request = $this->createRequest("GET", "", $body, "text/xml");
        $result = $this->sessionController->parseBody($request);

        self::assertSame(
            array(
                "key1" => "value1",
                "key2" => "value2"
            ),
            $result
        );
    }

    /**
     * Test pars body function with bad content type
     *
     * @throws BadRequest
     */
    public function testParseBodyWithBadContentType()
    {
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Unable to parse body");

        $request = $this->createRequest("GET", "", "key1=value1&key2=value2", "application/javascript");
        $this->sessionController->parseBody($request);
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