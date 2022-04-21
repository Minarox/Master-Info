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
 * Test class for LogController
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