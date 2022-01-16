<?php
declare(strict_types=1);

namespace Controllers;

use Controller;
use BadRequest;
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

        $token = $this->randString(60);
        if ($this->checkExist("username", $body, "Users", "username", false)) {
            $this->database()->update(
                "Users",
                [
                    "token" => $token,
                    "expire" => date(
                        "Y-m-d H:i:s",
                        strtotime($this->getDate()) + CONFIG["session"]["token_lifetime"]
                    )
                ],
                ["username" => $body["username"]]
            );
        } else {
            $nb_users = $this->database()->find(
                "Users",
                ["count(id) as count"],
                ['*'],
                true
            );

            if ($nb_users["count"] > CONFIG["session"]["maxUsers"])
                return $this->errorCode()->conflict("Maximum number of users reached");

            $this->database()->create(
                "Users",
                [
                    "username" => $body["username"],
                    "token" => $token,
                    "expire" => date(
                        "Y-m-d H:i:s",
                        strtotime($this->getDate()) + CONFIG["session"]["token_lifetime"]
                    )
                ]
            );
        }

        $response->getBody()->write(
            json_encode(
                $this->database()->find(
                    "Users",
                    ['*'],
                    ["username" => $body["username"]],
                    true
                )
            )
        );
        return $response->withStatus(200);
    }

    /**
     * Disconnect user from the app
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
        if (empty($request->getHeader("Authorization"))) return $this->errorCode()->unauthorized();
        $token = explode(' ', ($request->getHeader("Authorization"))[0]);

        $response->getBody()->write(
            json_encode(
                $this->database()->find(
                    "Users",
                    ['*'],
                    ["username" => $token[0], "token" => $token[1]],
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
        if (empty($request->getHeader("Authorization"))) return $this->errorCode()->unauthorized();
        $token = explode(' ', ($request->getHeader("Authorization"))[0]);

        $this->database()->update(
            "Users",
            ["token" => "null", "expire" => "null"],
            ["username" => $token[0], "token" => $token[1]]
        );
        return $this->successCode()->success();
    }
}