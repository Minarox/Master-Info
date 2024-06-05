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
     * @var int $passage_id
     */
    protected int $passage_id;

    /**
     * @var int $food_id
     */
    protected int $food_id;

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
     * SetUp parameters before execute tests
     */
    protected function setUp(): void
    {
        $this->response = new Response();

        $this->passage_id = $GLOBALS["pdo"]
            ->query("INSERT INTO passage (passage_id) VALUES (NULL) RETURNING passage_id;")
            ->fetchColumn();

        $this->food_id = $GLOBALS["pdo"]
            ->query("INSERT INTO food (food_id) VALUES (NULL) RETURNING food_id;")
            ->fetchColumn();
    }

    /**
     * Clean parameters after execute tests
     */
    protected function tearDown(): void
    {
        $GLOBALS["pdo"]
            ->query("DELETE FROM food WHERE food_id = '$this->food_id';")
            ->execute();
        $GLOBALS["pdo"]
            ->query("DELETE FROM passage WHERE passage_id = '$this->passage_id';")
            ->execute();

        // Unset variable
        unset($this->response);
    }
}