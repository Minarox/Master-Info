<?php
declare(strict_types=1);

namespace app;

use BadRequest;
use Codes\ErrorCode;
use NotFound;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * Check if user is connected and authorized to use the app before execute functions
 *
 * @param Request $request
 * @param RequestHandler $handler
 * @return Response
 * @throws NotFound|BadRequest
 */
return function (Request $request, RequestHandler $handler): Response {
    if (str_ends_with($request->getRequestTarget(), "/api/") || str_ends_with($request->getRequestTarget(), "/api")) {
        return $handler->handle($request);
    }

    // Check if request is for a require logged function
    if (!str_starts_with($request->getRequestTarget(), "/api/login")) {
        $error = new ErrorCode();
        if (empty($request->getHeader("Authorization"))) return $error->unauthorized();

        $token = explode(' ', ($request->getHeader("Authorization"))[0]);
        $database = new Database();

        $user = $database->find(
            "Users",
            ['*'],
            ["username" => $token[0], "token" => $token[1]],
            true,
            exception: false
        );

        if (!$user) return $error->unauthorized();
        if ($user["expire"] < date("Y-m-d H:i:s")) {
            $database->update(
                "Users",
                ["token" => "null", "expire" => "null"],
                ["id" => $user["id"]]
            );

            return $error->unauthorized();
        }

        $GLOBALS["user"] = $user;
    }

    // Return request (continue the execution)
    return $handler->handle($request);
};