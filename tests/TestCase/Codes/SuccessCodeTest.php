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
     * Test Success function
     */
    public function testSuccess()
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

        $result = $this->successCode->success("custom");
        self::assertSame(
            json_encode([
                "code_value" => 200,
                "code_description" => "custom"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());

        $result = $this->successCode->success("Success", false);
        self::assertSame(
            json_encode([
                "code_value" => 200,
                "code_description" => "Success"
            ]),
            $result
        );

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
     * Test Created function
     */
    public function testCreated()
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

        $result = $this->successCode->created("custom");
        self::assertSame(
            json_encode([
                "code_value" => 201,
                "code_description" => "custom"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(201, $result->getStatusCode());

        $result = $this->successCode->created("Created", false);
        self::assertSame(
            json_encode([
                "code_value" => 201,
                "code_description" => "Created"
            ]),
            $result
        );

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
