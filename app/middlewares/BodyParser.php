<?php
declare(strict_types=1);

namespace app;

use BadRequest;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * Check if user is connected and authorized to use the app before execute functions
 *
 * @param Request $request
 * @param RequestHandler $handler
 * @return Response
 * @throws BadRequest if error during parsing
 */
return function (Request $request, RequestHandler $handler): Response {
    // Check if method match
    if ($request->getMethod() == "POST" || $request->getMethod() == "PUT") {
        // Get params from body and content-type
        $body = $request->getBody()->__toString();
        $content_type = $request->getHeader("Content-Type");
        if (empty($body) || empty($content_type))
            throw new BadRequest("Body is empty");

        // Parse body according to content-type
        if (str_starts_with($content_type[0], "application/x-www-form-urlencoded")) {
            parse_str($body, $GLOBALS["body"]);
        } elseif (str_starts_with($content_type[0], "application/json")) {
            $GLOBALS["body"] = json_decode($body, true);
            if (!is_array($GLOBALS["body"]))
                throw new BadRequest("JSON malformed");
        } elseif (str_starts_with($content_type[0], "application/xml") || str_starts_with($content_type[0], "text/xml")) {
            $GLOBALS["body"] = (array) simplexml_load_string($body);
        } else {
            throw new BadRequest("Unable to parse body");
        }
    }

    // Return request (continue the execution)
    return $handler->handle($request);
};