<?php
declare(strict_types = 1);

namespace app;

use app\OAuth2 as Auth;
use Codes\ErrorCode;
use OAuth2\Request as OAuth_Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * Check if user is connected and authorized to use the app before execute functions
 *
 * @param Request        $request
 * @param RequestHandler $handler
 *
 * @return Response
 */
return function (Request $request, RequestHandler $handler): Response {
        // Check if request is for a require logged function
    if ($request->getRequestTarget() !== "/api/" && $request->getRequestTarget() !== "/api/login") {
        // Import Auth server
        $server = (new Auth())->getServer();

        // Check if user is connected
        if (!$server->verifyResourceRequest(OAuth_Request::createFromGlobals())) {
            if ($server->getResponse()->getParameter("error_description")) {
                return (new ErrorCode())->customError(
                    $server->getResponse()->getStatusCode(),
                    $server->getResponse()->getParameter("error_description")
                );
            }
            return (new ErrorCode())->unauthorized();
        }

        // Get session information
        $GLOBALS["session"] = $server->getAccessTokenData(OAuth_Request::createFromGlobals());
    }

    // Return request (continue the execution)
    return $handler->handle($request);
};