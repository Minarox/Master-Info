<?php
declare(strict_types = 1);

use app\Database;
use Codes\SuccessCode;
use Enums\Action;
use Enums\Type;
use JetBrains\PhpStorm\Pure;

/**
 * Abstract class for Controllers
 */
abstract class Controller
{
    /**
     * Getter for SuccessCode object
     *
     * @return SuccessCode
     */
    #[Pure] protected function successCode(): SuccessCode
    {
        return new SuccessCode();
    }

    /**
     * Getter for Database object
     *
     * @return Database
     */
    public function database(): Database
    {
        return new Database();
    }
}