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
     * Construct SuccessCode, ErrorCode and Database object to be used in Controllers
     */
    public function __construct()
    {
        $this->successCode = new SuccessCode();
        $this->errorCode   = new ErrorCode();
        $this->database    = new Database();
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
     * @param bool        $currentClient Add current client_id
     * @param string      $column        Column to search
     *
     * @return bool true if found, false otherwise
     * @throws BadRequest if request contain errors
     * @throws NotFound if value not found
     * @throws Forbidden if the user doesn't have permissions
     */
    protected function checkExist(string $value, array $args, string $table = null, bool $strict = false, bool $currentClient = true, string $column = "id"): bool
    {
        // Check if key exist in array
        if (array_key_exists($value, $args)) {
            if (!$table) {
                return true;
            }

            // Create array with correct values
            $fields = array($column => $args[$value]);
            if ($currentClient && !$this->checkScope([], false)) {
                $fields = array_merge($fields, array("client_id" => $GLOBALS["session"]["client_id"]));
            }

            // Check if value exist (throw NotFound exception automatically if not) and return true
            $this->database->find(
                $table,
                [$column],
                $fields,
                true
            );
            return true;
        }

        // Return false or throw BadRequest exception if it doesn't exist
        if ($strict) {
            throw new BadRequest("Missing value in array");
        }
        return false;
    }

    /**
     * Generate random string
     *
     * @param int $length of the generated string
     *
     * @return string
     */
    protected function randString(int $length = 16): string
    {
        // Password generator with custom length
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        return substr(str_shuffle($chars), 0, $length);
    }
}