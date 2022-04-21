<?php
declare(strict_types = 1);

namespace Controllers;

require_once __DIR__ . "/../TestCase.php";

use BadRequest;
use Enums\Action;
use NotFound;
use TestCase;
use Enums\Type;
use Unauthorized;

/**
 * Test class for UserController
 */
class LogControllerTest extends TestCase
{
    /**
     * @var LogController $logController
     */
    private LogController $logController;

    /**
     * @var int $log_id
     */
    private int $log_id;

    /**
     * Construct LogController for tests
     *
     * @param string|null $name
     * @param array       $data
     * @param string      $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->logController = new LogController();
        $GLOBALS["pdo"]      = $this->logController->database()->getPdo();
    }

    /**
     * Test getLogs function
     * Usage: GET /logs | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetLogs()
    {
        // Call function
        $request = $this->createRequest("GET", "/logs");
        $result = $this->logController->getLogs($request, $this->response);

        // Check if request = database and http code is correct
        self::assertSame(
            json_encode(
                $GLOBALS["pdo"]
                    ->query("SELECT source, source_id, source_type, action, target, target_id, target_type, created_at FROM logs ORDER BY created_at DESC LIMIT 300;")
                    ->fetchAll()
            ),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test getLogs function without permission
     * Usage: GET /logs | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetLogsWithoutScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "admin";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $request = $this->createRequest("GET", "/logs");
        $this->logController->getLogs($request, $this->response);
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
        $this->logController->addLog(
            $type_1->name,
            $type_1,
            $action,
            $GLOBALS["session"]["user_id"],
            $type_2
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
        $this->logController->addLog(
            $GLOBALS["session"]["user_id"],
            $type,
            $action,
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
        $this->logController->addLog(
            $GLOBALS["session"]["user_id"],
            $type_1,
            $action,
            (string) $email_id,
            $type_2
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

    /**
     * SetUp parameters before execute tests
     */
    protected function setUp(): void
    {
        parent::setUp();

        $type   = Type::Admin;
        $action = Action::Edit;
        $this->log_id = $GLOBALS["pdo"]
            ->query("INSERT INTO logs (source, source_id, source_type, action, target, target_id, target_type) VALUES ('Test user', '{$GLOBALS["session"]["user_id"]}', '$type->name', '$action->name', 'Test user 2', '{$GLOBALS["session"]["client_id"]}', '$type->name') RETURNING log_id;")
            ->fetchColumn();
    }

    /**
     * Clean parameters after execute tests
     */
    protected function tearDown(): void
    {
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM logs WHERE log_id = '$this->log_id';")
            ->execute();

        parent::tearDown();
    }
}