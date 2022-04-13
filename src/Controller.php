<?php
declare(strict_types = 1);

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
    private SuccessCode $successCode;

    /**
     * @var ErrorCode
     */
    private ErrorCode $errorCode;

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
        $this->successCode = new SuccessCode();
        $this->errorCode   = new ErrorCode();
        $this->database    = new Database();
        $this->date        = date("Y-m-d H:i:s");
    }

    /**
     * Getter for SuccessCode object
     *
     * @return SuccessCode
     */
    protected function successCode(): SuccessCode
    {
        return $this->successCode;
    }

    /**
     * Getter for ErrorCode object
     *
     * @return ErrorCode
     */
    protected function errorCode(): ErrorCode
    {
        return $this->errorCode;
    }

    /**
     * Getter for Database object
     *
     * @return Database
     */
    public function database(): Database
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
     * Check scope of current session
     *
     * @param array   $scopes         Scopes to check
     * @param boolean $throwException Raise exception
     *
     * @throws Forbidden if user doesn't have the permissions
     */
    protected function checkScope(array $scopes = [], bool $throwException = true): bool
    {
        // Check if scope is in current session
        $scopes[] = "super_admin";
        foreach ($scopes as $scope) {
            if (in_array($scope, $GLOBALS["session"]["scope"])) {
                return true;
            }
        }

        // Return error if not found
        if ($throwException) {
            throw new Forbidden("User doesn't have the permission");
        }
        return false;
    }

    /**
     * Check if value exist in database
     *
     * @param string      $value         Value to check
     * @param array       $args          Array to search inside
     * @param string|null $table         Table to check
     * @param bool        $strict        Raise exception
     * @param string      $column        Column to search
     *
     * @return bool true if found, false otherwise
     * @throws BadRequest if request contain errors
     * @throws NotFound if value not found
     */
    protected function checkExist(string $value, array $args, string $table = null, bool $strict = false, string $column = "id"): bool
    {
        // Check if key exist in array
        if (array_key_exists($value, $args)) {
            if (!$table) {
                return true;
            }

            // Create array with correct values
            $fields = array($column => $args[$value]);

            // Check if value exist (throw NotFound exception automatically if not) and return true
            return (bool) $this->database->find(
                $table,
                [$column],
                $fields,
                true,
                null,
                $strict
            );
        }

        // Return false or throw BadRequest exception if it doesn't exist
        if ($strict) {
            throw new BadRequest("Missing value in array");
        }
        return false;
    }
}