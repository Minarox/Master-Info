<?php
declare(strict_types=1);

namespace app;

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * Check if user is connected and authorized to use the app before execute functions
 *
 * @param Request $request
 * @param RequestHandler $handler
 * @return Response
 */
return function (Request $request, RequestHandler $handler): Response {
    // Check if request is for a require logged function
    /*if (!str_starts_with($request->getRequestTarget(), "/")) {

    }*/

    // Return request (continue the execution)
    return $handler->handle($request);
};