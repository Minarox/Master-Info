<?php
declare(strict_types = 1);

namespace Codes;

require_once __DIR__ . "/../TestCase.php";

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
        $result = $this->errorCode->customError(410, "Custom Error Code");

        self::assertJson(
            json_encode([
                "code_value"       => 410,
                "code_description" => "Custom Error Code"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(410, $result->getStatusCode());
    }

    /**
     * Test Custom error code function with JSON response
     */
    public function testJSONCustomError()
    {
        $result = $this->errorCode->customError(410, "Custom Error Code", false);

        self::assertJson(
            json_encode([
                "code_value"       => 410,
                "code_description" => "Custom Error Code"
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

        self::assertJson(
            json_encode([
                "code_value"       => 400,
                "code_description" => "Bad Request"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(400, $result->getStatusCode());
    }

    /**
     * Test Bad request function with JSON response
     */
    public function testJSONBadRequest()
    {
        $result = $this->errorCode->badRequest("Bad Request", false);

        self::assertJson(
            json_encode([
                "code_value"       => 400,
                "code_description" => "Bad Request"
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

        self::assertJson(
            json_encode([
                "code_value"       => 401,
                "code_description" => "Unauthorized"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(401, $result->getStatusCode());
    }

    /**
     * Test Unauthorized function with JSON response
     */
    public function testJSONUnauthorized()
    {
        $result = $this->errorCode->unauthorized("Unauthorized", false);

        self::assertJson(
            json_encode([
                "code_value"       => 401,
                "code_description" => "Unauthorized"
            ]),
            $result
        );
    }

    /**
     * Test Forbidden function
     */
    public function testForbidden()
    {
        $result = $this->errorCode->forbidden();

        self::assertJson(
            json_encode([
                "code_value"       => 403,
                "code_description" => "Forbidden"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(403, $result->getStatusCode());
    }

    /**
     * Test Forbidden function with JSON response
     */
    public function testJSONForbidden()
    {
        $result = $this->errorCode->forbidden("Forbidden", false);

        self::assertJson(
            json_encode([
                "code_value"       => 403,
                "code_description" => "Forbidden"
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

        self::assertJson(
            json_encode([
                "code_value"       => 404,
                "code_description" => "Not Found"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(404, $result->getStatusCode());
    }

    /**
     * Test Not found function with JSON response
     */
    public function testJSONNotFound()
    {
        $result = $this->errorCode->notFound("Not Found", false);

        self::assertJson(
            json_encode([
                "code_value"       => 404,
                "code_description" => "Not Found"
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

        self::assertJson(
            json_encode([
                "code_value"       => 405,
                "code_description" => "Method Not Allowed"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(405, $result->getStatusCode());
    }

    /**
     * Test Method not allowed function with JSON response
     */
    public function testJSONMethodNotAllowed()
    {
        $result = $this->errorCode->methodNotAllowed("Method Not Allowed", false);

        self::assertJson(
            json_encode([
                "code_value"       => 405,
                "code_description" => "Method Not Allowed"
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

        self::assertJson(
            json_encode([
                "ode_value"        => 409,
                "code_description" => "Conflict"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(409, $result->getStatusCode());
    }

    /**
     * Test Conflict function with JSON response
     */
    public function testJSONConflict()
    {
        $result = $this->errorCode->conflict("Conflict", false);

        self::assertJson(
            json_encode([
                "ode_value"        => 409,
                "code_description" => "Conflict"
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
