<?php
declare(strict_types=1);

require_once __DIR__ . "/../app/Loader.php";

use app\Database;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Psr7\Uri;

/**
 * Tests case for app
 */
class TestCase extends PHPUnit_TestCase
{
    /**
     * @var PDO
     */
    protected PDO $pdo;

    /**
     * @var Response
     */
    protected Response $response;

    /**
     * @var array
     */
    protected array $test_group;

    /**
     * @param string $method
     * @param string $path
     * @param array|string $body
     * @param string $contentType
     * @return Request
     */
    protected function createRequest(string $method, string $path, array|string $body = [], string $contentType = "application/json"): Request
    {
        $uri = new Uri('', '', 80, $path);
        if ($contentType == "application/json") {
            $stream = (new StreamFactory())->createStream(json_encode($body));
        } else {
            $stream = (new StreamFactory())->createStream($body);
        }

        $headers = new Headers();
        $headers->addHeader("Content-type", $contentType);
        $headers->addHeader("Cache-control", "no-cache");

        return new Request($method, $uri, $headers, [], [], $stream);
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

    /**
     * SetUp parameters before execute tests
     */
    protected function setUp(): void {
        $this->pdo = (new Database())->getPdo();
        $this->pdo->query("DELETE FROM Groups WHERE name = 'test_group_phpunit';")->execute();
        $this->pdo->query("DELETE FROM Users WHERE username = 'test_user_phpunit' AND is_admin = 1;")->execute();

        $date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")) + $GLOBALS["config"]["session"]["token_lifetime"]);
        $password = password_hash("test_password_phpunit", PASSWORD_BCRYPT);
        $test_user_id = $this->pdo->query("INSERT INTO Users (username, password, is_admin, token, expire) VALUES ('test_user_phpunit', '$password', 1, '{$this->randString(60)}', '$date') RETURNING id;")->fetchColumn();
        $this->test_group = $this->pdo->query("INSERT INTO Groups (name, admin, link) VALUES ('test_group_phpunit', '$test_user_id', 'phpunittestlink') RETURNING *;")->fetch();
        $this->pdo->query("UPDATE Users SET group_id = '{$this->test_group["id"]}' WHERE id = '$test_user_id';");
        $GLOBALS["user"] = $this->pdo->query("SELECT * FROM Users WHERE id = '$test_user_id' LIMIT 1;")->fetch();

        $this->response = new Response();
    }

    /**
     * Clean parameters after execute tests
     */
    protected function tearDown(): void {
        unset($this->response);

        $this->pdo->query("DELETE FROM Users WHERE id = '{$GLOBALS["user"]["id"]}';")->execute();
        $this->pdo->query("DELETE FROM Groups WHERE id = '{$this->test_group["id"]}';")->execute();

        unset($GLOBALS["user"]);
        unset($this->test_group);
        unset($this->pdo);
    }
}