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
class EmailControllerTest extends TestCase
{
    /**
     * @var EmailController $emailController
     */
    private EmailController $emailController;

    /**
     * @var int $email_id
     */
    private int $email_id;

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
        $this->emailController = new EmailController();
        $GLOBALS["pdo"]       = $this->emailController->database()->getPdo();
    }

    /**
     * Test getEmails function
     * Usage: GET /emails | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetEmails()
    {
        // Call function
        $request = $this->createRequest("GET", "/emails");
        $result = $this->emailController->getEmails($request, $this->response);

        // Check if request = database and http code is correct
        self::assertSame(
            json_encode(
                $GLOBALS["pdo"]
                    ->query("SELECT email_id, title, description FROM emails ORDER BY email_id LIMIT 300;")
                    ->fetchAll()
            ),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test getEmails function without permission
     * Usage: GET /emails | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetEmailsWithoutScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "app";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $request = $this->createRequest("GET", "/emails");
        $this->emailController->getEmails($request, $this->response);
    }
}