<?php
declare(strict_types = 1);

namespace Controllers;

require_once __DIR__ . "/../TestCase.php";

use BadRequest;
use NotFound;
use TestCase;

/**
 * Test class for BaseController
 */
class FoodControllerTest extends TestCase
{
    /**
     * @var FoodController $foodController
     */
    private FoodController $foodController;

    /**
     * Construct FoodController for tests
     *
     * @param string|null $name
     * @param array       $data
     * @param string      $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->foodController = new FoodController();
        $GLOBALS["pdo"]       = $this->foodController->database()->getPdo();
    }

    /**
     * Test getFoods function
     * Usage: GET /food
     *
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     */
    public function testGetFoods()
    {
        // Call function
        $request = $this->createRequest("GET", "/food");
        $result  = $this->foodController->getFoods($request, $this->response);

        // Check if request = database and http code is correct
        self::assertSame(
            json_encode(
                $GLOBALS["pdo"]
                    ->query("SELECT * FROM food LIMIT 300;")
                    ->fetchAll()
            ),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test getFood function
     * Usage: GET /food/{food_id}
     *
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     */
    public function testGetFood()
    {
        // Call function
        $request = $this->createRequest("GET", "/food/" . $this->food_id);
        $result  = $this->foodController->getFood($request, $this->response, ["food_id" => $this->food_id]);

        // Check if request = database and http code is correct
        self::assertSame(
            json_encode(
                $GLOBALS["pdo"]
                    ->query("SELECT * FROM food WHERE food_id = '$this->food_id' LIMIT 1;")
                    ->fetch()
            ),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test addFood function
     * Usage: POST /food
     *
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     */
    public function testAddFood()
    {
        // Call function
        $request = $this->createRequest("POST", "/food");
        $result  = $this->foodController->addFood($request, $this->response);

        // Check if http code is correct
        $this->assertHTTPCode($result, 201);

        // Check if food exist in database
        $food_id = $this->food_id + 1;
        self::assertTrue(
            (bool) $GLOBALS["pdo"]
                ->query("SELECT food_id FROM food WHERE food_id = '$food_id' LIMIT 1;")
                ->fetchColumn()
        );

        // Remove new food
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM food WHERE food_id = '$food_id';")
            ->execute();
    }
}