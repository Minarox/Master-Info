<?php
declare(strict_types=1);

namespace app;

use Codes\ErrorCode;
use Controllers\AdminController;
use Controllers\BaseController;
use Controllers\SessionController;
use Controllers\UserController;
use Slim\App;

/**
 * List of route for the app
 * @param App $app
 */
return function (App $app) {
    // CORS Policy
    $app->options("/{routes:.+}", function ($request, $response) {
        return $response;
    });

    // Base controller
    $app->get("/", [BaseController::class, "basePath"]);

    // Session controller
    $app->post("/login", [SessionController::class, "login"]);
    $app->post("/introspect", [SessionController::class, "introspect"]);
    $app->post("/revoke", [SessionController::class, "revoke"]);
    $app->get("/userinfo", [SessionController::class, "userInfo"]);
    $app->put("/userinfo", [SessionController::class, "editUserInfo"]);
    $app->put("/userinfo/password", [SessionController::class, "editPassword"]);
    $app->get("/logout", [SessionController::class, "logout"]);

    // Admin controller
    $app->get("/admins", [AdminController::class, "getAdmins"]);
    $app->post("/admins", [AdminController::class, "addAdmin"]);
    $app->get("/admins/{admin_id}", [AdminController::class, "getAdmin"]);
    $app->put("/admins/{admin_id}", [AdminController::class, "editAdmin"]);
    $app->delete("/admins/{admin_id}", [AdminController::class, "deleteAdmin"]);
    $app->put("/admins/{admin_id}/password", [AdminController::class, "editAdminPassword"]);

    // User controller
    $app->get("/users", [UserController::class, "getUsers"]);
    $app->post("/users", [UserController::class, "addUser"]);
    $app->get("/users/{user_id}", [UserController::class, "getUser"]);
    $app->put("/users/{user_id}", [UserController::class, "editUser"]);
    $app->delete("/users/{user_id}", [UserController::class, "deleteUser"]);

    /**
     * Redirect to 404 if none of the routes match
     */
    $app->map(["GET", "POST", "PUT", "DELETE", "PATCH"], "/{routes:.+}", function ($request, $response) {
        return (new ErrorCode())->methodNotAllowed();
    });
};