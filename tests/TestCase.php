<?php
declare(strict_types = 1);

require_once __DIR__ . "/../app/Loader.php";

use PHPUnit\Framework\TestCase as PHPUnit_TestCase;

/**
 * Tests case for app
 */
class TestCase extends PHPUnit_TestCase
{
    /**
     * SetUp parameters before execute tests
     */
    protected function setUp(): void {}

    /**
     * Clean parameters after execute tests
     */
    protected function tearDown(): void {}
}