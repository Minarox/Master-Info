<?php
declare(strict_types=1);

namespace TestCase\Codes;

require_once __DIR__ . "/../../TestCase.php";

use Codes\ErrorCode;
use TestCase;

/**
 * Test class for ErrorCode
 */
class ErrorCodeTest extends TestCase
{
    /**
     * @var ErrorCode
     */
    private ErrorCode $errorCode;

    /**
     * Test Custom error code function
     */
    public function testCustomError()
    {
        $result = $this->errorCode->customError(401, "Custom Error Code");
        self::assertSame(
            json_encode([
                "code_value" => 401,
                "code_description" => "Custom Error Code"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(401, $result->getStatusCode());

        $result = $this->errorCode->customError(402, "Custom Error", false);
        self::assertSame(
            json_encode([
                "code_value" => 402,
                "code_description" => "Custom Error"
            ]),
            $result
        );
    }

    /**
     * Test Bad request function
     */
    public function testBadRequest()
    {
        $result = $this->errorCode->badRequest();
        self::assertSame(
            json_encode([
                "code_value" => 400,
                "code_description" => "Bad Request"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(400, $result->getStatusCode());

        $result = $this->errorCode->badRequest("custom");
        self::assertSame(
            json_encode([
                "code_value" => 400,
                "code_description" => "custom"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(400, $result->getStatusCode());

        $result = $this->errorCode->badRequest("custom", false);
        self::assertSame(
            json_encode([
                "code_value" => 400,
                "code_description" => "custom"
            ]),
            $result
        );
    }

    /**
     * Test Unauthorized function
     */
    public function testUnauthorized()
    {
        $result = $this->errorCode->unauthorized();
        self::assertSame(
            json_encode([
                "code_value" => 401,
                "code_description" => "Unauthorized"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(401, $result->getStatusCode());

        $result = $this->errorCode->unauthorized("custom");
        self::assertSame(
            json_encode([
                "code_value" => 401,
                "code_description" => "custom"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(401, $result->getStatusCode());

        $result = $this->errorCode->unauthorized("custom", false);
        self::assertSame(
            json_encode([
                "code_value" => 401,
                "code_description" => "custom"
            ]),
            $result
        );
    }

    /**
     * Test Not found function
     */
    public function testNotFound()
    {
        $result = $this->errorCode->notFound();
        self::assertSame(
            json_encode([
                "code_value" => 404,
                "code_description" => "Not Found"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(404, $result->getStatusCode());

        $result = $this->errorCode->notFound("custom");
        self::assertSame(
            json_encode([
                "code_value" => 404,
                "code_description" => "custom"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(404, $result->getStatusCode());

        $result = $this->errorCode->notFound("custom", false);
        self::assertSame(
            json_encode([
                "code_value" => 404,
                "code_description" => "custom"
            ]),
            $result
        );
    }

    /**
     * Test Method not allowed function
     */
    public function testMethodNotAllowed()
    {
        $result = $this->errorCode->methodNotAllowed();
        self::assertSame(
            json_encode([
                "code_value" => 405,
                "code_description" => "Method Not Allowed"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(405, $result->getStatusCode());

        $result = $this->errorCode->methodNotAllowed("custom");
        self::assertSame(
            json_encode([
                "code_value" => 405,
                "code_description" => "custom"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(405, $result->getStatusCode());


        $result = $this->errorCode->methodNotAllowed("custom", false);
        self::assertSame(
            json_encode([
                "code_value" => 405,
                "code_description" => "custom"
            ]),
            $result
        );
    }

    /**
     * Test Conflict function
     */
    public function testConflict()
    {
        $result = $this->errorCode->conflict();
        self::assertSame(
            json_encode([
                "code_value" => 409,
                "code_description" => "Conflict"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(409, $result->getStatusCode());

        $result = $this->errorCode->conflict("custom");
        self::assertSame(
            json_encode([
                "code_value" => 409,
                "code_description" => "custom"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(409, $result->getStatusCode());

        $result = $this->errorCode->conflict("custom", false);
        self::assertSame(
            json_encode([
                "code_value" => 409,
                "code_description" => "custom"
            ]),
            $result
        );
    }

    /**
     * SetUp Parameters before execute tests
     */
    protected function setUp(): void
    {
        $this->errorCode = new ErrorCode();
    }

    /**
     * Clean Parameters after execute tests
     */
    protected function tearDown(): void
    {
        unset($this->errorCode);
    }
}
