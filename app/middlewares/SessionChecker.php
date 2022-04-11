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
    if (!str_starts_with($request->getRequestTarget(), "/v1/oauth2/token")
        && !str_starts_with($request->getRequestTarget(), "/v1/users/forgotpassword")
        && !str_starts_with($request->getRequestTarget(), "/v1/users/setpassword")) {

        // Import classes
        $server     = (new Auth())->getServer();
        $error_code = new ErrorCode();

        // Check if user is connected
        if (!$server->verifyResourceRequest(OAuth_Request::createFromGlobals())) {
            if ($server->getResponse()->getParameter("error_description")) {
                return $error_code->customError(
                    $server->getResponse()->getStatusCode(),
                    $server->getResponse()->getParameter("error_description"));
            }
            return $error_code->unauthorized();
        }

        // Get session information
        $GLOBALS["session"]              = $session = $server->getAccessTokenData(OAuth_Request::createFromGlobals());
        $GLOBALS["session"]["user_id"]   = $session["client_id"]; // Because user_id is the client_id column in the database
        $GLOBALS["session"]["client_id"] = $session["user_id"];   // Because client_id is the user_id column in the database
        $GLOBALS["session"]["scope"]     = $session["scope"];
    }

    // Return request (continue the execution)
    return $handler->handle($request);
};