<?php
declare(strict_types = 1);

require_once __DIR__ . "/../app/Loader.php";

use PHPUnit\Framework\TestCase as PHPUnit_TestCase;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Psr7\Uri;

/**
 * Tests case for app
 */
class TestCase extends PHPUnit_TestCase
{
    /**
     * @var Response
     */
    protected Response $response;

    /**
     * Create new request to the API
     *
     * @param string $method
     * @param string $path
     *
     * @return Request
     */
    protected function createRequest(string $method, string $path): Request
    {
        if (!isset($GLOBALS["body"])) {
            $GLOBALS["body"] = [];
        }

        $uri    = new Uri("http", "localhost", 8899, "/v1" . $path);
        $stream = (new StreamFactory())->createStream(json_encode($GLOBALS["body"]));

        $headers = new Headers();
        $headers->addHeader("Content-type", "application/json");
        $headers->addHeader("Cache-control", "no-cache");
        if (!empty($this->session["token"])) {
            $headers->addHeader("Authorization", "Bearer " . $this->session["token"]);
        }

        return new Request($method, $uri, $headers, [], $_SERVER, $stream);
    }

    /**
     * AssertSame with custom http code
     *
     * @param Response $result
     * @param int      $code
     * @param string   $description
     */
    protected function assertHTTPCode(Response $result, int $code = 200, string $description = '')
    {
        if (empty($description)) {
            match ($code) {
                200 => $description = "Success",
                201 => $description = "Created",
                400 => $description = "Bad Request",
                401 => $description = "Unauthorized",
                403 => $description = "Forbidden",
                404 => $description = "Not Found",
                405 => $description = "Method Not Allowed",
                409 => $description = "Conflict",
                410 => $description = "Custom Error Code"
            };
        }

        self::assertSame(
            json_encode([
                "code_value"       => $code,
                "code_description" => $description
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame($code, $result->getStatusCode());
    }

    /**
     * Generate random string
     *
     * @param int $length of the generated string
     *
     * @return string
     */
    protected function randString(int $length = 40): string
    {
        // Password generator with custom length
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        return substr(str_shuffle($chars), 0, $length);
    }

    /**
     * SetUp parameters before execute tests
     */
    protected function setUp(): void
    {
        $this->response = new Response();

        $password = password_hash("test!123", PASSWORD_BCRYPT);
        $new_user = $GLOBALS["pdo"]
            ->query("INSERT INTO admins (email, password, first_name, last_name, scope) VALUES ('test@example.com', '$password', 'Test', 'User', 'super_admin') RETURNING admin_id, email, scope;")
            ->fetch();
        $GLOBALS["session"]["user_id"] = $new_user["admin_id"];
        $GLOBALS["session"]["user_email"] = $new_user["email"];
        $GLOBALS["session"]["scope"] = $new_user["scope"];

         $new_client = $GLOBALS["pdo"]
            ->query("SELECT client_id, client_secret FROM clients WHERE user_id = '{$GLOBALS["session"]["user_id"]}' LIMIT 1;")
            ->fetch();
        $GLOBALS["session"]["client_id"] = $new_client["client_id"];
        $GLOBALS["session"]["client_secret"] = $new_client["client_secret"];

        $expires = date("Y-m-d H:i:s", strtotime("+1 hours"));
        $new_token = $GLOBALS["pdo"]
            ->query("INSERT INTO tokens (access_token, client_id, user_id, expires, scope) VALUES ('{$this->randString()}', '{$GLOBALS["session"]["client_id"]}', '{$GLOBALS["session"]["user_id"]}', '$expires', '{$GLOBALS["session"]["scope"]}') RETURNING access_token, expires;")
            ->fetch();

        $GLOBALS["session"]["access_token"] = $new_token["access_token"];
        $GLOBALS["session"]["expires"] = strtotime($new_token["expires"]);
    }

    /**
     * Clean parameters after execute tests
     */
    protected function tearDown(): void
    {
        $GLOBALS["pdo"]
            ->query("DELETE FROM admins WHERE admin_id = '{$GLOBALS["session"]["user_id"]}';")
            ->execute();

        // Unset variables
        unset($GLOBALS["session"]);
        unset($GLOBALS["body"]);
        unset($this->response);
    }
}