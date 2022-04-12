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
     * @param array  $body
     *
     * @return Request
     */
    protected function createRequest(string $method, string $path, array $body = []): Request
    {
        $uri    = new Uri("http", "localhost", 8899, "/v1" . $path);
        $stream = (new StreamFactory())->createStream(json_encode($body));

        $headers = new Headers();
        $headers->addHeader("Content-type", "application/json");
        $headers->addHeader("Cache-control", "no-cache");
        if (!empty($this->session["token"])) {
            $headers->addHeader("Authorization", "Bearer " . $this->session["token"]);
        }

        return new Request($method, $uri, $headers, [], $_SERVER, $stream);
    }

    /**
     * SetUp parameters before execute tests
     */
    protected function setUp(): void
    {
        $this->response = new Response();

        // TODO: Create fake admin account
    }

    /**
     * Clean parameters after execute tests
     */
    protected function tearDown(): void
    {
        // TODO: Remove fake admin account

        // Unset variables
        unset($GLOBALS["session"]);
        unset($GLOBALS["body"]);
        unset($this->response);
    }
}