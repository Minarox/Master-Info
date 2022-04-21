<?php
declare(strict_types = 1);

namespace Controllers;

require_once __DIR__ . "/../TestCase.php";

use BadRequest;
use Exception;
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
                    ->query("SELECT title, description, subject, content, created_at, updated_at FROM emails WHERE email_id = '$this->email_id' LIMIT 1;")
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
     * Test addEmail function
     * Usage: POST /emails | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testAddEmail()
    {
        // Fields
        $GLOBALS["body"] = [
            "title"       => "Test email model 2",
            "description" => "Email model for unit testing",
            "subject"     => "Test subject 2",
            "content"     => '<!DOCTYPE html>
                              <html lang="en">
                              <head>
                                  <meta charset="UTF-8">
                                  <title>Title</title>
                              </head>
                              <body>
                              
                              </body>
                              </html>'
        ];

        // Call function
        $request = $this->createRequest("POST", "/emails", $GLOBALS["body"]);
        $result = $this->emailController->addEmail($request, $this->response);

        // Fetch new email
        $new_email = $GLOBALS["pdo"]
            ->query("SELECT title, description, subject, content FROM emails WHERE title = '{$GLOBALS["body"]["title"]}' LIMIT 1;")
            ->fetch();

        // Remove new email
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM emails WHERE title = '{$GLOBALS["body"]["title"]}';")
            ->execute();

        // Check if request = database and http code is correct
        self::assertSame($new_email, $GLOBALS["body"]);
        $this->assertHTTPCode($result, 201, "Created");
    }

    /**
     * Test addEmail function without permission
     * Usage: POST /emails | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testAddEmailWithoutScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "app";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $request = $this->createRequest("POST", "/emails");
        $this->emailController->addEmail($request, $this->response);
    }

    /**
     * Test addEmail function without params
     * Usage: POST /emails | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testAddEmailWithoutParams()
    {
        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("POST", "/emails", $GLOBALS["body"] = []);
        $this->emailController->addEmail($request, $this->response);
    }

    /**
     * Test addEmail function with missing params
     * Usage: POST /emails | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testAddEmailWithMissingParams()
    {
        // Fields
        $GLOBALS["body"] = [
            "title" => "Test email model 2",
        ];

        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("POST", "/emails", $GLOBALS["body"]);
        $this->emailController->addEmail($request, $this->response);
    }

    /**
     * Test addTemplateEmail function
     * Usage: POST /emails/{email_id} | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testAddTemplateEmail()
    {
        // Fields
        $GLOBALS["body"] = [
            "title"       => "Test email model 2",
            "description" => "Email model for unit testing"
        ];

        // Call function
        $request = $this->createRequest("POST", "/emails/" . $this->email_id, $GLOBALS["body"]);
        $result = $this->emailController->addTemplateEmail($request, $this->response, ["email_id" => $this->email_id]);

        // Fetch new email
        $new_email = $GLOBALS["pdo"]
            ->query("SELECT title, description, subject, content FROM emails WHERE title = '{$GLOBALS["body"]["title"]}' LIMIT 1;")
            ->fetch();
        $template = $GLOBALS["pdo"]
            ->query("SELECT subject, content FROM emails WHERE email_id = '$this->email_id' LIMIT 1;")
            ->fetch();

        // Remove new email
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM emails WHERE title = '{$GLOBALS["body"]["title"]}';")
            ->execute();

        // Check if request = database and http code is correct
        self::assertSame(
            [
                "subject" => $new_email["subject"],
                "content" => $new_email["content"]
            ],
            $template
        );
        $this->assertHTTPCode($result, 201, "Created");
    }

    /**
     * Test addTemplateEmail function without permission
     * Usage: POST /emails/{email_id} | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testAddTemplateEmailWithoutScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "app";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $request = $this->createRequest("POST", "/emails/" . $this->email_id);
        $this->emailController->addTemplateEmail($request, $this->response, ["email_id" => $this->email_id]);
    }

    /**
     * Test addTemplateEmail function without params
     * Usage: POST /emails | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testAddTemplateEmailWithoutParams()
    {
        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("POST", "/emails/" . $this->email_id, $GLOBALS["body"] = []);
        $this->emailController->addTemplateEmail($request, $this->response, ["email_id" => $this->email_id]);
    }

    /**
     * Test addTemplateEmail function without body
     * Usage: POST /emails/{email_id} | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testAddTemplateEmailWithoutBody()
    {
        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("POST", "/emails/" . $this->email_id, $GLOBALS["body"] = []);
        $this->emailController->addTemplateEmail($request, $this->response, ["email_id" => $this->email_id]);
    }

    /**
     * Test addTemplateEmail function with bad ID
     * Usage: POST /emails/{email_id} | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testAddTemplateEmailWithBadID()
    {
        // Check if exception is thrown
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage("Nothing was found in the database");

        // Call function
        $request = $this->createRequest("POST", "/emails/0", $GLOBALS["body"] = []);
        $this->emailController->addTemplateEmail($request, $this->response, ["email_id" => "0"]);
    }

    /**
     * Test editEmail function
     * Usage: PUT /emails/{email_id} | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testEditEmail()
    {
        $GLOBALS["body"] = [
            "title"       => "Test email model 2",
            "description" => "email model for unit testing",
        ];

        // Call function
        $request = $this->createRequest("PUT", "/emails/" . $this->email_id, $GLOBALS["body"]);
        $result = $this->emailController->editEmail($request, $this->response, ["email_id" => $this->email_id]);

        // Check if http code is correct
        $this->assertHTTPCode($result);
    }

    /**
     * Test editEmail function without permission
     * Usage: PUT /emails/{email_id} | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testEditEmailWithoutScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "app";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $request = $this->createRequest("PUT", "/emails/" . $this->email_id, $GLOBALS["body"] = []);
        $this->emailController->editEmail($request, $this->response, ["email_id" => $this->email_id]);
    }

    /**
     * Test editEmail function without params
     * Usage: PUT /emails/{email_id} | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testEditEmailWithoutParams()
    {
        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("PUT", "/emails/");
        $this->emailController->editEmail($request, $this->response, []);
    }

    /**
     * Test editEmail function without body
     * Usage: PUT /emails/{email_id} | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testEditEmailWithoutBody()
    {
        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("PUT", "/emails/" . $this->email_id, $GLOBALS["body"] = []);
        $this->emailController->editEmail($request, $this->response, ["email_id" => $this->email_id]);
    }

    /**
     * Test editEmail function with bad ID
     * Usage: PUT /emails/{email_id} | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testEditEmailWithBadID()
    {
        // Check if exception is thrown
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage("Nothing was found in the database");

        // Call function
        $request = $this->createRequest("PUT", "/emails/0", $GLOBALS["body"] = []);
        $this->emailController->editEmail($request, $this->response, ["email_id" => "0"]);
    }

    /**
     * Test deleteEmail function
     * Usage: DELETE /emails/{email_id} | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testDeleteEmail()
    {
        // Call function
        $request = $this->createRequest("DELETE", "/emails/" . $this->email_id);
        $result = $this->emailController->deleteEmail($request, $this->response, ["email_id" => $this->email_id]);

        // Check if http code is correct
        $this->assertHTTPCode($result);
    }

    /**
     * Test deleteEmail function with bad ID
     * Usage: DELETE /email_id/{email_id} | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testDeleteEmailWithBadID()
    {
        // Check if exception is thrown
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage("Nothing was found in the database");

        // Call function
        $request = $this->createRequest("DELETE", "/emails/0");
        $this->emailController->deleteEmail($request, $this->response, ["email_id" => "0"]);
    }

    /**
     * Test deleteEmail function without permission
     * Usage: DELETE /emails/{email_id} | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testDeleteEmailWithoutScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "admin";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $request = $this->createRequest("DELETE", "/emails/" . $this->email_id);
        $this->emailController->deleteEmail($request, $this->response, ["email_id" => $this->email_id]);
    }

    /**
     * Test deleteEmail function without params
     * Usage: DELETE /emails/{email_id} | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testDeleteEmailWithoutParams()
    {
        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("DELETE", "/emails/");
        $this->emailController->deleteEmail($request, $this->response, []);
    }

    /**
     * Test sendEmails function
     * Usage: POST /emails/send | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized|Exception
     */
    public function testSendEmails()
    {
        // Create new user
        $user_id = $GLOBALS["pdo"]
            ->query("INSERT INTO users (email, first_name, last_name, device) VALUES ('postmaster@minarox.fr', 'Test', 'User', 'Android 10') RETURNING user_id;")
            ->fetchColumn();

        // Fields
        $GLOBALS["body"] = [
            "email_id" => $this->email_id,
            "users" => [
                $user_id
            ]
        ];

        // Call function
        $request = $this->createRequest("POST", "/emails/send", $GLOBALS["body"]);
        $result  = $this->emailController->sendEmails($request, $this->response);

        // Remove user
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM users WHERE user_id = '$user_id';")
            ->execute();

        // Check if http code is correct
        $this->assertHTTPCode($result, 200, "All emails have been sent");
    }

    /**
     * Test sendEmails function without permission
     * Usage: POST /emails/send | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized|Exception
     */
    public function testSendEmailsWithoutScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "app";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $request = $this->createRequest("POST", "/emails/send", $GLOBALS["body"] = []);
        $this->emailController->sendEmails($request, $this->response);
    }

    /**
     * Test sendEmails function without params
     * Usage: POST /emails/send | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized|Exception
     */
    public function testSendEmailsWithoutParams()
    {
        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("POST", "/emails/send", $GLOBALS["body"] = []);
        $this->emailController->sendEmails($request, $this->response);
    }

    /**
     * Test sendEmails function with bad email id
     * Usage: POST /emails/send | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized|Exception
     */
    public function testSendEmailsWithBadEmailID()
    {
        // Fields
        $GLOBALS["body"] = [
            "email_id" => 0
        ];

        // Check if exception is thrown
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage("Nothing was found in the database");

        // Call function
        $request = $this->createRequest("POST", "/emails/send", $GLOBALS["body"]);
        $this->emailController->sendEmails($request, $this->response);
    }

    /**
     * Test sendEmails function with bad user id
     * Usage: POST /emails/send | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized|Exception
     */
    public function testSendEmailsWithBadUserID()
    {
        // Fields
        $GLOBALS["body"] = [
            "email_id" => $this->email_id,
            "users" => [
                "00000000-0000-0000-0000-000000000000"
            ]
        ];

        // Call function
        $request = $this->createRequest("POST", "/emails/send", $GLOBALS["body"]);
        $result = $this->emailController->sendEmails($request, $this->response);

        // Check if http code is correct
        $this->assertHTTPCode($result, 400, "Bad Request");
    }

    /**
     * Test sendEmails function with good and bad user id
     * Usage: POST /emails/send | Scope: admin, super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized|Exception
     */
    public function testSendEmailsWithGoodAndBadUserID()
    {
        // Create new user
        $user_id = $GLOBALS["pdo"]
            ->query("INSERT INTO users (email, first_name, last_name, device) VALUES ('postmaster@minarox.fr', 'Test', 'User', 'Android 10') RETURNING user_id;")
            ->fetchColumn();

        // Fields
        $GLOBALS["body"] = [
            "email_id" => $this->email_id,
            "users" => [
                $user_id,
                "00000000-0000-0000-0000-000000000000"
            ]
        ];

        // Call function
        $request = $this->createRequest("POST", "/emails/send", $GLOBALS["body"]);
        $result = $this->emailController->sendEmails($request, $this->response);

        // Remove user
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM users WHERE user_id = '$user_id';")
            ->execute();

        // Check if http code is correct
        $this->assertHTTPCode($result, 200, "1 out of 2 emails were sent");
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
            ->query("INSERT INTO emails (title, description, subject, content) VALUES ('Test email model', 'Email model for unit testing', 'Test subject', '$content') RETURNING email_id;")
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