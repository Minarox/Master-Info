<?php
declare(strict_types=1);

# Load autoloader
require_once __DIR__ . "/../app/Loader.php";

# Show PHP errors
if ($GLOBALS["config"]["debug"]["displayPHPErrors"]) {
    ini_set("display_errors", '1');
    ini_set("display_startup_errors", '1');
    error_reporting(E_ALL);
}

use Codes\ErrorCode;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;

# Create new app
$app = AppFactory::create();
$app->setBasePath("/api");

# Handle errors
($app->addErrorMiddleware((bool)$GLOBALS["config"]["debug"]["displayErrorDetails"], (bool)$GLOBALS["config"]["debug"]["logError"], (bool)$GLOBALS["config"]["debug"]["logErrorDetails"]))
    ->setErrorHandler(NotFound::class, function() { return (new ErrorCode)->notFound(); })
    ->setErrorHandler(HttpNotFoundException::class, function() { return (new ErrorCode)->badRequest(); })
    ->setErrorHandler(Unauthorized::class, function() { return (new ErrorCode)->unauthorized(); })
    ->setErrorHandler(BadRequest::class, function() { return (new ErrorCode)->badRequest(); })
    ->setErrorHandler(TypeError::class, function() { return (new ErrorCode)->badRequest(); })
    ->setErrorHandler(PDOException::class, function() { return (new ErrorCode)->badRequest(); })
    ->setErrorHandler(HttpMethodNotAllowedException::class, function() { return (new ErrorCode)->methodNotAllowed(); });

# Add middleware for session and CORS
$app->add(require_once __DIR__ . "/../app/middlewares/Session.php");
$app->add(require_once __DIR__ . "/../app/middlewares/CORS.php");
$app->addRoutingMiddleware();

# Add routes to app
$routes = require_once __DIR__ . "/../app/Router.php";
$routes($app);

# Run the app
$app->run();