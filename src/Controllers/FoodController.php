<?php
declare(strict_types=1);

namespace Controllers;

use BadRequest;
use Controller;
use NotFound;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * Controller for base path
 */
class FoodController extends Controller
{
    /**
     * Get all foods.
     * Usage: GET /food
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     *
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     */
    public function getFoods(Request $request, Response $response): Response
    {
        // Fetch all passages from database
        $passages = $this->database()->find("food");

        // Display description
        $response->getBody()->write(
            json_encode($passages));
        return $response->withStatus(200);
    }

    /**
     * Get specific food.
     * Usage: GET /food/{food_id}
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     *
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     */
    public function getFood(Request $request, Response $response, array $args): Response
    {
        // Fetch passage information
        $passage = $this->database()->find(
            "food",
            ['*'],
            ["food_id" => $args["food_id"]],
            true
        );

        // Display description
        $response->getBody()->write(
            json_encode($passage));
        return $response->withStatus(200);
    }

    /**
     * Add food to database.
     * Usage: POST /food
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     *
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     */
    public function addFood(Request $request, Response $response): Response
    {
        // Insert new passage
        $this->database()->create(
            "food",
            ["food_id" => null]
        );

        // Display success code
        return $this->successCode()->created();
    }
}