<?php
declare(strict_types=1);

namespace TestCase\Codes;

require_once __DIR__ . "/../../TestCase.php";

use Codes\SuccessCode;
use TestCase;

/**
 * Test class for SuccessCode
 */
class SuccessCodeTest extends TestCase
{
    /**
     * @var SuccessCode
     */
    private SuccessCode $successCode;

    /**
     * Test Success function in "Response" mode
     */
    public function testSuccessResponse()
    {
        $result = $this->successCode->success();

        self::assertSame(
            json_encode([
                "code_value" => 200,
                "code_description" => "Success"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test Success function in "Response" mode with custom description
     */
    public function testSuccessResponseCustom()
    {
        $result = $this->successCode->success("custom");

        self::assertSame(
            json_encode([
                "code_value" => 200,
                "code_description" => "custom"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test Success function in "string" mode
     */
    public function testSuccessString()
    {
        $result = $this->successCode->success("custom", false);

        self::assertSame(
            json_encode([
                "code_value" => 200,
                "code_description" => "custom"
            ]),
            $result
        );
    }

    /**
     * Test Created function in "Response" mode
     */
    public function testCreatedResponse()
    {
        $result = $this->successCode->created();

        self::assertSame(
            json_encode([
                "code_value" => 201,
                "code_description" => "Created"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(201, $result->getStatusCode());
    }

    /**
     * Test Created function in "Response" mode with custom description
     */
    public function testCreatedResponseCustom()
    {
        $result = $this->successCode->created("custom");

        self::assertSame(
            json_encode([
                "code_value" => 201,
                "code_description" => "custom"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(201, $result->getStatusCode());
    }

    /**
     * Test Created function in "string" mode
     */
    public function testCreatedString()
    {
        $result = $this->successCode->created("custom", false);

        self::assertSame(
            json_encode([
                "code_value" => 201,
                "code_description" => "custom"
            ]),
            $result
        );
    }

    /**
     * SetUp parameters before execute tests
     */
    protected function setUp(): void
    {
        $this->successCode = new SuccessCode();
    }

    /**
     * Clean parameters after execute tests
     */
    protected function tearDown(): void
    {
        unset($this->successCode);
    }
}
