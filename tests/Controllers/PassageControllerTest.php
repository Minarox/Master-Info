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
class PassageControllerTest extends TestCase
{
    /**
     * @var PassageController $passageController
     */
    private PassageController $passageController;

    /**
     * Construct PassageController for tests
     *
     * @param string|null $name
     * @param array       $data
     * @param string      $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->passageController = new PassageController();
        $GLOBALS["pdo"]          = $this->passageController->database()->getPdo();
    }

    /**
     * Test getPassages function
     * Usage: GET /passage
     *
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     */
    public function testGetPassages()
    {
        // Call function
        $request = $this->createRequest("GET", "/passage");
        $result  = $this->passageController->getPassages($request, $this->response);

        // Check if request = database and http code is correct
        self::assertSame(
            json_encode(
                $GLOBALS["pdo"]
                    ->query("SELECT * FROM passage LIMIT 300;")
                    ->fetchAll()
            ),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test getPassage function
     * Usage: GET /passage/{passage_id}
     *
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     */
    public function testGetPassage()
    {
        // Call function
        $request = $this->createRequest("GET", "/passage/" . $this->passage_id);
        $result  = $this->passageController->getPassage($request, $this->response, ["passage_id" => $this->passage_id]);

        // Check if request = database and http code is correct
        self::assertSame(
            json_encode(
                $GLOBALS["pdo"]
                    ->query("SELECT * FROM passage WHERE passage_id = '$this->passage_id' LIMIT 1;")
                    ->fetch()
            ),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test addPassage function
     * Usage: POST /passage
     *
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     */
    public function testAddPassage()
    {
        // Call function
        $request = $this->createRequest("POST", "/passage");
        $result  = $this->passageController->addPassage($request, $this->response);

        // Check if http code is correct
        $this->assertHTTPCode($result, 201);

        // Check if passage exist in database
        $passage_id = $this->passage_id + 1;
        self::assertTrue(
            (bool) $GLOBALS["pdo"]
                ->query("SELECT passage_id FROM passage WHERE passage_id = '$passage_id' LIMIT 1;")
                ->fetchColumn()
        );

        // Remove new passage
        $GLOBALS["pdo"]
            ->prepare("DELETE FROM food WHERE food_id = '$passage_id';")
            ->execute();
    }
}