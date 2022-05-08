<?php
declare(strict_types=1);

namespace app;

use Codes\ErrorCode;
use Controllers\AdminController;
use Controllers\BaseController;
use Controllers\EmailController;
use Controllers\LogController;
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
    $app->put("/users/delete", [UserController::class, "deleteUsers"]);
    $app->get("/users/{user_id}", [UserController::class, "getUser"]);
    $app->put("/users/{user_id}", [UserController::class, "editUser"]);
    $app->delete("/users/{user_id}", [UserController::class, "deleteUser"]);

    // Email controller
    $app->get("/emails", [EmailController::class, "getEmails"]);
    $app->post("/emails", [EmailController::class, "addEmail"]);
    $app->post("/emails/send", [EmailController::class, "sendEmails"]);
    $app->get("/emails/{email_id}", [EmailController::class, "getEmail"]);
    $app->put("/emails/{email_id}", [EmailController::class, "editEmail"]);
    $app->post("/emails/{email_id}", [EmailController::class, "addTemplateEmail"]);
    $app->delete("/emails/{email_id}", [EmailController::class, "deleteEmail"]);

    // Log controller
    $app->get("/logs", [LogController::class, "getLogs"]);

    /**
     * Redirect to 404 if none of the routes match
     */
    $app->map(["GET", "POST", "PUT", "DELETE", "PATCH"], "/{routes:.+}", function ($request, $response) {
        return (new ErrorCode())->methodNotAllowed();
    });
};