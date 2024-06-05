<?php
declare(strict_types = 1);

# Load autoloader
require_once __DIR__ . "/../app/Loader.php";

# Show PHP errors
if (CONFIG["debug"]["displayPHPErrors"]) {
    ini_set("display_errors", '1');
    ini_set("display_startup_errors", '1');
    error_reporting(E_ALL);
} else {
    ini_set("display_errors", '0');
    ini_set("display_startup_errors", '0');
    error_reporting(0);
}

use Codes\ErrorCode;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;

# Create new app
$app = AppFactory::create();

# Handle errors
($app->addErrorMiddleware(
    (bool) CONFIG["debug"]["displayErrorDetails"],
    (bool) CONFIG["debug"]["logError"],
    (bool) CONFIG["debug"]["logErrorDetails"])
)
    ->setErrorHandler(NotFound::class, function() {
        return (new ErrorCode)->notFound();
    })
    ->setErrorHandler(HttpNotFoundException::class, function() {
        return (new ErrorCode)->badRequest();
    })
    ->setErrorHandler(Unauthorized::class, function() {
        return (new ErrorCode)->unauthorized();
    })
    ->setErrorHandler(Forbidden::class, function() {
        return (new ErrorCode)->forbidden();
    })
    ->setErrorHandler(BadRequest::class, function() {
        return (new ErrorCode)->badRequest();
    })
    ->setErrorHandler(TypeError::class, function() {
        return (new ErrorCode)->badRequest();
    })
    ->setErrorHandler(PDOException::class, function() {
        return (new ErrorCode)->badRequest();
    })
    ->setErrorHandler(HttpMethodNotAllowedException::class, function() {
        return (new ErrorCode)->methodNotAllowed();
    });

# Change base path
# $app->setBasePath('/api');

# Add middleware for session and CORS
$app->add(require_once __DIR__ . "/../app/middlewares/CORS.php");
$app->addRoutingMiddleware();

# Add routes to app
$routes = require_once __DIR__ . "/../app/Router.php";
$routes($app);

# Run the app
$app->run();