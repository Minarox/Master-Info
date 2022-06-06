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
class PassageController extends Controller
{
    /**
     * Get all passages.
     * Usage: GET /passage
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     *
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     */
    public function getPassages(Request $request, Response $response): Response
    {
        // Fetch all passages from database
        $passages = $this->database()->find(
            "passage",
            order: "seen_at DESC"
        );

        // Display description
        $response->getBody()->write(
            json_encode($passages));
        return $response->withStatus(200);
    }

    /**
     * Get specific passage.
     * Usage: GET /passage/{passage_id}
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     *
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     */
    public function getPassage(Request $request, Response $response, array $args): Response
    {
        // Fetch passage information
        $passage = $this->database()->find(
            "passage",
            ['*'],
            ["passage_id" => $args["passage_id"]],
            true
        );

        // Display description
        $response->getBody()->write(
            json_encode($passage));
        return $response->withStatus(200);
    }

    /**
     * Add passage to database.
     * Usage: POST /passage
     *
     * @param Request $request Slim request interface
     * @param Response $response Slim response interface
     * @return Response Response to show
     *
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     */
    public function addPassage(Request $request, Response $response): Response
    {
        // Insert new passage
        $this->database()->create(
            "passage",
            ["passage_id" => null]
        );

        // Display success code
        return $this->successCode()->created();
    }
}