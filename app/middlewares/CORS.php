<?php
declare(strict_types=1);

namespace app;

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

/**
 * Add CORS policy
 *
 * @param Request $request
 * @param RequestHandler $handler
 * @return Response
 */
return function (Request $request, RequestHandler $handler): Response {
    // Get methods and headers
    $routeContext = RouteContext::fromRequest($request);
    $routingResults = $routeContext->getRoutingResults();
    $methods = $routingResults->getAllowedMethods();
    $requestHeaders = $request->getHeaderLine("Access-Control-Request-Headers");

    // Add CORS to response
    $response = $handler->handle($request)
        ->withHeader("Access-Control-Allow-Origin", '*')
        ->withHeader("Access-Control-Allow-Methods", implode(',', $methods))
        ->withHeader("Access-Control-Allow-Headers", $requestHeaders)
        ->withHeader("Access-Control-Allow-Credentials", "true")
        ->withHeader("Content-Type", "application/json")
        ->withStatus($handler->handle($request)->getStatusCode());

    if ($request->getMethod() == "OPTIONS") return $response->withStatus(200);
    return $response;
};