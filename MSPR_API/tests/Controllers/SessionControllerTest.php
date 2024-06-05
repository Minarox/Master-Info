<?php
declare(strict_types = 1);

namespace Controllers;

require_once __DIR__ . "/../TestCase.php";

use BadRequest;
use NotFound;
use TestCase;

/**
 * Test class for SessionController
 */
class SessionControllerTest extends TestCase
{
    /**
     * @var SessionController $sessionController
     */
    private SessionController $sessionController;

    /**
     * Construct SessionController for tests
     *
     * @param string|null $name
     * @param array       $data
     * @param string      $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->sessionController = new SessionController();
        $GLOBALS["pdo"]          = $this->sessionController->database()->getPdo();
    }

    /**
     * Test login function without params
     * Usage: POST /login | Scope: none
     *
     * @throws NotFound|BadRequest
     */
    public function testLoginWithoutParams()
    {
        // Check if exception is thrown
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage("Missing value in array");

        // Call function
        $request = $this->createRequest("POST", "/actions");
        $this->sessionController->login($request, $this->response);
    }

    /**
     * Test login function with password
     * Usage: POST /login | Scope: none
     *
     * @throws NotFound|BadRequest
     */
    public function testLoginWithEmail()
    {
        // Fields
        $GLOBALS["body"] = [
            "grant_type" => "password",
            "email"      => $GLOBALS["session"]["user_email"],
            "password"   => "test!123"
        ];

        // Remove old access and refresh token from database
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM tokens WHERE client_id = '{$GLOBALS["session"]["client_id"]}';")
            ->execute();
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM refresh_tokens WHERE client_id = '{$GLOBALS["session"]["client_id"]}';")
            ->execute();

        // Call function
        $request = $this->createRequest("POST", "/login");
        $result = $this->sessionController->login($request, $this->response);

        // Fetch access and refresh token from database
        $access_token = $GLOBALS["pdo"]
            ->query("SELECT access_token FROM tokens WHERE user_id = '{$GLOBALS["session"]["user_id"]}' ORDER BY expires DESC LIMIT 1;")
            ->fetchColumn();
        $refresh_token = $GLOBALS["pdo"]
            ->query("SELECT refresh_token FROM refresh_tokens WHERE user_id = '{$GLOBALS["session"]["user_id"]}' ORDER BY expires DESC LIMIT 1;")
            ->fetchColumn();

        // Remove new access and refresh token from database
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM refresh_tokens WHERE refresh_token = '$refresh_token';")
            ->execute();
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM tokens WHERE access_token = '$access_token';")
            ->execute();

        // Check if request = database
        self::assertSame(
            json_encode([
                "access_token" => $access_token,
                "expires_in" => (int) CONFIG["oauth2"]["access_lifetime"],
                "token_type" => CONFIG["oauth2"]["token_bearer_header_name"],
                "scope" => "super_admin",
                "refresh_token" => $refresh_token
            ]),
            $result->getBody()->__toString()
        );
    }

    /**
     * Test login function with bad password
     * Usage: POST /login | Scope: none
     *
     * @throws NotFound|BadRequest
     */
    public function testLoginWithBadEmail()
    {
        // Fields
        $GLOBALS["body"] = [
            "grant_type" => "password",
            "email"      => "tests@example.com",
            "password"   => "test!123"
        ];

        // Call function
        $request = $this->createRequest("POST", "/login");
        $result = $this->sessionController->login($request, $this->response);

        // Check if http code is correct
        $this->assertHTTPCode($result, 401, "Invalid username and password combination");
    }

    /**
     * Test login function with bad password
     * Usage: POST /login | Scope: none
     *
     * @throws NotFound|BadRequest
     */
    public function testLoginWithBadPassword()
    {
        // Fields
        $GLOBALS["body"] = [
            "grant_type" => "password",
            "email"      => $GLOBALS["session"]["user_email"],
            "password"   => "test!1234"
        ];

        // Call function
        $request = $this->createRequest("POST", "/login");
        $result = $this->sessionController->login($request, $this->response);

        // Check if http code is correct
        $this->assertHTTPCode($result, 401, "Invalid username and password combination");
    }

    /**
     * Test login function with client
     * Usage: POST /login | Scope: none
     *
     * @throws NotFound|BadRequest
     */
    public function testLoginWithClient()
    {
        // Fields
        $GLOBALS["body"] = [
            "grant_type"    => "client_credentials",
            "client_id"     => $GLOBALS["session"]["client_id"],
            "client_secret" => $GLOBALS["session"]["client_secret"]
        ];

        // Remove old access token from database
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM tokens WHERE client_id = '{$GLOBALS["session"]["client_id"]}';")
            ->execute();

        // Call function
        $request = $this->createRequest("POST", "/login");
        $result = $this->sessionController->login($request, $this->response);

        // Fetch access token from database
        $access_token = $GLOBALS["pdo"]
            ->query("SELECT access_token FROM tokens WHERE client_id = '{$GLOBALS["session"]["client_id"]}' ORDER BY expires DESC LIMIT 1;")
            ->fetchColumn();

        // Remove new access token from database
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM tokens WHERE access_token = '$access_token';")
            ->execute();

        // Check if request = database
        self::assertSame(
            json_encode([
                "access_token" => $access_token,
                "expires_in" => (int) CONFIG["oauth2"]["access_lifetime"],
                "token_type" => CONFIG["oauth2"]["token_bearer_header_name"],
                "scope" => "super_admin"
            ]),
            $result->getBody()->__toString()
        );
    }

    /**
     * Test introspect function with token
     * Usage: POST /introspect | Scope: admin, super_admin, app
     *
     * @throws NotFound|BadRequest
     */
    public function testIntrospectWithToken()
    {
        $GLOBALS["body"] = [
            "token" => $GLOBALS["session"]["access_token"]
        ];

        // Call function
        $request = $this->createRequest("POST", "/introspect");
        $result = $this->sessionController->introspect($request, $this->response);

        // Check if request = database and http code is correct
        self::assertSame(
            json_encode([
                "active"  => true,
                "expires" => $GLOBALS["session"]["expires"]
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test introspect function with expired token
     * Usage: POST /introspect | Scope: admin, super_admin, app
     *
     * @throws NotFound|BadRequest
     */
    public function testIntrospectWithExpiredToken()
    {
        $current_date = date("Y-m-d H:i:s", strtotime("-1 hours"));
        $GLOBALS["pdo"]
            ->prepare("UPDATE tokens SET expires = '$current_date' WHERE access_token = '{$GLOBALS["session"]["access_token"]}';")
            ->execute();

        $GLOBALS["body"] = [
            "token" => $GLOBALS["session"]["access_token"]
        ];

        // Call function
        $request = $this->createRequest("POST", "/introspect");
        $result = $this->sessionController->introspect($request, $this->response);

        // Check if request = database and http code is correct
        self::assertSame(
            json_encode([
                "active"  => false,
                "expires" => strtotime($current_date)
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test introspect function with refresh token
     * Usage: POST /introspect | Scope: admin, super_admin, app
     *
     * @throws NotFound|BadRequest
     */
    public function testIntrospectWithRefreshToken()
    {
        $expires = date("Y-m-d H:i:s", strtotime("+1 hours"));
        $refresh_token = $GLOBALS["pdo"]
            ->query("INSERT INTO refresh_tokens (refresh_token, client_id, user_id, expires, scope) VALUES ('{$this->randString()}', '{$GLOBALS["session"]["client_id"]}', '{$GLOBALS["session"]["user_id"]}', '$expires', '{$GLOBALS["session"]["scope"]}') RETURNING refresh_token;")
            ->fetchColumn();

        $GLOBALS["body"] = [
            "refresh_token" => $refresh_token
        ];

        // Call function
        $request = $this->createRequest("POST", "/introspect");
        $result = $this->sessionController->introspect($request, $this->response);

        // Check if request = database and http code is correct
        self::assertSame(
            json_encode([
                "active"  => true,
                "expires" => strtotime($expires)
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());

        $GLOBALS["pdo"]
            ->prepare("DELETE FROM refresh_tokens WHERE refresh_token = '$refresh_token';")
            ->execute();
    }

    /**
     * Test introspect function without params
     * Usage: POST /introspect | Scope: admin, super_admin, app
     *
     * @throws NotFound|BadRequest
     */
    public function testIntrospectWithoutParams()
    {
        // Call function
        $request = $this->createRequest("POST", "/introspect");
        $result = $this->sessionController->introspect($request, $this->response);

        // Check http code is correct
        $this->assertHTTPCode($result, 400);
    }

    /**
     * Test revoke function with token
     * Usage: POST /revoke | Scope: admin, super_admin, app
     *
     * @throws NotFound|BadRequest
     */
    public function testRevokeWithToken()
    {
        $GLOBALS["body"] = [
            "token" => $GLOBALS["session"]["access_token"]
        ];

        // Call function
        $request = $this->createRequest("POST", "/revoke");
        $result = $this->sessionController->revoke($request, $this->response);

        // Check http code is correct
        self::assertNotFalse(
            $GLOBALS["pdo"]
                ->query("SELECT access_token FROM tokens WHERE access_token = '{$GLOBALS["session"]["access_token"]}' AND expires <= NOW() LIMIT 1;")
                ->fetchColumn()
        );
        $this->assertHTTPCode($result);
    }

    /**
     * Test revoke function with refresh token
     * Usage: POST /revoke | Scope: admin, super_admin, app
     *
     * @throws NotFound|BadRequest
     */
    public function testRevokeWithRefreshToken()
    {
        $expires = date("Y-m-d H:i:s", strtotime("+1 hours"));
        $refresh_token = $GLOBALS["pdo"]
            ->query("INSERT INTO refresh_tokens (refresh_token, client_id, user_id, expires, scope) VALUES ('{$this->randString()}', '{$GLOBALS["session"]["client_id"]}', '{$GLOBALS["session"]["user_id"]}', '$expires', '{$GLOBALS["session"]["scope"]}') RETURNING refresh_token;")
            ->fetchColumn();

        $GLOBALS["body"] = [
            "refresh_token" => $refresh_token
        ];

        // Call function
        $request = $this->createRequest("POST", "/revoke");
        $result = $this->sessionController->revoke($request, $this->response);

        // Check http code is correct
        self::assertNotFalse(
            $GLOBALS["pdo"]
                ->query("SELECT refresh_token FROM refresh_tokens WHERE refresh_token = '$refresh_token' AND expires <= NOW() LIMIT 1;")
                ->fetchColumn()
        );
        $this->assertHTTPCode($result);

        $GLOBALS["pdo"]
            ->prepare("DELETE FROM refresh_tokens WHERE refresh_token = '$refresh_token';")
            ->execute();
    }

    /**
     * Test revoke function without params
     * Usage: POST /revoke | Scope: admin, super_admin, app
     *
     * @throws NotFound|BadRequest
     */
    public function testRevokeWithoutParams()
    {
        // Call function
        $request = $this->createRequest("POST", "/revoke");
        $result = $this->sessionController->revoke($request, $this->response);

        // Check http code is correct
        $this->assertHTTPCode($result, 400);
    }

    /**
     * Test revoke function with expired token
     * Usage: POST /revoke | Scope: admin, super_admin, app
     *
     * @throws NotFound|BadRequest
     */
    public function testRevokeWithExpiredToken()
    {
        $current_date = date("Y-m-d H:i:s", strtotime("-1 hours"));
        $GLOBALS["pdo"]
            ->prepare("UPDATE tokens SET expires = '$current_date' WHERE access_token = '{$GLOBALS["session"]["access_token"]}';")
            ->execute();

        $GLOBALS["body"] = [
            "token" => $GLOBALS["session"]["access_token"]
        ];

        // Call function
        $request = $this->createRequest("POST", "/revoke");
        $result = $this->sessionController->revoke($request, $this->response);

        // Check http code is correct
        $this->assertHTTPCode($result, 409, "Access_token is already revoked");
    }

    /**
     * Test revoke function with expired refresh token
     * Usage: POST /revoke | Scope: admin, super_admin, app
     *
     * @throws NotFound|BadRequest
     */
    public function testRevokeWithExpiredRefreshToken()
    {
        $expires = date("Y-m-d H:i:s", strtotime("-1 hours"));
        $refresh_token = $GLOBALS["pdo"]
            ->query("INSERT INTO refresh_tokens (refresh_token, client_id, user_id, expires, scope) VALUES ('{$this->randString()}', '{$GLOBALS["session"]["client_id"]}', '{$GLOBALS["session"]["user_id"]}', '$expires', '{$GLOBALS["session"]["scope"]}') RETURNING refresh_token;")
            ->fetchColumn();

        $GLOBALS["body"] = [
            "refresh_token" => $refresh_token
        ];

        // Call function
        $request = $this->createRequest("POST", "/revoke");
        $result = $this->sessionController->revoke($request, $this->response);

        // Check http code is correct
        $this->assertHTTPCode($result, 409, "Refresh_token is already revoked");

        $GLOBALS["pdo"]
            ->prepare("DELETE FROM refresh_tokens WHERE refresh_token = '$refresh_token';")
            ->execute();
    }

    /**
     * Test userInfo function
     * Usage: GET /userinfo | Scope: admin, super_admin, app
     *
     * @throws NotFound|BadRequest
     */
    public function testUserInfo()
    {
        // Call function
        $request = $this->createRequest("GET", "/userinfo");
        $result = $this->sessionController->userInfo($request, $this->response);

        // Check if request = database and http code is correct
        self::assertSame(
            json_encode(
                $GLOBALS["pdo"]
                    ->query("SELECT email, first_name, last_name FROM admins WHERE admin_id = '{$GLOBALS["session"]["user_id"]}' LIMIT 1;")
                    ->fetch()
            ),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test editUserInfo function
     * Usage: PUT /userinfo | Scope: admin, super_admin, app
     *
     * @throws NotFound|BadRequest
     */
    public function testEditUserInfo()
    {
        // Fields
        $GLOBALS["body"] = [
            "first_name" => "Test2"
        ];

        // Call function
        $request = $this->createRequest("PUT", "/userinfo");
        $result = $this->sessionController->editUserInfo($request, $this->response);

        // Check if request = database and http code is correct
        $this->assertHTTPCode($result);
        self::assertSame(
            $GLOBALS["body"],
            $GLOBALS["pdo"]
                ->query("SELECT first_name FROM admins WHERE admin_id = '{$GLOBALS["session"]["user_id"]}' LIMIT 1;")
                ->fetch()
        );
    }

    /**
     * Test editPassword function
     * Usage: PUT /userinfo/password | Scope: admin, super_admin, app
     *
     * @throws NotFound|BadRequest
     */
    public function testEditPassword()
    {
        // Fields
        $GLOBALS["body"] = [
            "old_password"         => "test!123",
            "new_password"         => "test1234",
            "confirm_new_password" => "test1234",
        ];

        // Call function
        $request = $this->createRequest("PUT", "/userinfo/password");
        $result = $this->sessionController->editPassword($request, $this->response);

        // Check if request = database and http code is correct
        $this->assertHTTPCode($result);
        self::assertTrue(
            password_verify(
                "test1234",
                $GLOBALS["pdo"]
                    ->query("SELECT password FROM admins WHERE admin_id = '{$GLOBALS["session"]["user_id"]}' LIMIT 1;")
                    ->fetchColumn()
            )
        );
    }

    /**
     * Test editPassword function with bad new passwords
     * Usage: PUT /userinfo/password | Scope: admin, super_admin, app
     *
     * @throws NotFound|BadRequest
     */
    public function testEditPasswordWithBadNewPasswords()
    {
        // Fields
        $GLOBALS["body"] = [
            "old_password"         => "test!123",
            "new_password"         => "test1234",
            "confirm_new_password" => "test!123",
        ];

        // Call function
        $request = $this->createRequest("PUT", "/userinfo/password");
        $result = $this->sessionController->editPassword($request, $this->response);

        // Check if request = database and http code is correct
        $this->assertHTTPCode($result, 409, "New passwords doesn't match");
    }

    /**
     * Test editPassword function with bad old password
     * Usage: PUT /userinfo/password | Scope: admin, super_admin, app
     *
     * @throws NotFound|BadRequest
     */
    public function testEditPasswordWithBadOldPassword()
    {
        // Fields
        $GLOBALS["body"] = [
            "old_password"         => "test1234",
            "new_password"         => "test1234",
            "confirm_new_password" => "test1234",
        ];

        // Call function
        $request = $this->createRequest("PUT", "/userinfo/password");
        $result = $this->sessionController->editPassword($request, $this->response);

        // Check if request = database and http code is correct
        $this->assertHTTPCode($result, 409, "Old password doesn't match");
    }

    /**
     * Test logout function
     * Usage: GET /logout | Scope: admin, super_admin, app
     *
     * @throws NotFound|BadRequest
     */
    public function testLogout()
    {
        // Call function
        $request = $this->createRequest("GET", "/logout");
        $result = $this->sessionController->logout($request, $this->response);

        // Check if request = database and http code is correct
        self::assertNotFalse(
            $GLOBALS["pdo"]
                ->query("SELECT access_token FROM tokens WHERE access_token = '{$GLOBALS["session"]["access_token"]}' AND expires <= NOW() LIMIT 1;")
                ->fetchColumn()
        );
        $this->assertHTTPCode($result);
    }
}