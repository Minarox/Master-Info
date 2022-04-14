<?php
declare(strict_types = 1);

namespace Controllers;

use app\OAuth2;
use BadRequest;
use Controller;
use NotFound;
use OAuth2\Request as OAuth_Request;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * Controller for Oauth2 authentication functions
 * example of doc: https://developer.okta.com/docs/reference/api/oidc
 */
class SessionController extends Controller
{
    /**
     * Return new access token
     * Usage: POST /login | Scope: none
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     */
    public function login(Request $request, Response $response): Response
    {
        $this->checkExist("grant_type", $GLOBALS["body"], null, true);

        // Simplify password grant_type
        if ($GLOBALS["body"]["grant_type"] == "password"
            && (array_key_exists("email", $GLOBALS["body"])
            || array_key_exists("username", $GLOBALS["body"]))) {

            // Fetch email from fields
            $email = (array_key_exists("email", $GLOBALS["body"]))
                ? $GLOBALS["body"]["email"] : $GLOBALS["body"]["username"];

            // Fetch admin_id
            $admin = $this->database()->find(
                "admins",
                ["admin_id"],
                ["email" => $email],
                true
            );

            // Fetch client_id and client_secret
            $client = $this->database()->find(
                "clients",
                [
                    "client_id",
                    "client_secret"
                ],
                ["user_id" => $admin["admin_id"]],
                true
            );

            // Add client_id and client_secret to request
            $GLOBALS["body"]["username"]      = $email;
            $GLOBALS["body"]["client_id"]     = $client["client_id"];
            $GLOBALS["body"]["client_secret"] = $client["client_secret"];
            unset($GLOBALS["body"]["email"]);
        }

        // Create new access_token
        $server        = (new OAuth2())->getServer();
        $token_request = $server->handleTokenRequest($this->createFromGlobals($GLOBALS["body"]));

        // Return error code if error in request
        if ($server->getResponse()->getParameter("error") !== null)
            return $this->errorCode()->customError(
                $server->getResponse()->getStatusCode(),
                $server->getResponse()->getParameter("error_description")
            );

        // Display response
        $response->getBody()->write(
            $token_request->getResponseBody()
        );
        return $response;
    }

    /**
     * Return information about specific access_token or refresh_token
     * Usage: POST /introspect | Scope: admin, super_admin, app
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     */
    public function introspect(Request $request, Response $response): Response
    {
        if (array_key_exists("token", $GLOBALS["body"])) {
            // Find access_token in database
            $expires = $this->database()->find(
                "tokens",
                ["expires"],
                [
                    "access_token" => $GLOBALS["body"]["token"],
                    "client_id"    => $GLOBALS["session"]["client_id"],
                    "user_id"      => $GLOBALS["session"]["user_id"]
                ],
                true
            );
        } else if (array_key_exists("refresh_token", $GLOBALS["body"])) {
            // Find refresh_token in database
            $expires = $this->database()->find(
                "refresh_tokens",
                ["expires"],
                [
                    "refresh_token" => $GLOBALS["body"]["refresh_token"],
                    "client_id"     => $GLOBALS["session"]["client_id"],
                    'user_id'       => $GLOBALS["session"]["user_id"]
                ],
                true
            );
        } else {
            return $this->errorCode()->badRequest();
        }

        // Determinate if token or refresh_token is active or not
        $expires = implode($expires);
        if ($expires > $this->getDate()) {
            $active = true;
        } else {
            $active = false;
        }

        // Display token or refresh_token information
        $response->getBody()->write(
            json_encode(
                [
                    "active"  => $active,
                    "expires" => strtotime($expires)
                ]
            )
        );
        return $response->withStatus(200);
    }

    /**
     * Revoke specific access_token or refresh_token
     * Usage: POST /revoke | Scope: admin, super_admin, app
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     */
    public function revoke(Request $request, Response $response): Response
    {
        $token = false;

        if (array_key_exists("token", $GLOBALS["body"])) {
            // Find access_token in database
            $expires = $this->database()->find(
                "tokens",
                ["expires"],
                [
                    "access_token" => $GLOBALS["body"]["token"],
                    "client_id"    => $GLOBALS["session"]["client_id"],
                    "user_id"      => $GLOBALS["session"]["user_id"]
                ],
                true
            );

            $token = true;
        } else if (array_key_exists("refresh_token", $GLOBALS["body"])) {
            // Find refresh_token in database
            $expires = $this->database()->find(
                "refresh_tokens",
                ["expires"],
                [
                    "refresh_token" => $GLOBALS["body"]["refresh_token"],
                    "client_id"     => $GLOBALS["session"]["client_id"],
                    "user_id"       => $GLOBALS["session"]["user_id"]
                ],
                true
            );
        } else {
            return $this->errorCode()->badRequest();
        }

        // Determinate if active or not
        $expires = implode($expires);

        if ($expires > $this->getDate()) {
            if ($token) {
                // Change expires date to current date to invalidate the access_token
                $this->database()->update(
                    "tokens",
                    ["expires" => $this->getDate()],
                    [
                        "access_token" => $GLOBALS["body"]["token"],
                        "client_id"    => $GLOBALS["session"]["client_id"],
                        "user_id"      => $GLOBALS["session"]["user_id"]
                    ]
                );
            } else {
                // Change expires date to current date to invalidate the refresh_token
                $this->database()->update(
                    "refresh_tokens",
                    ["expires" => $this->getDate()],
                    [
                        "refresh_token" => $GLOBALS["body"]["refresh_token"],
                        "client_id"     => $GLOBALS["session"]["client_id"],
                        "user_id"       => $GLOBALS["session"]["user_id"]
                    ]
                );
            }

            // Display success code
            return $this->successCode()->success();
        } else {
            if (array_key_exists("token", $GLOBALS["body"])) {
                // Display error code if access_token is already invalid
                return $this->errorCode()->conflict("Access_token is already revoked");
            } else {
                // Display error code if refresh_token is already invalid
                return $this->errorCode()->conflict("Refresh_token is already revoked");
            }
        }
    }

    /**
     * Information about the user
     * Usage: GET /userinfo | Scope: admin, super_admin, app
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     */
    public function userInfo(Request $request, Response $response): Response
    {
        // Display current user information
        $response->getBody()->write(
            json_encode(
                $this->database()->find(
                    "admins",
                    [
                        "email",
                        "first_name",
                        "last_name"
                    ],
                    ["admin_id" => $GLOBALS["session"]["user_id"]],
                    true
                )
            )
        );
        return $response->withStatus(200);
    }

    /**
     * Update information about the user
     * Usage: PUT /userinfo | Scope: admin, super_admin, app
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     */
    public function editUserInfo(Request $request, Response $response): Response
    {
        // Fetch values from request
        $fields = array(
            "email"      => $GLOBALS["body"]["email"] ?? '',
            "first_name" => $GLOBALS["body"]["first_name"] ?? '',
            "last_name"  => $GLOBALS["body"]["last_name"] ?? ''
        );

        // Update user information
        $this->database()->update(
            "admins",
            $fields,
            ["admin_id" => $GLOBALS["session"]["user_id"]]
        );

        // Display success code
        return $this->successCode()->success();
    }

    /**
     * Change password of the user
     * Usage: PUT /userinfo/password | Scope: admin, super_admin, app
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     */
    public function editPassword(Request $request, Response $response): Response
    {
        // Check if values exist in request
        $this->checkExist("old_password", $GLOBALS["body"], null, true);
        $this->checkExist("new_password", $GLOBALS["body"], null, true);
        $this->checkExist("confirm_new_password", $GLOBALS["body"], null, true);

        // Check if new passwords are the same
        if ($GLOBALS["body"]["new_password"] !== $GLOBALS["body"]["confirm_new_password"]) {
            return $this->errorCode()->conflict("New passwords doesn't match");
        }

        // Check old password
        $old_password = ($this->database()->find(
            "admins",
            ["password"],
            ["admin_id" => $GLOBALS["session"]["user_id"]],
            true
        ))["password"];

        if (!password_verify($GLOBALS["body"]["old_password"], $old_password)) {
            return $this->errorCode()->conflict("Old password doesn't match");
        }

        // Update user password
        $this->database()->update(
            "admins",
            ["password" => password_hash($GLOBALS["body"]["new_password"], PASSWORD_BCRYPT)],
            ["admin_id" => $GLOBALS["session"]["user_id"]]
        );

        // Invalidate current token
        return $this->logout($request, $response);
    }

    /**
     * Revoke current token
     * Usage: GET /logout | Scope: admin, super_admin, app
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     */
    public function logout(Request $request, Response $response): Response
    {
        // Update expires to current date to invalidate the token
        $this->database()->update(
            "tokens",
            ["expires" => $this->getDate()],
            [
                "access_token" => $GLOBALS["session"]["access_token"],
                "client_id"    => $GLOBALS["session"]["client_id"],
                "user_id"      => $GLOBALS["session"]["user_id"]
            ]
        );
        return $this->successCode()->success();
    }

    /**
     * Creates a new request with custom body
     *
     * @param array $customBody
     *
     * @return OAuth_Request
     */
    private function createFromGlobals(array $customBody): OAuth_Request
    {
        $request = new OAuth_Request($_GET, $customBody, array(), $_COOKIE, $_FILES, $_SERVER);

        $contentType   = $request->server("CONTENT_TYPE", '');
        $requestMethod = $request->server("REQUEST_METHOD", "GET");
        if (0 === strpos($contentType, "application/x-www-form-urlencoded")
            && in_array(strtoupper($requestMethod), array("PUT", "DELETE"))) {
            parse_str($request->getContent(), $request->request);
        } else if (0 === strpos($contentType, "application/json")
                   && in_array(strtoupper($requestMethod), array("POST", "PUT", "DELETE"))) {
            $request->request = $customBody;
        }

        return $request;
    }
}