<?php
declare(strict_types = 1);

namespace Controllers;

require_once __DIR__ . "/../TestCase.php";

use BadRequest;
use NotFound;
use TestCase;
use Unauthorized;

/**
 * Test class for StatisticController
 */
class StatisticControllerTest extends TestCase
{
    /**
     * @var StatisticController $statisticController
     */
    private StatisticController $statisticController;

    /**
     * Construct StatisticController for tests
     *
     * @param string|null $name
     * @param array       $data
     * @param string      $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->statisticController = new StatisticController();
        $GLOBALS["pdo"]        = $this->statisticController->database()->getPdo();
    }

    /**
     * Test getGlobalStats function
     * Usage: GET /statistics | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetGlobalStats()
    {
        // Call function
        $request = $this->createRequest("GET", "/statistics");
        $result  = $this->statisticController->getGlobalStats($request, $this->response);

        // Fetch stats in database
        $nb_users = $GLOBALS["pdo"]
            ->query("SELECT count(user_id) FROM users;")
            ->fetchColumn();

        $nb_share = $GLOBALS["pdo"]
            ->query("SELECT sum(nb_share) FROM users;")
            ->fetchColumn();

        $date = date("Y-m-d H:i:s", strtotime('-60 days'));
        $avg_usage_day = $GLOBALS["pdo"]
            ->query("SELECT count(*) FROM users WHERE updated_at >= '$date';")
            ->fetchColumn();
        $avg_usage_day = round(($avg_usage_day / 60), 3);

        $date = date("Y-m-d H:i:s", strtotime('-7 days'));
        $avg_usage_7_days = $GLOBALS["pdo"]
            ->query("SELECT count(*) FROM users WHERE updated_at >= '$date';")
            ->fetchColumn();
        $avg_usage_7_days = round(($avg_usage_7_days / 7), 3);

        $android = $GLOBALS["pdo"]
            ->query("SELECT device FROM users GROUP BY device ORDER BY count(*) DESC LIMIT 1;")
            ->fetchColumn();

        // Check if request = database and http code is correct
        self::assertSame(
            json_encode([
                "nb_users"             => (int) $nb_users,
                "nb_share"             => (int) $nb_share,
                "avg_usage_day"        => $avg_usage_day,
                "avg_usage_7_days"     => $avg_usage_7_days,
                "most_used_android_version" => $android
            ]),
            $result->getBody()->__toString()
        );
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test getGlobalStats function without permission
     * Usage: GET /statistics | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetGlobalStatsWithoutScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "admin";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $request = $this->createRequest("GET", "/statistics");
        $this->statisticController->getGlobalStats($request, $this->response);
    }

    /**
     * Test getAvgUsageMonth function
     * Usage: GET /statistics/avg-usage-month | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetAvgUsageMonth()
    {
        // Call function
        $request = $this->createRequest("GET", "/statistics/avg-usage-month");
        $result  = $this->statisticController->getAvgUsageMonth($request, $this->response);

        // Check if request = database and http code is correct
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test getAvgUsageMonth function without permission
     * Usage: GET /statistics/avg-usage-month | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetAvgUsageMonthWithoutScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "admin";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $request = $this->createRequest("GET", "/statistics/avg-usage-month");
        $this->statisticController->getAvgUsageMonth($request, $this->response);
    }

    /**
     * Test getAvgUsageDay function
     * Usage: GET /statistics/avg-usage-day | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetAvgUsageDay()
    {
        // Call function
        $request = $this->createRequest("GET", "/statistics/avg-usage-day");
        $result  = $this->statisticController->getAvgUsageDay($request, $this->response);

        // Check if request = database and http code is correct
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test getAvgUsageDay function without permission
     * Usage: GET /statistics/avg-usage-day | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetAvgUsageDayWithoutScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "admin";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $request = $this->createRequest("GET", "/statistics/avg-usage-day");
        $this->statisticController->getAvgUsageDay($request, $this->response);
    }

    /**
     * Test getAvgUsageHour function
     * Usage: GET /statistics/avg-usage-hour | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetAvgUsageHour()
    {
        // Call function
        $request = $this->createRequest("GET", "/statistics/avg-usage-hour");
        $result  = $this->statisticController->getAvgUsageHour($request, $this->response);

        // Check if request = database and http code is correct
        self::assertSame(200, $result->getStatusCode());
    }

    /**
     * Test getAvgUsageHour function without permission
     * Usage: GET /statistics/avg-usage-hour | Scope: super_admin
     *
     * @throws NotFound|BadRequest|Unauthorized
     */
    public function testGetAvgUsageHourWithoutScope()
    {
        // Change scope
        $GLOBALS["session"]["scope"] = "admin";

        // Check if exception is thrown
        $this->expectException(Unauthorized::class);
        $this->expectExceptionMessage("User doesn't have the permission");

        // Call function
        $request = $this->createRequest("GET", "/statistics/avg-usage-hour");
        $this->statisticController->getAvgUsageHour($request, $this->response);
    }

}