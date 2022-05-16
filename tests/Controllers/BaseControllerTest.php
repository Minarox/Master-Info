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
                "version"     => "v1.4.0",
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
        $result = $this->baseController->checkScope(throwException: false);
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
        $result = $this->baseController->checkScope(throwException: false);
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
        $this->baseController->checkScope(throwException: true);
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

    /**
     * Test addLog function
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testAddLog()
    {
        // Add new log
        $type_1 = Type::App;
        $type_2 = Type::Admin;
        $action = Action::Add;
        $this->baseController->addLog(
            $action,
            $GLOBALS["session"]["user_id"],
            $type_2,
            $type_1->name,
            $type_1
        );

        // Fetch admin full name
        $admin = $GLOBALS["pdo"]
            ->query("SELECT first_name, last_name FROM admins WHERE admin_id = '{$GLOBALS["session"]["user_id"]}' LIMIT 1;")
            ->fetch();
        $name = $admin["first_name"] . ' ' . $admin["last_name"];

        // Check if log added = database
        $log_id = $GLOBALS["pdo"]
            ->query("SELECT log_id FROM logs WHERE source = '$type_1->name' AND source_id = '$type_1->name' AND source_type = '$type_1->name' AND action = '$action->name' AND target = '$name' AND target_id = '{$GLOBALS["session"]["user_id"]}' AND target_type = '$type_2->name' LIMIT 1;")
            ->fetchColumn();
        self::assertNotFalse((bool) $log_id);

        // Remove new log
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM logs WHERE log_id = '$log_id';")
            ->execute();
    }

    /**
     * Test addLog function for admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testAddLogForAdmin()
    {
        // Add new log
        $type = Type::Admin;
        $action = Action::Edit;
        $this->baseController->addLog(
            $action,
            $GLOBALS["session"]["user_id"],
            $type,
            $GLOBALS["session"]["user_id"],
            $type
        );

        // Fetch admin full name
        $admin = $GLOBALS["pdo"]
            ->query("SELECT first_name, last_name FROM admins WHERE admin_id = '{$GLOBALS["session"]["user_id"]}' LIMIT 1;")
            ->fetch();
        $name = $admin["first_name"] . ' ' . $admin["last_name"];

        // Check if log added = database
        $log_id = $GLOBALS["pdo"]
            ->query("SELECT log_id FROM logs WHERE source = '$name' AND source_id = '{$GLOBALS["session"]["user_id"]}' AND source_type = '$type->name' AND action = '$action->name' AND target = '$name' AND target_id = '{$GLOBALS["session"]["user_id"]}' AND target_type = '$type->name' LIMIT 1;")
            ->fetchColumn();
        self::assertNotFalse((bool) $log_id);

        // Remove new log
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM logs WHERE log_id = '$log_id';")
            ->execute();
    }

    /**
     * Test addLog function for email
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testAddLogForEmail()
    {
        // Add email
        $email_id = $GLOBALS["pdo"]
            ->query("INSERT INTO emails (title, description, subject, content) VALUES ('Test email model', 'Email model for unit testing', 'Test subject', '<!DOCTYPE html>') RETURNING email_id;")
            ->fetchColumn();

        // Add new log
        $type_1 = Type::Admin;
        $type_2 = Type::Email;
        $action = Action::Add;
        $this->baseController->addLog(
            $action,
            (string) $email_id,
            $type_2,
            $GLOBALS["session"]["user_id"],
            $type_1
        );

        // Fetch admin full name
        $admin = $GLOBALS["pdo"]
            ->query("SELECT first_name, last_name FROM admins WHERE admin_id = '{$GLOBALS["session"]["user_id"]}' LIMIT 1;")
            ->fetch();
        $name = $admin["first_name"] . ' ' . $admin["last_name"];

        // Fetch email title
        $title = $GLOBALS["pdo"]
            ->query("SELECT title FROM emails WHERE email_id = '$email_id' LIMIT 1;")
            ->fetchColumn();

        // Check if log added = database
        $log_id = $GLOBALS["pdo"]
            ->query("SELECT log_id FROM logs WHERE source = '$name' AND source_id = '{$GLOBALS["session"]["user_id"]}' AND source_type = '$type_1->name' AND action = '$action->name' AND target = '$title' AND target_id = '$email_id' AND target_type = '$type_2->name' LIMIT 1;")
            ->fetchColumn();
        self::assertNotFalse((bool) $log_id);

        // Remove email and new log
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM emails WHERE email_id = '$email_id';")
            ->execute();
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM logs WHERE log_id = '$log_id';")
            ->execute();
    }
}