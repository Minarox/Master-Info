<?php
declare(strict_types=1);

use app\Database;
use Codes\ErrorCode;
use Codes\SuccessCode;
use Slim\Psr7\Request;

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
     * Parse request body into array
     *
     * @param Request $request
     * @return array
     * @throws BadRequest
     */
    public function parseBody(Request $request): array
    {
        $body = $request->getBody()->__toString();
        $content_type = $request->getHeader("Content-Type");
        if (empty($body) || empty($content_type)) throw new BadRequest("Body or Content type is empty");

        if (str_starts_with($content_type[0], "application/x-www-form-urlencoded")) {
            parse_str($body, $data);
            return $data;
        } elseif (str_starts_with($content_type[0], "application/json")) {
            $result = json_decode($body, true);
            if (!is_array($result)) throw new BadRequest("JSON malformed");
            return $result;
        } elseif (str_starts_with($content_type[0], "application/xml") || str_starts_with($content_type[0], "text/xml")) {
            return (array) simplexml_load_string($body);
        } else {
            throw new BadRequest("Unable to parse body");
        }
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
                exception: $strict
            );
            if ($find) return true;
        }

        // Return false or throw NotFound exception if it doesn't exist
        if ($strict && !$table) throw new BadRequest("Value doesn't exist in array");
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

    /**
     * Update config.ini file
     *
     * @param $section
     * @param $key
     * @param $value
     * @return void
     */
    function updateConfig($section, $key, $value) {
        $config_data = parse_ini_file(__DIR__ . "/../config.ini", true);
        $config_data[$section][$key] = $value;
        $new_content = '';
        foreach ($config_data as $section => $section_content) {
            $section_content = array_map(function($value, $key) {
                return "$key='$value'";
            }, array_values($section_content), array_keys($section_content));
            $section_content = implode("\n", $section_content);
            $new_content .= "[$section]\n$section_content\n";
        }
        file_put_contents(__DIR__ . "/../config.ini", $new_content);
        $GLOBALS["config"] = parse_ini_file(__DIR__ . "/../config.ini", true);
    }
}