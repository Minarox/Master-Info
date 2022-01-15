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
    private Database $database;

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
        $this->database = new Database();
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
    protected function database(): Database
    {
        return $this->database;
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

    /**
     * Check if body array is empty
     *
     * @param array $body parsedBody returned by the request
     * @return array
     * @throws BadRequest if body is empty
     */
    protected function isEmpty(array $body): array
    {
        if (!$body) throw new BadRequest("Body is empty");
        return $body;
    }


    /**
     * Check if value exist in array and in database
     *
     * @param string $value Value to check
     * @param array $args Array to search inside
     * @param string|null $table Table to check
     * @param string|null $column to search in
     * @param bool $strict Raise exception
     * @return bool
     * @throws BadRequest if request contain errors
     * @throws NotFound if value not found
     */
    protected function checkExist(string $value, array $args, string $table = null, string $column = null, bool $strict = true): bool
    {
        // Check if key exist in array
        if (array_key_exists($value, $args)) {
            if (!$table) return true;

            // Create array with correct values
            $fields = array($column => $args[$value]);

            // Check if value exist
            $find = $this->database->find(
                $table,
                [$column],
                $fields,
                true,
                null,
                $strict
            );
            if ($find) return true;
        }

        // Return false or throw NotFound exception if it doesn't exist
        if ($strict) throw new NotFound("Value doesn't exist in array");
        return false;
    }

    /**
     * Generate random string
     *
     * @param int $length of the generated string
     * @return string
     */
    protected function randString(int $length = 16): string
    {
        // Password generator with custom length
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        return substr(str_shuffle($chars), 0, $length);
    }
}