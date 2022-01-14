<?php
declare(strict_types=1);

use app\Database;
use Codes\ErrorCode;
use Codes\SuccessCode;

/**
 * Abstract class for Controllers
 */
abstract class Controller
{
    /**
     * @var SuccessCode
     */
    private SuccessCode $success;
    /**
     * @var ErrorCode
     */
    private ErrorCode $error;
    /**
     * @var Database
     */
    private Database $pdo;
    /**
     * @var string $date
     */
    private string $date;

    /**
     * Construct SuccessCode, ErrorCode and Database object to be used in Controllers
     */
    public function __construct()
    {
        $this->success = new SuccessCode();
        $this->error = new ErrorCode();
        $this->pdo = new Database();
        $this->date = date("Y-m-d H:i:s");
    }

    /**
     * Getter for SuccessCode object
     *
     * @return SuccessCode
     */
    protected function successCode(): SuccessCode
    {
        return $this->success;
    }

    /**
     * Getter for ErrorCode object
     *
     * @return ErrorCode
     */
    protected function errorCode(): ErrorCode
    {
        return $this->error;
    }

    /**
     * Getter for Database object
     *
     * @return Database
     */
    protected function pdo(): Database
    {
        return $this->pdo;
    }

    /**
     * Get current date
     *
     * @return string
     */
    protected function getDate(): string
    {
        return $this->date;
    }
}