<?php
declare(strict_types=1);

namespace app;

use Codes\ErrorCode;
use Controllers\AdminController;
use Controllers\GroupController;
use Controllers\SessionController;
use NotFound;
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

    $app->map(["GET", "POST", "PUT", "DELETE", "PATCH"], "[/]", function ($request, $response) {
        return (new ErrorCode())->methodNotAllowed();
    });

    $app->post("/login", [SessionController::class, "login"]);
    $app->get("/session", [SessionController::class, "currentSession"]);
    $app->get("/logout", [SessionController::class, "logout"]);

    $app->group("/admin", function (RouteCollectorProxy $group) {
        $group->get("/config", [AdminController::class, "getConfig"]);
        $group->post("/max-users", [AdminController::class, "setMaxUsers"]);
        $group->post("/users-per-group", [AdminController::class, "setUsersPerGroup"]);
        $group->post("/last-group", [AdminController::class, "setLastGroupConfig"]);
        $group->delete("/user/{user_id}", [AdminController::class, "deleteUser"]);
        $group->get("/groups", [AdminController::class, "getGroups"]);
        $group->delete("/group/{group_id}", [AdminController::class, "deleteGroup"]);
    });

    $app->group("/group", function (RouteCollectorProxy $group) {
        $group->get("[/]", [GroupController::class, "getCurrentGroup"]);
        $group->post("[/]", [GroupController::class, "addGroup"]);
        $group->get("/users", [GroupController::class, "getUsers"]);
        $group->get("/leave", [GroupController::class, "leaveCurrentGroup"]);
        $group->get("/random", [GroupController::class, "joinRandomGroup"]);
        $group->get("/join/{group_link}", [GroupController::class, "joinGroup"]);
    });

    /**
     * Redirect to 404 if none of the routes match
     */
    $app->map(["GET", "POST", "PUT", "DELETE", "PATCH"], "/{routes:.+}", function ($request, $response) {
        return (new ErrorCode())->methodNotAllowed();
    });
};