<?php
declare(strict_types = 1);

use app\Database;
use Codes\ErrorCode;
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
     * Getter for ErrorCode object
     *
     * @return ErrorCode
     */
    #[Pure] protected function errorCode(): ErrorCode
    {
        return new ErrorCode();
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

    /**
     * Get current date
     *
     * @return string
     */
    protected function getDate(): string
    {
        return date("Y-m-d H:i:s");
    }

    /**
     * Check scope of current session
     *
     * @param array   $scopes         Scopes to check
     * @param boolean $throwException Raise exception
     *
     * @throws Unauthorized if user doesn't have the permissions
     */
    public function checkScope(array $scopes = [], bool $throwException = true): bool
    {
        // Check if scope is in current session
        $scopes[] = "super_admin";
        foreach ($scopes as $scope) {
            if ($scope === $GLOBALS["session"]["scope"]) {
                return true;
            }
        }

        // Return error if not found
        if ($throwException) {
            throw new Unauthorized("User doesn't have the permission");
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
     *
     * @return bool true if found, false otherwise
     * @throws BadRequest if request contain errors
     * @throws NotFound if value not found
     */
    public function checkExist(string $value, array $args, string $table = null, bool $strict = false): bool
    {
        // Check if key exist in array
        if (array_key_exists($value, $args)) {
            if (!$table) {
                return true;
            }

            // Create array with correct values
            $fields = array($value => $args[$value]);

            // Check if value exist (throw NotFound exception automatically if not) and return true
            return (bool) $this->database()->find(
                $table,
                [$value],
                $fields,
                true,
                exception: $strict
            );
        }

        // Return false or throw BadRequest exception if it doesn't exist
        if ($strict) {
            throw new BadRequest("Missing value in array");
        }
        return false;
    }

    /**
     * Create new log
     *
     * @param Action     $action
     * @param string|int $target_id
     * @param Type       $target_type
     * @param string     $source_id
     * @param Type       $source_type
     *
     * @return void
     * @throws BadRequest if request contain errors
     * @throws NotFound if database return nothing
     */
    public function addLog(Action $action, string|int $target_id, Type $target_type, string $source_id = '', Type $source_type = Type::Admin)
    {
        // Default value is current user
        if (empty($source_id)) {
            $source_id = $GLOBALS["session"]["user_id"];
        }

        // Create new log
        $this->database()->create(
            "logs",
            [
                "source" => $this->getName($source_id, $source_type),
                "source_id" => $source_id,
                "source_type" => $source_type->name,
                "action" => $action->name,
                "target" => $this->getName($target_id, $target_type),
                "target_id" => $target_id,
                "target_type" => $target_type->name
            ]
        );
    }

    /**
     * Fetch full name for source or target field of Logs table
     *
     * @param string|int $id
     * @param Type       $type
     *
     * @return string
     * @throws BadRequest
     * @throws NotFound
     */
    private function getName(string|int $id, Type $type): string
    {
        switch ($type->name) {
            case "User":
            case "Admin":
                $name = $this->database()->find(
                    strtolower($type->name) . 's',
                    [
                        "first_name",
                        "last_name"
                    ],
                    [strtolower($type->name) . "_id" => $id],
                    true
                );

                return $name["first_name"] . ' ' . $name["last_name"];
            case "Email":
                return ($this->database()->find(
                    "emails",
                    ["title"],
                    ["email_id" => $id],
                    true
                ))["title"];
            default:
                return Type::App->name;
        }
    }
}