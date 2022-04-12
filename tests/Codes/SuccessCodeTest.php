<?php
declare(strict_types = 1);

namespace Codes;

require_once __DIR__ . "/../TestCase.php";

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

        self::assertJson(
            json_encode([
                "code_value"       => 200,
                "code_description" => "Success"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test Success function with JSON response
     */
    public function testJSONSuccess()
    {
        $result = $this->successCode->success("Success", false);

        self::assertJson(
            json_encode([
                "code_value"       => 200,
                "code_description" => "Success"
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

        self::assertJson(
            json_encode([
                "code_value"       => 201,
                "code_description" => "Created"
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(201, $result->getStatusCode());
    }

    /**
     * Test Created function with JSON response
     */
    public function testJSONCreated()
    {
        $result = $this->successCode->created("Created", false);

        self::assertJson(
            json_encode([
                "code_value"       => 201,
                "code_description" => "Created"
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
