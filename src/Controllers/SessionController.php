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
     * Usage: POST /oauth2/token
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
        // Simplify password grant_type
        if ($GLOBALS["body"]["grant_type"] == "password"
            && (array_key_exists("email", $GLOBALS["body"])
            || array_key_exists("username", $GLOBALS["body"]))) {

            // Fetch client_id and client_secret
            $email = (array_key_exists("email", $GLOBALS["body"]))
                ? $GLOBALS["body"]["email"] : $GLOBALS["body"]["username"];

            $client = $this->database()->find(
                "clients",
                [
                    "client_id",
                    "client_secret"
                ],
                ["user_id" => $email],
                true
            );

            // Add client_id and client_secret to request
            $GLOBALS["body"]["username"] = $email;
            $GLOBALS["body"]["client_id"] = $client["client_id"];
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