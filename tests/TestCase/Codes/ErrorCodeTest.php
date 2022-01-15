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
     * Test Custom error code function in "Response" mode
     */
    public function testCustomErrorResponse()
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
    }

    /**
     * Test Custom error code function in "string" mode
     */
    public function testCustomErrorString()
    {
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
     * Test Bad request function in "Response" mode
     */
    public function testBadRequestResponse()
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
    }

    /**
     * Test Bad request function in "Response" mode with custom description
     */
    public function testBadRequestResponseCustom()
    {
        $result = $this->errorCode->badRequest("custom");

        self::assertSame(
            json_encode([
                "code_value" => 400,
                "code_description" => "custom"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(400, $result->getStatusCode());
    }

    /**
     * Test Bad request function in "string" mode
     */
    public function testBadRequestString()
    {
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
     * Test Unauthorized function in "Response" mode
     */
    public function testUnauthorizedResponse()
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
    }

    /**
     * Test Unauthorized function in "Response" mode with custom description
     */
    public function testUnauthorizedResponseCustom()
    {
        $result = $this->errorCode->unauthorized("custom");

        self::assertSame(
            json_encode([
                "code_value" => 401,
                "code_description" => "custom"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(401, $result->getStatusCode());
    }

    /**
     * Test Unauthorized function in "Response" mode
     */
    public function testUnauthorizedString()
    {
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
     * Test Not found function in "Response" mode
     */
    public function testNotFoundResponse()
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
    }

    /**
     * Test Not found function in "Response" mode with custom description
     */
    public function testNotFoundResponseCustom()
    {
        $result = $this->errorCode->notFound("custom");

        self::assertSame(
            json_encode([
                "code_value" => 404,
                "code_description" => "custom"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(404, $result->getStatusCode());
    }

    /**
     * Test Not found function in "string" mode
     */
    public function testNotFoundString()
    {
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
     * Test Method not allowed function in "Response" mode
     */
    public function testMethodNotAllowedResponse()
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
    }

    /**
     * Test Method not allowed function in "Response" mode with custom description
     */
    public function testMethodNotAllowedResponseCustom()
    {
        $result = $this->errorCode->methodNotAllowed("custom");

        self::assertSame(
            json_encode([
                "code_value" => 405,
                "code_description" => "custom"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(405, $result->getStatusCode());
    }

    /**
     * Test Method not allowed function in "string" mode
     */
    public function testMethodNotAllowedString()
    {
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
     * Test Conflict function in "Response" mode
     */
    public function testConflictResponse()
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
    }

    /**
     * Test Conflict function in "Response" mode with custom description
     */
    public function testConflictResponseCustom()
    {
        $result = $this->errorCode->conflict("custom");

        self::assertSame(
            json_encode([
                "code_value" => 409,
                "code_description" => "custom"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(409, $result->getStatusCode());
    }

    /**
     * Test Conflict function in "string" mode
     */
    public function testConflictString()
    {
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
