<?php
declare(strict_types=1);

namespace Controllers;

use BadRequest;
use Controller;
use Exception;
use NotFound;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * Controller for Actions table
 */
class SessionController extends Controller
{
    /**
     * Connect user to the app if exist in database
     *
     * Usage: POST /login
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     * @throws BadRequest if body is empty
     * @throws NotFound if nothing was found
     * @throws Exception
     */
    public function login(Request $request, Response $response): Response
    {
        $body = $this->parseBody($request);
        $this->checkExist("username", $body);
        $this->checkExist("password", $body);

        $token = $this->randString(60);
        if ($this->checkExist("username", $body, "Users", "username", false)) {
            $userPassword = ($this->database()->find(
                "Users",
                ["password"],
                ["username" => $body["username"]],
                true
            ))["password"];

            if (password_verify($body["password"], $userPassword)) {
                $this->database()->update(
                    "Users",
                    [
                        "token" => $token,
                        "expire" => date(
                            "Y-m-d H:i:s",
                            strtotime($this->getDate()) + $GLOBALS["config"]["session"]["token_lifetime"]
                        )
                    ],
                    ["username" => $body["username"]]
                );
            } else {
                return $this->errorCode()->unauthorized();
            }
        } else {
            $nb_users = $this->database()->find(
                "Users",
                ["count(id) as count"],
                ['*'],
                true
            );

            if ($nb_users["count"] > $GLOBALS["config"]["session"]["maxUsers"])
                return $this->errorCode()->conflict("Maximum number of users reached");

            $this->database()->create(
                "Users",
                [
                    "username" => $body["username"],
                    "password" => password_hash($body["password"], PASSWORD_BCRYPT),
                    "token" => $token,
                    "expire" => date(
                        "Y-m-d H:i:s",
                        strtotime($this->getDate()) + $GLOBALS["config"]["session"]["token_lifetime"]
                    )
                ]
            );
        }

        $response->getBody()->write(
            json_encode(
                $this->database()->find(
                    "Users",
                    ["id", "username", "is_admin", "group_id", "token", "expire", "created_at"],
                    ["username" => $body["username"]],
                    true
                )
            )
        );
        return $response->withStatus(200);
    }

    /**
     * Get current session information
     *
     * Usage: GET /session
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     * @throws NotFound|BadRequest if nothing was found in the database
     */
    public function currentSession(Request $request, Response $response): Response
    {
        $response->getBody()->write(
            json_encode(
                $this->database()->find(
                    "Users",
                    ["id", "username", "is_admin", "group_id", "token", "expire", "created_at"],
                    ["username" => $GLOBALS["user"]["username"], "token" => $GLOBALS["user"]["token"]],
                    true
                )
            )
        );
        return $response->withStatus(200);
    }

    /**
     * Disconnect user from the app
     *
     * Usage: GET /logout
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     * @throws BadRequest|NotFound if nothing was found in the database
     */
    public function logout(Request $request, Response $response): Response
    {
        $this->database()->update(
            "Users",
            ["token" => "null", "expire" => "null"],
            ["username" => $GLOBALS["user"]["username"], "token" => $GLOBALS["user"]["token"]]
        );
        return $this->successCode()->success();
    }
}