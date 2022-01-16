<?php
declare(strict_types=1);

namespace app;

use Codes\ErrorCode;
use Controllers\AdminController;
use Controllers\SessionController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

/**
 * List of route for the app
 * @param App $app
 */
return function (App $app) {
    // CORS Policy
    $app->options("/{routes:.+}", function ($request, $response) {
        return $response;
    });

    $app->post("/login", [SessionController::class, "login"]);
    $app->get("/session", [SessionController::class, "currentSession"]);
    $app->get("/logout", [SessionController::class, "logout"]);

    $app->group("/admin", function (RouteCollectorProxy $group) {
        $group->get("/config", [AdminController::class, "getConfig"]);
        $group->post("/max-users", [AdminController::class, "setMaxUsers"]);
        $group->post("/users-per-group", [AdminController::class, "setUsersPerGroup"]);
        $group->post("/last-group", [AdminController::class, "setLastGroupConfig"]);
        $group->get("/users", [AdminController::class, "getUsers"]);
        $group->delete("/user/{user_id}", [AdminController::class, "deleteUser"]);
    });

    /**
     * Redirect to 404 if none of the routes match
     */
    $app->map(["GET", "POST", "PUT", "DELETE", "PATCH"], "/{routes:.+}", function ($request, $response) {
        return (new ErrorCode())->methodNotAllowed();
    });
};