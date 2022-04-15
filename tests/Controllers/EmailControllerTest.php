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

    /**
     * Test getEmail function
     * Usage: GET /emails/{email_id} | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetEmail()
    {
        // Call function
        $request = $this->createRequest("GET", "/emails/" . $this->email_id);
        $result = $this->emailController->getEmail($request, $this->response, ["email_id" => $this->email_id]);

        // Check if request = database and http code is correct
        self::assertSame(
            json_encode(
                $GLOBALS["pdo"]
                    ->query("SELECT title, description, content, created_at, updated_at FROM emails WHERE email_id = '$this->email_id' LIMIT 1;")
                    ->fetch()
            ),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test getEmail function without permission
     * Usage: GET /emails/{email_id} | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetEmailWithoutScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "app";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $request = $this->createRequest("GET", "/emails/" . $this->email_id);
        $this->emailController->getEmail($request, $this->response, ["email_id" => $this->email_id]);
    }

    /**
     * Test getEmail function without params
     * Usage: GET /emails/{email_id} | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetEmailWithoutParams()
    {
        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("GET", "/emails/");
        $this->emailController->getEmail($request, $this->response, []);
    }

    /**
     * Test getEmail function with bad ID
     * Usage: GET /emails/{email_id} | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetEmailWithBadID()
    {
        // Check if exception is thrown
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage("Nothing was found in the database");

        // Call function
        $request = $this->createRequest("GET", "/emails/0");
        $this->emailController->getEmail($request, $this->response, ["email_id" => "0"]);
    }

    /**
     * SetUp parameters before execute tests
     */
    protected function setUp(): void
    {
        parent::setUp();

        $content = '<!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <title>Title</title>
                    </head>
                    <body>
                    
                    </body>
                    </html>';
        $this->email_id = $GLOBALS["pdo"]
            ->query("INSERT INTO emails (title, description, content) VALUES ('Test email model', 'Email model for unit testing', '$content') RETURNING email_id;")
            ->fetchColumn();
    }

    /**
     * Clean parameters after execute tests
     */
    protected function tearDown(): void
    {
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM emails WHERE email_id = '$this->email_id';")
            ->execute();

        parent::tearDown();
    }
}