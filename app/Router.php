<?php
declare(strict_types=1);

namespace app;

use Codes\ErrorCode;
use Controllers\BaseController;
use Controllers\FoodController;
use Controllers\PassageController;
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

    // Passage controller
    $app->get("/passage", [PassageController::class, "getPassages"]);
    $app->post("/passage", [PassageController::class, "addPassage"]);
    $app->get("/passage/{passage_id}", [PassageController::class, "getPassage"]);

    // Food controller
    $app->get("/food", [FoodController::class, "getFoods"]);
    $app->post("/food", [FoodController::class, "addFood"]);
    $app->get("/food/{food_id}", [FoodController::class, "getFood"]);

    /**
     * Redirect to 404 if none of the routes match
     */
    $app->map(["GET", "POST", "PUT", "DELETE", "PATCH"], "/{routes:.+}", function ($request, $response) {
        return (new ErrorCode())->methodNotAllowed();
    });
};